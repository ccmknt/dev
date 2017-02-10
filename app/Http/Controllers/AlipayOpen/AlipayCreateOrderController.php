<?php

namespace App\Http\Controllers\AlipayOpen;


use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayShopLists;
use Illuminate\Http\Request;
use App\Http\Requests;


/**
 * Class AlipayTradePrecreateController
 * @package App\Http\Controllers
 */
class AlipayCreateOrderController extends AlipayOpenController
{
  //商户收款码金额界面有店铺
    public function alipay_trade_create(Request $request)
    {
        $u_id = $request->get('u_id');
        $shop = AlipayShopLists::where('id', $u_id)->first();//用户信息
        if ($shop) {
            $shop = $shop->toArray();
        }
        return view('admin.alipayopen.create_qr_order', compact('shop'));
    }
   //仅收款码金额界面 无店铺
    public function alipay_oqr_create(Request $request)
    {
        $u_id = $request->get('u_id');
        $shop = AlipayAppOauthUsers::where('user_id', $u_id)->first();//用户信息
        if ($shop) {
            $shop = $shop->toArray();
        }
        return view('admin.alipayopen.create_oqr_order', compact('shop'));
    }
}
