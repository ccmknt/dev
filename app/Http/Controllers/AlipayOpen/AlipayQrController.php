<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2016/12/21
 * Time: 11:23
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Models\AlipayIsvConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AlipayQrController extends AlipayOpenController
{


    public function Skm(Request $request)
    {
        $u_id=$request->get('id');//这个是系统商户列表的id
        $config=AlipayIsvConfig::where('id',1)->first();
        if ($config){
            $config=$config->toArray();
        }
        $config['app_auth_url']= Config::get('alipayopen.app_auth_url');

        $code_url=$config['app_auth_url'].'?app_id='.$config['app_id']."&redirect_uri=".$config['callback'].'&scope=auth_base&state=SXD_'.$u_id;
        return view('admin.alipayopen.skm',compact('code_url'));


    }
}