<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinganConfig extends Model
{
    //
    protected  $fillable=['app_id','rsaPrivateKey','pinganrsaPublicKey'];
}