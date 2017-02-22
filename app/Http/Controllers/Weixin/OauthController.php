<?php

namespace App\Http\Controllers\Weixin;

use App\Models\WeixinPayConfig;
use Illuminate\Http\Request;
use EasyWeChat\Foundation\Application;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class OauthController extends BaseController
{
    //授权
    public function oauth(Request $request)
    {
        $sub_info = $request->get('sub_info');
        $arr = explode('_', $sub_info);
        if ($arr[0] == 'pay'||$arr[0] == 'PPay') {
            $options = $this->Options();//基础配置
            $config = [
                'app_id' => $options['app_id'],
                'scope' => 'snsapi_base',
                'oauth' => [
                    'scopes' => ['snsapi_base'],
                    'response_type' => 'code',
                    'callback' => url('admin/weixin/oauth_callback?sub_info='.$sub_info),
                ],

            ];
            $app = new Application($config);
          /*  $response = $app->oauth->scopes(['snsapi_base'])
                ->setRequest($request)
                ->redirect();*/

            $response = $app->oauth->redirect();
//回调后获取user时也要设置$request对象
//$user = $app->oauth->setRequest($request)->user();
        }

        return $response;
    }

    public function oauth_callback(Request $request)
    {
        $sub_info = $request->get('sub_info');
        $arr = explode('_', $sub_info);
        $code = $request->input('code');
        $wxConfig = WeixinPayConfig::where('id', 1)->first();
        $config = [
            'app_id' => $wxConfig->app_id,
            "secret" => $wxConfig->secret,
            "code" => $code,
            "grant_type" => "authorization_code",
        ];
        $app = new Application($config);
        $oauth = $app->oauth;

        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();
        $userarray = $user->toArray();
        $request->session()->forget('wx_user_data');
        $request->session()->push('wx_user_data', $userarray);
        if ($arr[0] == 'pay') {
            header('location:' . url("admin/weixin/orderview?sub_merchant_id=" . $arr[1])); // 跳转到 user/profile*/
        }
        if ($arr[0] == 'PPay') {
            header('location:' . url("admin/pingan/weixin/orderview?sub_merchant_id=" . $arr[1])); // 跳转到 user/profile*/
        }
        //跳转到订单付款页面
        /* $_SESSION['wechat_user'] = $user->toArray();
         $targetUrl = empty($_SESSION['target_url']) ? '/' : $_SESSION['target_url'];
         header('location:' . $targetUrl); // 跳转到 user/profile*/
    }
}
