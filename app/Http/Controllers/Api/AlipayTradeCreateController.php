<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/2
 * Time: 11:10
 */
namespace App\Http\Controllers\Api;

use Alipayopen\Sdk\Request\AlipayTradeCreateRequest;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayShopLists;
use App\Models\AlipayTradeQuery;
use Illuminate\Http\Request;

class AlipayTradeCreateController extends BaseController
{
    /**
     * 统一收单交易创建接口
     */
    public function AlipayTradeCreate(Request $request)
    {
        //0.接受参数
        $total_amount = $request->get('total_amount');
        $u_id = $request->get('u_id');

        $shop = AlipayShopLists::where('id', $u_id)->first();
        if ($shop) {
            $shop = $shop->toArray();
        }else{
            $shop['main_shop_name']="商户";
        }

        $config = AlipayIsvConfig::where('id', 1)->first();
        if ($config) {
            $config = $config->toArray();
        }
        $goods_id = "goods_" . date('YmdHis', time());
        //1.实例化公共参数
        $c = $this->AopClient();
        $c->method = "alipay.trade.create";
        $c->version = "2.0";
        //2.调用接口
        $requests = new AlipayTradeCreateRequest();
        $user = $request->session()->get('user_data');
        $out_trade_no = time() . rand(100, 999);
        /**
         * 如果打开下面的注释记得查看前后的标点符号
         * */
        $requests->setBizContent("{" .
            "\"out_trade_no\":" . $out_trade_no . "," .
            /*  "\"seller_id\":\"2088102169018185\"," .*/
            "\"total_amount\":" . $total_amount . "," .
            "\"subject\":\"" . $shop['main_shop_name'] . "收款" . "\"," .
            "\"body\":\"" . $shop['main_shop_name'] . "扫码收款" . "\"," .
            "\"buyer_id\":" . $user[0]->user_id . "," .
            "\"goods_detail\":[{" .
            "\"goods_id\":\"" . $goods_id . "\"," .
            "\"goods_name\":\"" . $shop['main_shop_name'] . "\"," .
            " \"quantity\":1," .
            "\"price\":" . $total_amount . "" .
            "}]," .
            "\"store_id\":\"" . $shop['store_id'] . "\"," .
            "\"extend_params\":{" .
            "\"sys_service_provider_id\":\"" . $config['pid'] . "\"" .
            /*  "\"hb_fq_num\":\"3\"," .
                 "\"hb_fq_seller_percent\":\"100\"" .*/
            "}," .
            "\"timeout_express\":\"90m\"" .
            "}");
        $result = $c->execute($requests, null, $shop['app_auth_token']);
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        $trade_no = $result->$responseNode->trade_no;//订单号
        if (!empty($resultCode) && $resultCode == 10000) {
            //保存数据库
            $insert = [
                'trade_no' => $trade_no,
                "out_trade_no" => $out_trade_no,
                "status" => "",
                "total_amount" => $total_amount,
                'store_id' => $shop['store_id'],
            ];
            AlipayTradeQuery::create($insert);
            $data = [
                'status' => 1,
                "trade_no" => $trade_no,
                "msg" => "OK",
            ];
        } else {
            $data = [
                'status' => 0,
                "trade_no" => "",
                "msg" => "error",
            ];
        }
        return json_encode($data);

    }
    /**
     * 统一收单交易创建接口
     */
    public function AlipayOqrCreate(Request $request)
    {
        //0.接受参数
        $total_amount = $request->get('total_amount');
        $u_id = $request->get('u_id');
        $shop = AlipayAppOauthUsers::where('user_id', $u_id)->first();
        if ($shop) {
            $shop = $shop->toArray();
        }else{
            $shop['auth_shop_name']="商户";
        }
        $config = AlipayIsvConfig::where('id', 1)->first();
        if ($config) {
            $config = $config->toArray();
        }
        $goods_id = "goods_" . date('YmdHis', time());
        //1.实例化公共参数
        $c = $this->AopClient();
        $c->method = "alipay.trade.create";
        $c->version = "2.0";
        //2.调用接口
        $requests = new AlipayTradeCreateRequest();
        $user = $request->session()->get('user_data');
        $out_trade_no = time() . rand(100, 999);
        /**
         * 如果打开下面的注释记得查看前后的标点符号
         * */
        $requests->setBizContent("{" .
            "\"out_trade_no\":" . $out_trade_no . "," .
            /*  "\"seller_id\":\"2088102169018185\"," .*/
            "\"total_amount\":" . $total_amount . "," .
            "\"subject\":\"" . $shop['auth_shop_name'] . "收款" . "\"," .
            "\"body\":\"" . $shop['auth_shop_name'] . "扫码收款" . "\"," .
            "\"buyer_id\":" . $user[0]->user_id . "," .
            "\"goods_detail\":[{" .
            "\"goods_id\":\"" . $goods_id . "\"," .
            "\"goods_name\":\"" . $shop['auth_shop_name'] . "\"," .
            " \"quantity\":1," .
            "\"price\":" . $total_amount . "" .
            "}]," .
            "\"store_id\":\"" . 'o'.$shop['user_id'] . "\"," .
            "\"extend_params\":{" .
            "\"sys_service_provider_id\":\"" . $config['pid'] . "\"" .
            /*  "\"hb_fq_num\":\"3\"," .
                 "\"hb_fq_seller_percent\":\"100\"" .*/
            "}," .
            "\"timeout_express\":\"90m\"" .
            "}");
        $result = $c->execute($requests, null, $shop['app_auth_token']);
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        $trade_no = $result->$responseNode->trade_no;//订单号
        if (!empty($resultCode) && $resultCode == 10000) {
            //保存数据库
            $insert = [
                'trade_no' => $trade_no,
                "out_trade_no" => $out_trade_no,
                "status" => "",
                "total_amount" => $total_amount,
                'store_id' => 'o'.$shop['user_id'],
            ];
            AlipayTradeQuery::create($insert);
            $data = [
                'status' => 1,
                "trade_no" => $trade_no,
                "msg" => "OK",
            ];
        } else {
            $data = [
                'status' => 0,
                "trade_no" => "",
                "msg" => "error",
            ];
        }
        return json_encode($data);

    }

}