<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2016/12/22
 * Time: 13:43
 */

namespace App\Http\Controllers\AlipayOpen;


class ApplyorderBatchqueryController extends AlipayOpenController
{


    public function query()
    {
      return  view('admin.alipayopen.store.applyorderbatchquery');
    }
}