<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/17
 * Time: 16:09
 */

namespace App\Http\Controllers\AlipayOpen;


use Illuminate\Http\Request;

class NotifyController extends AlipayOpenController
{

    public function notify(Request $request)
    {
    dd($request);
    }
    //商户开店状态通知URL
    public function operate_notify_url(){

    }
}