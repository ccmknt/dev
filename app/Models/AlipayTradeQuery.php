<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlipayTradeQuery extends Model
{
    //

    protected  $fillable=['out_trade_no','trade_no','store_id','status','total_amount'];
}
