<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2016/12/21
 * Time: 11:23
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayIsvConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AlipayQrController extends AlipayOpenController
{


    public function Skm(Request $request)
    {
        $u_id = $request->get('id');//这个是系统商户列表的id
        $config = AlipayIsvConfig::where('id', 1)->first();
        if ($config) {
            $config = $config->toArray();
        }
        $config['app_auth_url'] = Config::get('alipayopen.app_auth_url');

        $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=SXD_' . $u_id;
        return view('admin.alipayopen.skm', compact('code_url'));


    }

    //仅生成收款
    public function OnlySkm(Request $request)
    {
        $user_id = $request->get('user_id');//授权的user_id
        $config = AlipayIsvConfig::where('id', 1)->first();
        if ($config) {
            $config = $config->toArray();
        }
        $usersInfo = AlipayAppOauthUsers::where('user_id', $user_id)->first();
        if ($usersInfo) {
            $auth_shop_name = $usersInfo->toArray()['auth_shop_name'];
        } else {
            $auth_shop_name = "无效商户二维码";
        }
        $config['app_auth_url'] = Config::get('alipayopen.app_auth_url');
        $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=OSK_' . $user_id;
        return view('admin.alipayopen.skm', compact('code_url', 'auth_shop_name'));

    }
}