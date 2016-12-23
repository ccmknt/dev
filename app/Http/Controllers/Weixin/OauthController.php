<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use EasyWeChat\Foundation\Application;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class OauthController extends Controller
{
    //授权
    public function oauth(Request $request)
    {
        $config = [
            'app_id' => 'wx99dd6fe83cd87924',
            'scope' => 'snsapi_base',
            'oauth' => [
                'scopes' => ['snsapi_base'],
                'response_type' => 'code',
                'callback' => url('admin/weixin/oauth_callback'),
                'state' => '#wechat_redirect'
            ],
        ];
        $app = new Application($config);
        $response = $app->oauth->scopes(['snsapi_userinfo'])
            ->setRequest($request)
            ->redirect();
//回调后获取user时也要设置$request对象
//$user = $app->oauth->setRequest($request)->user();
        return $response;
    }

    public function oauth_callback(Request $request)
    {

        $code = $request->input('code');
        $config = [
            'app_id' => 'wx99dd6fe83cd87924',
            "secret" => "ff48da35c0b54104396f43fff6c63d39",
            "code" => $code,
            "grant_type" => "authorization_code",
        ];
        $app = new Application($config);
        $oauth = $app->oauth;
        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();
        $userarray=$user->toArray();
        Cache::put('open_id', $userarray['id'], 10);//缓存用户信息
        //跳转到订单付款页面

        header('location:' . url("admin/weixin/orderview")); // 跳转到 user/profile*/
        /* $_SESSION['wechat_user'] = $user->toArray();
         $targetUrl = empty($_SESSION['target_url']) ? '/' : $_SESSION['target_url'];
         header('location:' . $targetUrl); // 跳转到 user/profile*/
    }
}
