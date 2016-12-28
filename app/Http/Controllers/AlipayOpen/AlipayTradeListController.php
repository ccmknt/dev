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
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AlipayTradeListController extends AlipayOpenController
{
    public function index(Request $request)
    {
        $query = AlipayTradeQuery::orderBy('created_at', 'desc')->get();
        if ($query->isEmpty()) {
            $paginator="";
            $datapage="";
        } else {
            $query = $query->toArray();
            foreach ($query as $k => $v) {
                $data1 = json_decode($this->QueryStatus($v['trade_no']), true);
                $data2 = $data1[$k] = $v;
                $data[] = array_merge($data1, $data2);
            }
            //非数据库模型自定义分页
            $perPage = 9;//每页数量
            if ($request->has('page')) {
                $current_page = $request->input('page');
                $current_page = $current_page <= 0 ? 1 : $current_page;
            } else {
                $current_page = 1;
            }
            $item = array_slice($data, ($current_page - 1) * $perPage, $perPage); //注释1
            $total = count($data);
            $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
            $datapage = $paginator->toArray()['data'];
        }
        return view('admin.alipayopen.alipaytradelist', compact('paginator', 'datapage'));
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