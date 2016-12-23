<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2016/12/22
 * Time: 10:00
 */

namespace App\Http\Controllers\Api;


use Alipayopen\Sdk\Request\AlipayTradeQueryRequest;
use App\Models\AlipayTradeQuery;
use Illuminate\Http\Request;

class AlipayTradeQueryController extends BaseController
{

    /**
     * 查询交易
     */
    public function QueryStatus(Request $request)
    {
        $out_trade_no = $request->get('out_trade_no');//商户订单号
        $trade_no = $request->get('out_trade_no');//支付宝交易号

        $aop = $this->AopClient();
        $aop->apiVersion = "2.0";

        $requests = new AlipayTradeQueryRequest();

        if ($out_trade_no) {
            $requests->setBizContent("{" .
                "    \"out_trade_no\":\"" . $out_trade_no . "\"" .
                "  }");
        } else {
            $requests->setBizContent("{" .
                "    \"trade_no\":\"" . $trade_no . "\"" .
                "  }");
        }

        $result = $aop->execute($request);
    }

    //更新订单状态
    public function UpdateStatus(Request $request)
    {

    }
}