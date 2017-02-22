<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/1/10
 * Time: 15:00
 */

namespace App\Http\Controllers\Weixin;


use App\Http\Controllers\Controller;
use App\Models\WeixinShopList;
use App\Models\WxPayOrder;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShopsListsController extends BaseController
{

    public function index(Request $request)
    {
        //
        $data = WeixinShopList::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        if (Auth::user()->hasRole('admin')) {
            $data = WeixinShopList::orderBy('created_at', 'desc')->get();
        }

        if ($data->isEmpty()) {
            $paginator = "";
            $datapage = "";
        } else {
            $data = $data->toArray();
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
        return view('admin.weixin.shoplist', compact('datapage', 'paginator'));
    }

    public function WxAddShop()
    {

        return view('admin.weixin.add');

    }

    public function WxEditShop(Request $request)
    {
        $id = $request->get('id');
        $Shop = WeixinShopList::where('id', $id)->first();
        return view('admin.weixin.edit', compact('Shop'));
    }

    public function WxShopPost(Request $request)
    {

        $data = $request->except(['_token']);
        $rules = [
            'store_name' => 'required',
            'mch_id' => 'required',
        ];
        $messages = [
            'required' => '必填项',
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator);  //返回一次性错误
        }
        $data['store_id'] = $request->get('mch_id');
        $data['user_id'] = Auth::user()->id;
        WeixinShopList::create($data);
        return redirect(route('WxShopList'));
    }

    public function WxEditShopPost(Request $request)
    {
        $data = $request->except(['_token', 'id']);
        $rules = [
            'store_name' => 'required',
            'mch_id' => 'required',
        ];
        $messages = [
            'required' => '必填项',
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator);  //返回一次性错误
        }
        $data['store_id'] = $request->get('mch_id');
        $data['user_id'] = Auth::user()->id;
        WeixinShopList::where('id', $request->get('id'))->update($data);
        return redirect(route('WxShopList'));
    }

    public function WxPayQr(Request $request)
    {
        $mch_id = $request->get('mch_id');
        $shop = WeixinShopList::where('mch_id', $mch_id)->first();
        $code_url = url('admin/weixin/oauth?sub_info=pay_' . $mch_id);
        return view('admin.weixin.wxpayqr', compact('code_url', 'shop'));
    }
//收单列表
    public function WxOrder(Request $request)
    {
        $wxorder = DB::table('wx_pay_orders')
            ->join('weixin_shop_lists', 'wx_pay_orders.mch_id', '=', 'weixin_shop_lists.store_id')
            ->select('wx_pay_orders.*', 'weixin_shop_lists.store_name')
            ->orderBy('updated_at', 'desc')
            ->paginate(8);

        return view('admin.weixin.wxorder', compact('wxorder'));

    }

    public function WxOrder1(Request $request)
    {
        $wxorder = WxPayOrder::all();
        if ($wxorder->isEmpty()) {
            $paginator = "";
            $datapage = "";
        } else {
            $wxorder = $wxorder->toArray();
            foreach ($wxorder as $v) {
                $options = $this->Options();
                $options['payment']['sub_merchant_id'] = $v['mch_id'];
                $app = new Application($options);
                $payment = $app->payment;
                $orderNo = $v['out_trade_no'];
                $query = $payment->query($orderNo);
                if ($query->return_code == "SUCCESS") {
                    $data[] = array_merge($query->toArray(), $v);
                }
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
        return view('admin.weixin.wxorder', compact('paginator', 'datapage'));
        /*  dd($data);    0 => array:18 [▼
         "return_code" => "SUCCESS"
         "return_msg" => "OK"
         "appid" => "wx789fb035be0b7481"
         "mch_id" => "1419589702"
         "sub_mch_id" => "1419589702"
         "nonce_str" => "zDLnKNcxd6wxpdSp"
         "sign" => "A14999AFD736B9486250F98E701DB663"
         "result_code" => "SUCCESS"
         "out_trade_no" => "20170114030819888820170114030819"
         "trade_state" => "NOTPAY"
         "trade_state_desc" => "订单未支付"
         "id" => 43
         "transaction_id" => ""
         "total_fee" => "0.10"
         "open_id" => "opnT0s8Pltziuu2qATK3o8bKAWbA"
         "status" => ""
         "created_at" => "2017-01-14 15:08:19"
         "updated_at" => "2017-01-14 15:08:19"
       ]
       1 => array:25 [▼
         "return_code" => "SUCCESS"
         "return_msg" => "OK"
         "appid" => "wx789fb035be0b7481"
         "mch_id" => "1419589702"
         "sub_mch_id" => "1419589702"
         "nonce_str" => "Tmz4kk49sUft6SFi"
         "sign" => "9CEF6F088CC60A40F1173C9F2728F492"
         "result_code" => "SUCCESS"
         "openid" => "opnT0s8Pltziuu2qATK3o8bKAWbA"
         "is_subscribe" => "Y"
         "trade_type" => "JSAPI"
         "bank_type" => "CFT"
         "total_fee" => "0.01"
         "fee_type" => "CNY"
         "transaction_id" => ""
         "out_trade_no" => "20170114030827888820170114030827"
         "attach" => null
         "time_end" => "20170114150831"
         "trade_state" => "SUCCESS"
         "cash_fee" => "1"
         "id" => 44
         "open_id" => "opnT0s8Pltziuu2qATK3o8bKAWbA"
         "status" => ""
         "created_at" => "2017-01-14 15:08:27"
         "updated_at" => "2017-01-14 15:08:27"
       ]
     ]*/
    }
}