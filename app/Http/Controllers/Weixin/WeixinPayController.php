<?php

namespace App\Http\Controllers\Weixin;

use App\Models\PageSets;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use App\Models\WeixinShopList;
use App\Models\WxPayOrder;
use Illuminate\Http\Request;
use EasyWeChat\Foundation\Application;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use EasyWeChat\Payment\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeixinPayController extends BaseController
{
    //订单金额页面
    public function orderview(Request $request)
    {
        $sub_merchant_id = $request->get('sub_merchant_id');//子商户号
        $shop = WeixinShopList::where('mch_id', $sub_merchant_id)->first();
        return view('admin.weixin.orderview', compact('sub_merchant_id', 'shop'));
    }

    //输入金额付款
    public function order(Request $request)
    {
        $wx_user_data = $request->session()->get('wx_user_data');
        $sub_merchant_id = $request->get('sub_merchant_id');
        $options = $this->Options();
        $options['payment']['sub_merchant_id'] = $sub_merchant_id;
        $shop = WeixinShopList::where('mch_id', $sub_merchant_id)->first();
        $out_trade_no = date('Ymdhis', time()) . '8888' . date('Ymdhis', time());//订单号
        $total_fee = (int)($request->get('total_fee') * 100);//金额
        $app = new Application($options);
        $payment = $app->payment;
        $attributes = [
            'trade_type' => 'JSAPI', // JSAPI，NATIVE，APP...
            'body' => $shop->store_name . '商家收款',
            'detail' => $shop->store_name . '商家收款',
            'out_trade_no' => $out_trade_no,
            'total_fee' => $total_fee,
            'notify_url' => url('/admin/weixin/ordernotify'), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid' => $wx_user_data[0]['id']
            // ...
        ];
        $order = new Order($attributes);
        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
            $prepayId = $result->prepay_id;
            $data = [
                'mch_id' => $sub_merchant_id,
                'out_trade_no' => $out_trade_no,
                'transaction_id' => '',
                'total_fee' => $request->get('total_fee'),
                'open_id' => $wx_user_data[0]['id'],
                'status' => '',
            ];
            WxPayOrder::create($data);
        }
        $json = $payment->configForPayment($prepayId);
        return $json;
    }

    //生成二维码付款
    public function createOrder()
    {

        $options = [
            // 前面的appid什么的也得保留哦
            'app_id' => 'wx789fb035be0b7481',
            // ...
            // payment
            'payment' => [
                'merchant_id' => '1273479101',
                'key' => 'dasdawdarwfesczzcaSADwrr3434fsfa',
                'cert_path' => app_path() . '/lib/cert/apiclient_cert.pem',// XXX: 绝对路径！！！！
                'key_path' => app_path() . '/lib/cert/apiclient_key.pem',// XXX: 绝对路径！！！！
                'notify_url' => url('/admin/weixin/ordernotify'),       // 你也可以在下单时单独设置来想覆盖它
                'device_info' => '013467007045764',
                //'sub_app_id'      => '',
                'sub_merchant_id' => '1405994502',
            ],
        ];
        $app = new Application($options);
        $payment = $app->payment;


        $attributes = [
            'trade_type' => 'NATIVE', // JSAPI，NATIVE，APP...
            'body' => '手机支付',
            'detail' => 'iPad mini 16G 白色',
            'out_trade_no' => date('Ymdhis', time()) . '8888' . date('Ymdhis', time()),//订单号
            'total_fee' => 50,
            'notify_url' => url('/admin/weixin/ordernotify'), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            // ...
        ];
        $order = new Order($attributes);

        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
            $prepayId = $result->prepay_id;
        }
        /*打印 $result
         * "return_code" => "SUCCESS"
    "return_msg" => "OK"
    "appid" => "wx789fb035be0b7481"
    "mch_id" => "1273479101"
    "sub_mch_id" => "1405994502"
    "device_info" => "013467007045764"
    "nonce_str" => "JCIGTp69E6U4zQ39"
    "sign" => "CC8EDF6A26FFB6EADA1D2849ACE92CC6"
    "result_code" => "SUCCESS"
    "prepay_id" => "wx20161030202137d82a8067b60501474833"
    "trade_type" => "NATIVE"
    "code_url" => "weixin://wxpay/bizpayurl?pr=GapY1jk"
         *
         */
        $code_url = $result->code_url;//获得二维码url

        return view('admin.weixin.createorder', compact('code_url'));
    }

    //支付结果通知网址
    public function ordernotify(Request $request)
    {
        $options = $this->Options();
        $app = new Application($options);
        $response = $app->payment->handleNotify(function ($notify, $successful) {
            $out_trade_no = $notify->out_trade_no;//订单号
            $result_code = $notify->result_code;
            // 你的逻辑
            try {
                $WxPayOrder = WxPayOrder::where('out_trade_no', $out_trade_no)->first();
                if ($WxPayOrder) {
                    if ($WxPayOrder->status != $result_code) {
                        WxPayOrder::where('out_trade_no', $out_trade_no)->update([
                            'status' => $result_code
                        ]);

                        if ($result_code == "SUCCESS") {
                            try {
                                //设置成功提醒微信收款
                                $store_id = 'w' . $WxPayOrder->mch_id;//微信店铺id;
                                $WeixinPayNotifyStore = WeixinPayNotify::where('store_id', $store_id)->first();
                                //实例化
                                $config = WeixinPayConfig::where('id', 1)->first();//微信支付参数 app_id
                                $optionsNotify = [
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
                                $app = new Application($optionsNotify);
                                $userService = $app->user;
                                $template = PageSets::where('id', 1)->first();
                                $notice = $app->notice;
                                $userIds = $WeixinPayNotifyStore->receiver;
                                $open_ids = explode(",", $userIds);
                                $templateId = $template->string1;
                                $url = $WeixinPayNotifyStore->linkTo;
                                $color = $WeixinPayNotifyStore->topColor;
                                $user = $userService->get($WxPayOrder->open_id);//买家open_id
                                $data = array(
                                    "keyword1" => $WxPayOrder->total_fee,
                                    "keyword2" => '微信支付(' . $user->nickname . ')',
                                    "keyword3" => '' . $WxPayOrder->updated_at . '',
                                    "keyword4" => $out_trade_no,
                                    "remark" => '祝' . $WeixinPayNotifyStore->store_name . '生意红火',
                                );
                                foreach ($open_ids as $v) {
                                    $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($v)->send();
                                }


                            } catch (\Exception $exception) {
                                return false;
                            }
                        }
                    }
                }
            } catch (\Exception $exception) {
                Log::info($exception);
                return false;
            }
            return true; // 或者错误消息
        });
        return $response;
        /*$notify
         * {"appid":"wx789fb035be0b7481","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"Y","mch_id":"1273479101","nonce_str":"58a939461279b","openid":"opnT0s8Pltziuu2qATK3o8bKAWbA","out_trade_no":"20170219022054888820170219022054","result_code":"SUCCESS","return_code":"SUCCESS","sign":"75D69190E5D930EED252E43A83F457BC","sub_mch_id":"1419589702","time_end":"20170219142057","total_fee":"1","trade_type":"JSAPI","transaction_id":"4003762001201702190519905709"} */
    }

    public function paySuccess()
    {
        return view('admin.weixin.success');
    }

}
