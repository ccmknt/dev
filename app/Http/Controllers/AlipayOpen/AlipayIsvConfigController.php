<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2016/12/18
 * Time: 22:48
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Models\AlipayIsvConfig;
use Illuminate\Http\Request;

class AlipayIsvConfigController
{


    public function isvconfig()
    {
        $c=AlipayIsvConfig::where('id', 1)->first();
        if ($c){
            $c=$c->toArray();
        }
        return view('admin.alipayopen.config.isvconfig',compact('c'));
    }

    public function saveconfig(Request $request)
    {
        $c=AlipayIsvConfig::where('id', 1)->first();
        $data = [
            'id' => 1,
            'alipayrsaPublicKey' => $request->get("alipayrsaPublicKey"),
            'app_id' => $request->get("app_id"),
            'callback' => $request->get("callback"),
            'notify' => $request->get("notify"),
            'pid' => $request->get("pid"),
            'rsaPrivateKey' => $request->get("rsaPrivateKey"),
            'rsaPrivateKeyFilePath' => $request->get("rsaPrivateKeyFilePath"),
            'rsaPublicKeyFilePath' => $request->get("rsaPublicKeyFilePath"),
            'operate_notify_url'=>$request->get("operate_notify_url")
        ];
        if($c){
            AlipayIsvConfig::where('id', 1)->update($data);
        }else{
            AlipayIsvConfig::create($data);
        }
        return json_encode([
            'status' => 1,
        ]);
    }
}