<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WxPayOrder extends Model
{
    //
    protected $fillable=['mch_id','out_trade_no','transaction_id','total_fee','open_id','status'];
}
