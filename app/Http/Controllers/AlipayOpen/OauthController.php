<?php

namespace App\Http\Controllers\AlipayOpen;

use Alipayopen\Sdk\Request\AlipayOpenAuthTokenAppRequest;
use Alipayopen\Sdk\Request\AlipaySystemOauthTokenRequest;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayUser;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Alipayopen\Sdk\AopClient;
use Illuminate\Support\Facades\Session;

class OauthController extends AlipayOpenController
{
    /** 商户授权
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    //应用授权URL拼装
    public function oauth()
    {
        $config=AlipayIsvConfig::where('id',1)->first();
        if ($config){
            $config=$config->toArray();
        }
        $url = urlencode($config['callback']);
        $appid = $config['app_id'];
        $app_oauth_url = Config::get('alipayopen.app_oauth_url');
        $code_url = $app_oauth_url . '?app_id=' . $appid . '&redirect_uri=' . $url;
        return view('layouts.qr', compact('code_url'));
    }
    /**商户授权返回函数
     * @param Request $request
     */
//授权回调获取商户信息主要是获取token
    public function callback(Request $request)
    {
        $state = $request->get('state');//个人授权有这个参数商户授权没有这个参数
        //A用户授权
        if ($state) {
            //SYD_2088402162863826  扫码下单 生成二维码 用户输入金额 完成付款
            $arr = explode('_', $state);
            $type = $arr[0];
            $u_id = $arr[1];
            //1.初始化参数配置
            $c = $this->AopClient();
            //2.执行相应的接口获得相应的业务
            //获取app_auth_code
            $app_auth_code = $request->get('auth_code');
            //使用app_auth_code换取接口access_token及用户userId
            $obj = new AlipaySystemOauthTokenRequest();
            $obj->setApiVersion('2.0');
            $obj->setCode($app_auth_code);
            $obj->setGrantType("authorization_code");
            try {
                $data = $c->execute($obj);
                $re = $data->alipay_system_oauth_token_response;

            } catch (\Exception $exception) {
                return redirect('https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=2016120503886618&redirect_uri=http%3A%2F%2Fisv.cmkcms.com%2Fcallback&scope=auth_base&state=' . $state);//重新跳转授权
            }
            $request->session()->push('user_data', $re);
            if ($type == 'SXD') {
                return redirect(url('admin/alipayopen/alipay_trade_create?u_id='.$u_id));//跳转到输入金额页面
            }
            /*
             *  dd($re);
            +"access_token": "composeB758d0ffce2eb4f029d7a1b421b2e4X04"
            +"alipay_user_id": "2088102168684040"
            +"expires_in": 500
            +"re_expires_in": 300
            +"refresh_token": "composeB5ae6765a63b648a1b389aaf72cf9dX04"
            +"user_id": "2088102168684040"
            */
        } //B第三方应用授权
        else {
            //1.初始化参数配置
            $c = $this->AopClient();
            //2.执行相应的接口获得相应的业务
            //获取app_auth_code
            $app_auth_code = $request->get('app_auth_code');
            //使用app_auth_code换取app_auth_token
            $obj = new AlipayOpenAuthTokenAppRequest();
            $obj->setApiVersion('2.0');
            $obj->setBizContent("{" .
                "    \"grant_type\":\"authorization_code\"," .
                "    \"code\":\"$app_auth_code\"," .
                "  }");
            try {
                $data = $c->execute($obj);
                $app_response = $data->alipay_open_auth_token_app_response;
            } catch (\Exception $exception) {
                return redirect('/admin/alipayopen/oauth');
            }
            $model = [
                "user_id" => $app_response->user_id,
                "app_auth_token" => $app_response->app_auth_token,
                "app_refresh_token" => $app_response->app_refresh_token,
                "expires_in" => $app_response->expires_in,
                "re_expires_in" => $app_response->re_expires_in,
                "auth_app_id" => $app_response->auth_app_id,
                "auth_shop_name" => "",
                "auth_phone" => "",
            ];
            $alipay_user = AlipayAppOauthUsers::where('user_id', $app_response->user_id)->first();//如果存在修改信息
            if ($alipay_user) {
                $re = AlipayAppOauthUsers::where('user_id', $app_response->user_id)
                    ->update($model);
            } else {
                $re = AlipayAppOauthUsers::create($model);//新增信息
            }
            //Cache::put('key', 'value', '527040');//一年
            //这里拿到商户信息如下 auth_token 有效期1年
            //  +"code": "10000"
            // +"msg": "Success"
            // +"app_auth_token": "201610BB7bae5f482d3042b58926dcb331b80X20"
            // +"app_refresh_token": "201610BB206dad017d0049218f89418fb048eX20"
            //  +"auth_app_id": "2016072800112318"
            //  +"expires_in": 31536000
            // +"re_expires_in": 32140800
            //  +"user_id": "2088102168897200"
            return redirect("/alipayopen/userinfo?user_id=" . $app_response->user_id);
        }
    }

    /*
     * 个人用户授权 跳转支付界面
     */
    public function auth()
    {
        $url = urlencode(Config::get('alipayopen.redirect_uri'));
        $appid = Config::get('alipayopen.app_id');
        $app_auth_url = Config::get('alipayopen.app_auth_url');
        $code_url = $app_auth_url . '?app_id=' . $appid . '&redirect_uri=' . $url . "&scope=auth_base" . '&state=SXD_2088402162863826';
        return view('layouts.qr', compact('code_url'));
    }

    public function userinfo()
    {
        return view('admin.alipayopen.store.userinfo');
    }

    public function userinfoinsert(Request $request)
    {
        $user_id = $request->get('user_id');
        $auth_shop_name = $request->get('auth_shop_name');
        $auth_phone = $request->get('auth_phone');
        if ($user_id) {
            if ($auth_shop_name || $auth_phone) {
                $update = [
                    'auth_shop_name' => $auth_shop_name,
                    'auth_phone' => $auth_phone,
                ];
                try {
                    $user = AlipayAppOauthUsers::where('user_id', $user_id)->update($update);

                } catch (\Exception $exception) {
                    echo '出错了！请联系客服';
                }
                return json_encode(['code' => 200, 'msg' => "添加成功"]);
            }
        } else {
            return redirect('/admin/alipayopen/oauth');//重新跳转授权
        }
    }

    //商家第三方应用授权列表
    public function oauthlist()
    {
        $data = AlipayAppOauthUsers::all()->toArray();
        return view('admin.alipayopen.store.oauthlist', compact('data'));
    }

    public function getUserAuth()
    {

    }
}
