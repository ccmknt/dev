<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/1/17
 * Time: 23:08
 */

namespace App\Http\Controllers\PingAn;

use Alipayopen\Sdk\Request\AlipayOfflineMarketShopCreateRequest;
use App\Models\PingancqrLsits;
use App\Models\PingancqrLsitsinfo;
use App\Models\PinganStore;
use App\Models\PinganStoreInfos;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreController extends BaseController

{

    public function index(Request $request)
    {

        $auth = Auth::user()->can('pinganstore');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $data = PinganStore::where('user_id', Auth::user()->id)->where('is_delete', 0)->orderBy('created_at', 'desc')->get();
        if (Auth::user()->hasRole('admin')) {
            $data = PinganStore::where('is_delete', 0)->orderBy('created_at', 'desc')->get();
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

        return view('admin.pingan.store.index', compact('datapage', 'paginator'));

    }

    public function add()
    {
        $auth = Auth::user()->can('pinganstore');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        return view('admin.pingan.store.add');
    }

    public function addPost(Request $request)
    {
        $auth = Auth::user()->can('pinganstore');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $store = $request->except('_token');
        $aop = $this->AopClient();
        $aop->method = "fshows.liquidation.submerchant.create";
        $data = array('content' => json_encode($store));
        $response = $aop->execute($data);
        $responseArray = json_decode($response, true);
        if ($responseArray['success']) {
            $store['user_id'] = Auth::user()->id;
            $store['user_name'] = Auth::user()->name;
            $store['sub_merchant_id'] = $responseArray['return_value']['sub_merchant_id'];
            $storeinfo = PinganStore::where('external_id', $store['external_id'])->first();
            if ($storeinfo) {
                PinganStore::where('external_id', $store['external_id'])->update($store);
            } else {
                PinganStore::create($store);
            }
        }
        return $response;
    }

    public function DelPinanStore(Request $request)
    {
        $id = $request->get('id');
        PinganStore::where('id', $id)->update(['is_delete' => 1]);
        return json_encode(['status' => 1]);
    }

    public function SetStore(Request $request)
    {
        $id = $request->get('id');
        $store = PinganStore::where('id', $id)->first();
        return view('admin.pingan.store.set', compact('store'));
    }

    public function SetStorePost(Request $request)
    {
        $aop = $this->AopClient();
        $aop->method = 'fshows.liquidation.submerchant.bank.bind';
        $store = PinganStore::where('id', $request->get('id'))->first();
        if ($request->get('is_public_account') == 1) {
            $content = $request->except(['_token', 'id', 'merchant_rate']);
        } else {
            $content = $request->except(['_token', 'id', 'merchant_rate', 'is_public_account', 'open_bank']);
        }
        $content['sub_merchant_id'] = $store->sub_merchant_id;
        $data = array('content' => json_encode($content));
        $response = $aop->execute($data);
        $responseArray = json_decode($response, true);
        if ($responseArray['success']) {//绑卡成功
            PinganStore::where('id', $request->get('id'))->update($content);
        }
        return $response;

    }

    public function setMerchantRate(Request $request)
    {
        $id = $request->get('id');
        $store = PinganStore::where('id', $id)->first();
        return view('admin.pingan.store.setM', compact('store'));

    }

    public function setMerchantRatePost(Request $request)
    {
        //设置费率
        $merchant_rate = $request->get('merchant_rate');
        $id = $request->get('id');
        $store = PinganStore::where('id', $id)->first();
        $aop = $this->AopClient();
        $aop->method = 'fshows.liquidation.submerchant.rate.set';
        $con = [
            'sub_merchant_id' => $store->sub_merchant_id,
            'merchant_rate' => $merchant_rate
        ];
        $data = array('content' => json_encode($con));
        $response = $aop->execute($data);
        $responseArray = json_decode($response, true);
        if ($responseArray['success']) {//绑卡成功
            PinganStore::where('id', $request->get('id'))->update($con);
        }
        return $response;
    }

    public function PingAnStoreQR()
    {
        $code_url = url('admin/pingan/autoStore?user_id=' . Auth::user()->id);
        return view('admin.pingan.store.myqr', compact('code_url'));
    }

    //提交店铺信息
    public function autoStore()
    {
        $pay_type = "other";
        //判断是不是微信
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $pay_type = 'weixin';
        }
        //判断是不是支付宝
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $pay_type = 'alipay';
        }

        if ($pay_type == "other") {

            echo '请用支付宝或者微信扫描二维码';

        }
        return view('admin.pingan.store.autostore');
    }

    //自主提交到后台保存 第一步提交
    public function autoStorePost(Request $request)
    {
        $store = $request->except(['_token', 'user_id']);
        $aop = $this->AopClient();
        $aop->method = "fshows.liquidation.submerchant.create";
        $data = array('content' => json_encode($store));
        try {
            $response = $aop->execute($data);
            $responseArray = json_decode($response, true);
        } catch (\Exception $exception) {
            return '系统超时！请刷新再试';
        }

        if ($responseArray['success']) {
            $store['user_id'] = $request->get('user_id', 7);
            $store['contact_mobile'] = $request->get('service_phone');
            $store['user_name'] = User::where('id', $request->get('user_id', 1))->first()->name;
            $store['sub_merchant_id'] = $responseArray['return_value']['sub_merchant_id'];
            $storeinfo = PinganStore::where('external_id', $store['external_id'])->first();
            if ($storeinfo) {
                PinganStore::where('external_id', $store['external_id'])->update($store);
            } else {
                PinganStore::create($store);
            }
        }


        return $response;
    }

