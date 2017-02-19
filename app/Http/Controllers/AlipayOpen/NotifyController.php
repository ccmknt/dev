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
use App\Models\AlipayTradeQuery;
use App\Models\PageSets;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class NotifyController extends AlipayOpenController
{

    public function notify(Request $request)
    {
        //支付异步通知
        $config = AlipayIsvConfig::where('id', 1)->first();
        $alipayrsaPublicKey = $config->alipayrsaPublicKey;
        $aop = $this->AopClientNotify();
        $aop->alipayrsaPublicKey = $alipayrsaPublicKey;
        $umxnt = $aop->rsaCheckUmxnt($request->all(), $alipayrsaPublicKey);
        if ($umxnt) {
            $data = $request->all();
            Log::info($data);
            $AlipayTradeQuery = AlipayTradeQuery::where('trade_no', $data['trade_no'])->first();
            //如果状态不相同修改数据库状态
            if ($AlipayTradeQuery->status != $data['trade_status']) {
                AlipayTradeQuery::where('trade_no', $data['trade_no'])->update([
                    'status' => $data['trade_status'],
                    'total_amount' => $data['total_amount'],
                ]);

                //微信通知商户收营员
                try {
                    //店铺通知微信
                    if ($data['trade_status'] == 'TRADE_SUCCESS') {
                        $store_id = $AlipayTradeQuery->store_id;
                        $WeixinPayNotifyStore = WeixinPayNotify::where('store_id', $store_id)->first();
                        //实例化
                        $config = WeixinPayConfig::where('id', 1)->first();
                        $options = [
                            'app_id' => $config->app_id,
                            'secret' => $config->secret,
                            'token' => '18851186776',
                            'payment' => [
                                'merchant_id' => $config->merchant_id,
                                'key' => $config->key,
                                'cert_path' => $config->cert_path, // XXX: 绝对路径！！！！
                                'key_path' => $config->key_path,      // XXX: 绝对路径！！！！
                                'notify_url' => $config->notify_url,       // 你也可以在下单时单独设置来想覆盖它
                            ],
                        ];
                        $app = new Application($options);
                        $broadcast = $app->broadcast;//群发
                        $userService = $app->user;
                        $open_ids = $userService->lists()->data['openid'];//获得所有关注的微信openid
                        /*  foreach ($open_ids as $v) {
                          $userinfo[]=$userService->get($v);

                          }*/

                        $template = PageSets::where('id', 1)->first();
                        $notice = $app->notice;
                        $userIds = $WeixinPayNotifyStore->receiver;
                        $open_ids = explode(",", $userIds);
                        $templateId = $template->string1;
                        $url = $WeixinPayNotifyStore->linkTo;
                        $color = $WeixinPayNotifyStore->topColor;
                        $data = array(
                            "keyword1" => $AlipayTradeQuery->total_amount,
                            "keyword2" => '支付宝(' . $data['buyer_logon_id'] . ')',
                            "keyword3" => '' . $AlipayTradeQuery->updated_at . '',
                            "keyword4" => $data['trade_no'],
                            "remark" => '祝' . $WeixinPayNotifyStore->store_name . '生意红火',
                        );
                        foreach ($open_ids as $v) {
                            $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($v)->send();
                        }

                    }

                } catch (\Exception $exception) {
                    Log::info($exception);
                    return json_encode([
                        'status' => 1,
                    ]);
                }
            }
        }
    }

    public function alipay_notify(Request $request)
    {
        Log::info('...' . $request);
    }

    //商户开店状态通知URL
    public function operate_notify_url(Request $request)
    {
        Log::info($request);
        $requestArray = $request->toArray();
        $config = AlipayIsvConfig::where('id', 1)->first();
        $alipayrsaPublicKey = $config->alipayrsaPublicKey;
        $aop = $this->AopClientNotify();
        $aop->alipayrsaPublicKey = $alipayrsaPublicKey;
        $umxnt = $aop->rsaCheckUmxnt($requestArray, $alipayrsaPublicKey);
        Log::info('_________' . $umxnt);
        if ($umxnt) {
            $data = [
                'shop_id' => $request->get('shop_id', ''),
                'audit_status' => $request->get('audit_status'),

            ];
            AlipayShopLists::where('apply_id', $request->get('apply_id'))->update($data);
            $store = AlipayShopLists::where('apply_id', $request->get('apply_id'))->first();
            $storeInfo = AlipayStoreInfo::where('store_id', $store->store_id)->first();
            $dataInfo = [
                'store_id' => $store->store_id,
                'biz_type' => $request->get('biz_type', ''),
                'notify_time' => $request->get('notify_time', ''),
                'shop_id' => $request->get('shop_id', ''),
                'apply_id' => $request->get('apply_id', ''),
                'is_show' => $request->get('is_show', ''),
                'request_id' => $request->get('request_id', ''),
                'audit_status' => $request->get('audit_status', ''),
            ];
            if ($request->get('result_code', '')) {
                $dataInfo['result_code'] = $request->get('result_code', '');
                $dataInfo['result_desc'] = $request->get('result_desc', '');
            }
            if ($storeInfo) {
                AlipayStoreInfo::where('store_id', $store->store_id)->update($dataInfo);
            } else {
                AlipayStoreInfo::create($dataInfo);
            }
        } else {
            dd('失败！不是支付宝请求');
        }
    }
}

//通过的返回提醒
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

//不通过的返回提醒
/*array (
    'is_online' => 'F',
    'biz_type' => 'CREATE_SHOP_AUDIT',
    'notify_time' => '2017-02-13 13:23:16',
    'sign_type' => 'RSA2',
    'notify_type' => 'shop_audit_result',
    'apply_id' => '2017021300107000000027889581',
    'version' => '2.0',
    'result_code' => 'RISK_AUDIT_FAIL',
    'sign' => 'QLQC/U7ACBT5g512PzIyeg9t19XLHUmojJuaMpXqbweXCg4HhZoJiI5Jg+yBOIJ99vU5imKCUDza+GcS8SKGSNeL9MI0ZkqLoEk7lIHwsHqqWM0+XbE61S0lNKdJkVY8Unhn7ylCBxFbUYT0Lgwcnv1UhFtHHiVA/1gt5wopyTZQP1UUUX/71ve13x7mfypZ8toGg34gOu2qFIyGuJt7tIgj61Qt2Euc84NEhEZz9kyEn5QeK/gNn5gZsm93QDHF40OMBRB0wVepCwXJS+MXH/v0Clsj5iyEPu1EDCR4SronQ59jqfueo0Wm5V2Tc6wgvyJVo85nCgP94gao78XMXg==',
    'result_desc' => '您提交的证照图片不清晰，无法辨别证照信息，请重新拍摄并提供清晰的证照图片（营业执照不清晰）;',
    'is_show' => 'F',
    'request_id' => '20170213112044',
    'notify_id' => 'c009530cd81267ec37df445d757e60bjh2',
    'audit_status' => 'AUDIT_FAILED',
)*/