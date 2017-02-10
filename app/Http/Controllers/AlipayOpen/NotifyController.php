<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/17
 * Time: 16:09
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Models\AlipayIsvConfig;
use App\Models\AlipayShopLists;
use App\Models\AlipayStoreInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotifyController extends AlipayOpenController
{

    public function notify(Request $request)
    {
        Log::info($request);
    }

    //商户开店状态通知URL
    public function operate_notify_url(Request $request)
    {
        /*$request=array (
            'is_online' => 'T',
            'biz_type' => 'CREATE_SHOP_AUDIT',
            'notify_time' => '2017-02-04 16:42:11',
            'shop_id' => '2017020400077000000000058537',
            'sign_type' => 'RSA',
            'notify_type' => 'shop_audit_result',
            'apply_id' => '2017020400107000000000090975',
            'version' => '2.0',
            'sign' => 'lQrTCugG0iibEfoaNyBPtr8VXSZGznfKUBmaV769b2SskvIJP83nBJ8whMwLKD5i4rWE9ux+u1ANzaEx5aMLxuDcbr143l41QSwADwMoqWEbD5TxY5NDCOD8biLjqtYhpXFJlOG+RDqtWV8ExnmfN8OSwawsteKh4mu+sk1b7h4=',
            'is_show' => 'T',
            'request_id' => '20170204163910',
            'notify_id' => '98408d09b1f2d0c065ecdf828a43b24mhe',
            'audit_status' => 'AUDIT_SUCCESS',
        );*/
        //验签
        $request=$request->toArray();
        $config = AlipayIsvConfig::where('id', 1)->first();
        $alipayrsaPublicKey = $config->alipayrsaPublicKey;
        $aop = $this->AopClient();
        $aop->alipayrsaPublicKey=$alipayrsaPublicKey;
        $umxnt = $aop->rsaCheckV1($request, $alipayrsaPublicKey);
        if ($umxnt) {
            $data = [
                'shop_id' => $request['shop_id'],
                'audit_status' => $request['audit_status']

            ];
            AlipayShopLists::where('apply_id', $request['apply_id'])->update($data);
            $store = AlipayShopLists::where('apply_id', $request['apply_id'])->first();
            $storeInfo = AlipayStoreInfo::where('store_id', $store->store_id)->first();
            $dataInfo = [
                'store_id' => $store->store_id,
                'biz_type' => $request['biz_type'],
                'notify_time' => $request['notify_time'],
                'shop_id' => $request['shop_id'],
                'apply_id' => $request['apply_id'],
                'is_show' => $request['is_show'],
                'request_id' => $request['request_id'],
                'audit_status' => $request['audit_status'],
            ];
            if($request['shop_id']==""){
                $dataInfo['result_code'] = $request['result_code'];
            }
            if ($storeInfo) {
                AlipayStoreInfo::where('store_id', $store->store_id)->update($dataInfo);
            } else {
                AlipayStoreInfo::create($dataInfo);
            }
        }else{
            dd('失败！不是支付宝请求');
        }
    }
}