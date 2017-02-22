<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/20
 * Time: 16:02
 */

namespace App\Http\Controllers\PingAn;


use App\Models\PinganStore;
use App\Models\PinganTradeQueries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AlipayController extends BaseController
{

    public function alipay(Request $request)
    {
        $merchant_id = $request->get('u_id');//商户号
        $shop = PinganStore::where('sub_merchant_id', $merchant_id)->first();
        $shop['main_shop_name'] = $shop->alias_name;
        return view('admin.pingan.alipay.create', compact('shop'));

    }

    public function PingAnAlipay(Request $request)
    {
        $total_amount = $request->get('total_amount');
        $u_id = $request->get('u_id');
        $store=PinganStore::where('sub_merchant_id',$u_id)->first();
        $user = $request->session()->get('user_data');//买家信息
        $aop = $this->AopClient();
        $aop->method = "fshows.liquidation.submerchant.alipay.trade.create";
        $pay = [
            'out_trade_no' => 'pa' . date('YmdHis', time()) . rand(10000, 99999),
            'notify_url' => url('/admin/pingan/notify_url'),
            'total_amount' => $total_amount,
            'subject' => $store->alias_name.'门店收款',
            'body' => $store->alias_name.'门店收款信息',
            'sub_merchant' => [
                'merchant_id' => $u_id
            ],
            'buyer_id' => $user[0]->user_id
        ];
        $data = array('content' => json_encode($pay));
        try {
            $response = $aop->execute($data);
            $responseArray = json_decode($response, true);
            /* array (
                 'return_value' =>
                     array (
                         'out_trade_no' => 'p2017022018354860591',
                         'trade_no' => '2017022018354802846363959867',
                         'prepay_id' => '2017022021001004660288105594',
                     ),
                 'success' => true,
             );*/
            //保存数据库
            if ($responseArray['success']) {
                $insert = [
                    'trade_no' => $responseArray['return_value']['trade_no'],
                    "out_trade_no" => $responseArray['return_value']['out_trade_no'],
                    "status" => "",
                    "type"=>"alipay",
                    "total_amount" => $total_amount,
                    'store_id' => $store->external_id,
                ];
                PinganTradeQueries::create($insert);
            }

        } catch (\Exception $exception) {
            Log::info($exception);

        }

        return $response;
    }

    public function ReturnStatus(Request $request)
    {
        $trade_no = $request->get('out_trade_no');
        $result_code = $request->get('result_code');
        $trade_no = $request->get('trade_no');
        //付款成功
        if ($result_code == "9000") {
            return view('admin.pingan.page.paysuccess');
        } else {
            return redirect(url('admin/alipayopen/OrderErrors?code=' . $result_code));
        }
    }


}