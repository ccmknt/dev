<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use EasyWeChat\Foundation\Application;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use EasyWeChat\Payment\Order;
use Illuminate\Support\Facades\Cache;

class WeixinPayController extends Controller
{
    //订单金额页面
    public function orderview()
    {
        return view('admin.weixin.orderview');
    }
  //输入金额付款
    public function order()
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
            'trade_type' => 'JSAPI', // JSAPI，NATIVE，APP...
            "openid"=>Cache::get("open_id"),
            'body' => '手机支付',
            'detail' => 'iPad mini 16G 白色',
            'out_trade_no' => date('Ymdhis', time()) . '8888' . date('Ymdhis', time()),//订单号
            'total_fee' => 50,
            'notify_url' => url('/admin/weixin/ordernotify'), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            // ...
        ];
        $order = new Order($attributes);

        $result = $payment->prepare($order);
        dd($result);
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
    public function ordernotify()
    {
        echo 'ordernotify';
    }
}
