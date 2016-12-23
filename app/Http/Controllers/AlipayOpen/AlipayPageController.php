<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2016/12/21
 * Time: 18:23
 */

namespace App\Http\Controllers\AlipayOpen;


use Illuminate\Http\Request;

class AlipayPageController extends AlipayOpenController
{
    /**
     * 支付成功页面
     */
    public function PaySuccess(Request $request)
    {
        $price=$request->get('price');
        return view('admin.alipayopen.page.paysuccess',compact('price'));

    }
    public function OrderErrors(Request $request){

        $code=$request->get('code');
        return view('admin.alipayopen.page.ordererrors',compact('code'));
    }

}