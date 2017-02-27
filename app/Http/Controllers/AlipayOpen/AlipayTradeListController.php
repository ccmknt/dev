<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/27
 * Time: 12:55
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayShopLists;
use App\Models\AlipayTradeQuery;
use Alipayopen\Sdk\Request\AlipayTradeQueryRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class AlipayTradeListController extends AlipayOpenController
{

    public function index(Request $request)
    {
        if (Auth::user()->hasRole('admin')) {
            $query = AlipayTradeQuery::orderBy('updated_at', 'desc')->get();
            if ($query) {
                $query = $query->toArray();
                $data = [];
                foreach ($query as $k => $value) {
                    $data[] = $value;
                    $frist = substr($value['store_id'], 0, 1);
                    if ($frist == 'o') {
                        $store = AlipayAppOauthUsers::where('user_id', substr($value['store_id'], 1))->first();
                        if ($store) {
                            $data[$k]['store_name'] = $store->auth_shop_name;
                            $data[$k]['branch_shop_name'] = '';
                        } else {
                            $data[$k]['store_name'] = substr($value['store_id'], 1) . '_店铺';
                            $data[$k]['branch_shop_name'] = '';
                        }
                    }
                    if ($frist == 's') {
                        $store = AlipayShopLists::where('store_id', $value['store_id'])->first();
                        if ($store) {
                            $data[$k]['store_name'] = $store->main_shop_name;
                            $data[$k]['branch_shop_name'] = $store->branch_shop_name;
                        } else {
                            $data[$k]['store_name'] = substr($value['store_id'], 1) . '_店铺';
                            $data[$k]['branch_shop_name'] = '';
                        }

                    }

                }
            } else {
                $query = [];
            }
        } else {
            $query = [];
            //s店铺流水
            $query1 = DB::table('alipay_shop_lists')
                ->join('alipay_trade_queries', 'alipay_shop_lists.store_id', '=', 'alipay_trade_queries.store_id')
                ->where('user_id', Auth::user()->id)
                ->get()->toArray();

            //o店铺流水
            $query2 = DB::table('alipay_trade_queries')
                ->join('alipay_app_oauth_users', 'alipay_trade_queries.store_id', '=', 'alipay_app_oauth_users.store_id')
                ->where('promoter_id', Auth::user()->id)
                ->get()->toArray();
            $query = array_merge($query1, $query2);
            $data = [];
            foreach ($query as $k => $value) {
                $data[] = get_object_vars($value);
                $frist = substr($value->store_id, 0, 1);
                if ($frist == 'o') {
                    $store = AlipayAppOauthUsers::where('store_id', $value->store_id)->first();
                    if ($store) {
                        $data[$k]['store_name'] = $store->auth_shop_name;
                        $data[$k]['branch_shop_name'] = '';
                    } else {
                        $data[$k]['store_name'] = $value->store_id . '_店铺';
                        $data[$k]['branch_shop_name'] = '';
                    }
                }
                if ($frist == 's') {
                    $store = AlipayShopLists::where('store_id', $value->store_id)->first();
                    if ($store) {
                        $data[$k]['store_name'] = $store->main_shop_name;
                        $data[$k]['branch_shop_name'] = $store->branch_shop_name;
                    } else {
                        $data[$k]['store_name'] =$value->store_id . '_店铺';
                        $data[$k]['branch_shop_name'] = '';
                    }

                }

            }

        }

        //非数据库模型自定义分页
        $perPage = 8;//每页数量
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
        return view('admin.alipayopen.alipaytradelist', compact('datapage', 'paginator'));
    }

    public function index1(Request $request)
    {
        $auth = Auth::user()->can('alipaytradelist');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $query = AlipayTradeQuery::orderBy('created_at', 'desc')->get();
        //没有订单
        if ($query->isEmpty()) {
            $paginator = "";
            $datapage = "";
        } //有订单
        else {
            $query = $query->toArray();
            foreach ($query as $k => $v) {
                $user_id = substr($v['store_id'], 1);
                $user = AlipayAppOauthUsers::where('user_id', $user_id)->first();
                if ($user) {
                    $data1 = json_decode($this->QueryStatus($v['trade_no'], $user->app_auth_token), true);
                    //有效订单
                    if ($data1['code'] == "10000") {
                        $data2 = $data1[$k] = $v;
                        $data[] = array_merge($data1, $data2);

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
                }
            }

        }
        return view('admin.alipayopen.alipaytradelist', compact('paginator', 'datapage'));
    }

    /**
     * 查询交易 主动查询
     */
    public function QueryStatus($trade_no, $app_auth_token)
    {
        $aop = $this->AopClient();
        $aop->method = "alipay.trade.query";
        $aop->apiVersion = "2.0";
        $requests = new AlipayTradeQueryRequest();
        $requests->setBizContent("{" .
            "    \"trade_no\":\"" . $trade_no . "\"" .
            "  }");
        $result = $aop->execute($requests, null, $app_auth_token);
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        return json_encode($result->$responseNode);
    }
}