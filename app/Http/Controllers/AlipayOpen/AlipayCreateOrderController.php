<?php

namespace App\Http\Controllers\AlipayOpen;


use App\Models\AlipayShopLists;
use Illuminate\Http\Request;
use App\Http\Requests;


/**
 * Class AlipayTradePrecreateController
 * @package App\Http\Controllers
 */
class AlipayCreateOrderController extends AlipayOpenController
{

    public function alipay_trade_create(Request $request)
    {
        $u_id = $request->get('u_id');
        $shop = AlipayShopLists::where('id', $u_id)->first();//用户信息
        if ($shop) {
            $shop = $shop->toArray();
        }
        return view('admin.alipayopen.createorder', compact('shop'));
    }
}
