<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/27
 * Time: 12:55
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Models\AlipayTradeQuery;
use Alipayopen\Sdk\Request\AlipayTradeQueryRequest;
use Illuminate\Http\Request;
class AlipayTradeListController extends AlipayOpenController
{

    public function index(Request $request)
    {
        $query = AlipayTradeQuery::all();
        if ($query) {
            $query = $query->toArray();
        }
        foreach ($query as $k=>$v) {
            $data1=json_decode($this->QueryStatus($v['trade_no']),true);
            $data2=$data1[$k]=$v;
            $data[]=array_merge($data1,$data2);
        }
        return view('admin.alipayopen.alipaytradelist',compact('data'));
    }

    /**
     * 查询交易
     */
    public function QueryStatus($trade_no)
    {
        $aop = $this->AopClient();
        $aop->method = "alipay.trade.query";
        $aop->apiVersion = "2.0";
        $requests = new AlipayTradeQueryRequest();
        $requests->setBizContent("{" .
            "    \"trade_no\":\"" . $trade_no . "\"" .
            "  }");
        $result = $aop->execute($requests);
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        return json_encode($result->$responseNode);
    }
}