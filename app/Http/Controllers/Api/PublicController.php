<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/7
 * Time: 17:30
 */

namespace App\Http\Controllers\Api;


use Alipayopen\Sdk\Request\AlipayOfflineMaterialImageUploadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class PublicController extends BaseController
{

    //图片上传
    public function upload(Request $request)
    {
        $file = Input::file('image');
        $store_id = $request->get('store_id', 'default');
        $app_auth_token = $request->get('app_auth_token');
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
            $path = $file->move(public_path() . '/uploads/shop/' . $store_id . '/', $newName);

        }
        //上传至支付宝
        $aop = $this->AopClient();
        $aop->apiVersion = '2.0';
        $aop->method = "alipay.offline.material.image.upload";
        $aop->app_auth_token = $app_auth_token;
        $requests = new AlipayOfflineMaterialImageUploadRequest();
        $requests->setImageType($entension);
        $requests->setImageName($newName);
        $requests->setImageContent('@' . $path);
        $result = $aop->execute($requests);
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            $data = [
                'image_id' => $result->$responseNode->image_id,
                'image_url' => url('/uploads/shop/' . $store_id . '/' . $newName),
            ];

            return json_encode($data);

        } else {
            return "上传失败";
        }
    }

    //图片上传
    public function uploadfile(Request $request)
    {
        $file = Input::file('file');
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
            $path = $file->move(public_path() . '/uploads/', $newName);

        }
        $data = [
            'path' => public_path() . '/uploads/' . $newName,
            'status' => 1,
        ];

        return json_encode($data);

    }

    public function uploadlocal(Request $request)
    {
     $store_id=$request->get('store_id');
     $file = Input::file('image');
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
            $path = $file->move(public_path() . '/uploads/'.$store_id.'/', $newName);

        }
        $data = [
            'image_url' => url('/uploads/' .$store_id.'/'. $newName),
            'status' => 1,
        ];

        return json_encode($data);
    }
}
