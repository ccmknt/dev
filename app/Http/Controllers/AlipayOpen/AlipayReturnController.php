<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/14
 * Time: 18:29
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Models\AlipayStoreInfo;
use Illuminate\Http\Request;

class AlipayReturnController extends AlipayOpenController
{


    public function shopNotify(Request $request)
    {
        $data = [];
        $info = AlipayStoreInfo::where('store_id', $request->get('store_id'))->first();
        if ($info) {
            $data = $info->toArray();
        }
        return json_encode($data);

    }

}