//用户自主绑定银行卡 第二步 页面
    public function autom()
    {
        $pay_type = "other";
        //判断是不是微信
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $pay_type = 'weixin';
        }
        //判断是不是支付宝
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $pay_type = 'alipay';
        }

        if ($pay_type == "other") {

            echo '请用支付宝或者微信扫描二维码';

        }

        return view('admin.pingan.store.autom');
    }

    //提交绑定银行卡
    public function automPost(Request $request)
    {
        $external_id = $request->get('external_id');
        $code_number = $request->get('$code_number');
        $aop = $this->AopClient();
        $aop->method = 'fshows.liquidation.submerchant.bank.bind';
        if ($request->get('is_public_account') == 1) {
            $content = [
                'is_public_account' => 1,
                'open_bank' => $request->get('open_bank')
            ];
        }
        $store = PinganStore::where('external_id', $external_id)->first();
        $content['sub_merchant_id'] = $store->sub_merchant_id;
        $content['bank_card_no'] = $request->get('bank_card_no');
        $content['card_holder'] = $request->get('card_holder');
        $data = array('content' => json_encode($content));
        $response = $aop->execute($data);
        $responseArray = json_decode($response, true);
        if ($responseArray['success']) {//绑卡成功
            try {
                PinganStore::where('external_id', $external_id)->update($content);//修改商户信息
            } catch (\Exception $exception) {
                return json_encode([
                    'success' => 0,
                    'error_message' => '修改商户信息保存失败！'
                ]);
            }
        }
        return $response;
    }

    //第三步上传资质文件

    public function autoFile(Request $request)
    {
        $pay_type = "other";
        //判断是不是微信
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $pay_type = 'weixin';
        }
        //判断是不是支付宝
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $pay_type = 'alipay';
        }

        if ($pay_type == "other") {

            echo '请用支付宝或者微信扫描二维码';

        }

        return view('admin.pingan.store.autoFile');


    }

    public function autoFilePost(Request $request)
    {
        $external_id = $request->get('external_id');
        $code_number = $request->get('code_number');
        $PinganStore = PinganStore::where('external_id', $external_id)->first();
        if ($PinganStore) {
            try {
                $pInfo = PinganStoreInfos::where('external_id', $external_id)->first();
                if ($pInfo) {
                    PinganStoreInfos::where('external_id', $external_id)->update($request->except(['_token', 'code_number']));
                } else {
                    PinganStoreInfos::create($request->except(['_token', 'code_number']));
                }
            } catch (\Exception $exception) {
                return json_encode([
                    'success' => 0,
                    'error_message' => '200信息保存失败！'
                ]);
            }
            try {
                //修改二维码为商户收款码
                PingancqrLsitsinfo::where('code_number', $code_number)->update([
                    'store_id' => $external_id,
                    'code_type' => 1,
                    'store_name' => $PinganStore->alias_name
                ]);
            } catch (\Exception $exception) {
                return json_encode([
                    'success' => 0,
                    'error_message' => '2001保存失败！'
                ]);
            }
            /*  try {
                  //修改已使用数量
                  $s_sum = DB::table('pingancqr_lsitsinfos')->where('cno', $pInfo->cno)->where('code_type', 1)->count();
                  PingancqrLsits::where('cno', $pInfo->cno)->update([
                      's_num' => $s_sum,
                  ]);

              } catch (\Exception $exception) {
                  return json_encode([
                      'success' => 0,
                      'error_message' => '2002保存失败！'
                  ]);
              }*/
        }
        return json_encode([
            'success' => 1,
        ]);

    }

    public function success()
    {
        return view('admin.pingan.store.success');
    }

    public function OrderQuery(Request $request)
    {

        if (Auth::user()->hasRole('admin')) {
            $order = DB::table('pingan_trade_queries')
                ->join('pingan_stores', 'pingan_trade_queries.store_id', '=', 'pingan_stores.external_id')
                ->select('pingan_trade_queries.*', 'pingan_stores.alias_name')
                ->orderBy('updated_at', 'desc')
                ->paginate(8);
        } else {
            $order = DB::table('pingan_trade_queries')
                ->join('pingan_stores', 'pingan_trade_queries.store_id', '=', 'pingan_stores.external_id')
                ->select('pingan_trade_queries.*', 'pingan_stores.alias_name')
                ->where('user_id', Auth::user()->id)
                ->orderBy('updated_at', 'desc')
                ->paginate(8);
        }
        return view('admin.pingan.store.order', compact('order'));
    }

    //店铺收款状态
    public function PayStatus(Request $request)
    {
        $type = $request->get('type');
        try {
            PinganStore::where('id', $request->get('id'))->update([
                'pay_status' => $type
            ]);
        } catch (\Exception $exception) {
            return json_encode([
                'success' => 0,
            ]);
        }
        return json_encode([
            'success' => 1,
        ]);

    }
}