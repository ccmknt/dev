<?php

namespace App\Http\Controllers\AlipayOpen;

use Alipayopen\Sdk\Request\AlipayOpenAuthTokenAppRequest;
use Alipayopen\Sdk\Request\AlipaySystemOauthTokenRequest;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayUser;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Alipayopen\Sdk\AopClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OauthController extends AlipayOpenController
{
    /** 商户授权
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    //应用授权URL拼装
    public function oauth()
    {

        $config = AlipayIsvConfig::where('id', 1)->first();
        if ($config) {
            $config = $config->toArray();
        }
        $url = urlencode($config['callback']);
        $appid = $config['app_id'];
        $app_oauth_url = Config::get('alipayopen.app_oauth_url');
        $code_url = $app_oauth_url . '?app_id=' . $appid . '&redirect_uri=' . $url . "&state=App_" . Auth::user()->id;
        return view('admin.alipayopen.app_auth', compact('code_url'));
    }
    /**商户授权返回函数
     * @param Request $request
     */
//授权回调获取商户信息主要是获取token
    public function callback(Request $request)
    {
        $state = $request->get('state', 'App_6');//个人授权有这个参数商户授权没有这个参数
        $arr = explode('_', $state);
        //第三方应用授权
        if ($arr[0] == "App") {
            //1.初始化参数配置
            $c = $this->AopClient();
            //2.执行相应的接口获得相应的业务
            //获取app_auth_code
            $app_auth_code = $request->get('app_auth_code');
            $promoter_id = $arr[1];
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
                "store_id"=>'o'.$app_response->user_id,
                "app_auth_token" => $app_response->app_auth_token,
                "app_refresh_token" => $app_response->app_refresh_token,
                "expires_in" => $app_response->expires_in,
                "re_expires_in" => $app_response->re_expires_in,
                "auth_app_id" => $app_response->auth_app_id,
                "promoter_id" => $promoter_id,
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
        } //A用户授权跳转收款
        if ($arr[0] == "OSK" || $arr[0] == "SXD" ||$arr[0] == "PA") {
            //SYD_2088402162863826  扫码下单 生成二维码 用户输入金额 完成付款
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
            $request->session()->forget('user_data');
            $request->session()->push('user_data', $re);
            //有门店自带收款码
            if ($type == 'SXD') {
                return redirect(url('admin/alipayopen/alipay_trade_create?u_id=' . $u_id));//跳转到输入金额页面
            }
            //仅生成收款码
            if ($type == 'OSK') {
                return redirect(url('admin/alipayopen/alipay_oqr_create?u_id=' . $u_id));//跳转到输入金额页面
            }
            //跳到平安界面
            if ($type == 'PA') {
                return redirect(url('admin/pingan/alipay?u_id=' . $u_id));//跳转到输入金额页面
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
        $user_id = $request->get('user_id', 1);
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
    public function oauthlist(Request $request)
    {
        $auth = Auth::user()->can('oauthlist');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        //
        $data = DB::table('users')->select('users.name', 'alipay_app_oauth_users.*')->orderBy('updated_at', 'desc')->where('promoter_id', Auth::user()->id)->join('alipay_app_oauth_users', 'alipay_app_oauth_users.promoter_id', '=', 'users.id')->get();
        if (Auth::user()->hasRole('admin')) {
            $data = DB::table('users')->select('users.name', 'alipay_app_oauth_users.*')->orderBy('updated_at', 'desc')->join('alipay_app_oauth_users', 'alipay_app_oauth_users.promoter_id', '=', 'users.id')->get();
        }
        if ($data->isEmpty()) {
            $paginator = "";
            $datapage = "";
        } else {
            $data = $data->toArray();
            //下一版本去掉
            foreach ($data as $v) {
                AlipayAppOauthUsers::where('user_id',$v->user_id)->update([
                    'store_id'=>'o'.$v->user_id
                ]);
            }
            //下一版本去掉结束
            //非数据库模型自定义分页
            $perPage = 9;//每页数量
            if ($request->has('page')) {
                $current_page = $request->input('page');
                $current_page = $current_page <= 0 ? 1 : $current_page;
            } else {
                $current_page = 1;
            }
            $item = array_slice($data, ($current_page - 1) * $perPage, $perPage); //注释1
            $total = count($data);
            $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
            $datapage = $paginator->toArray()['data'];
        }
        return view('admin.alipayopen.store.oauthlist', compact('datapage', 'paginator'));
    }

    //修改信息
    public function updateOauthUser(Request $request)
    {
        $id = $request->get('id');
        $store = AlipayAppOauthUsers::where('id', $id)->first()->toArray();
        return view('admin.alipayopen.config.updateOauthUser', compact('store'));

    }

    public function updateOauthUserPost(Request $request)
    {
        $data = $request->except(['_token', 'id']);
        try {
            AlipayAppOauthUsers::where('id', $request->get('id'))->update($data);
        } catch (\Exception $exception) {
            return json_encode([
                'status' => 0,
            ]);
        }
        return json_encode([
            'status' => 1,
        ]);
    }

}
