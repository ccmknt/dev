<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    //
    public function spset(){
        return view('admin.weixin.spset');
    }
}
