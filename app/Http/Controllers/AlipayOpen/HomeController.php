<?php

namespace App\Http\Controllers\AlipayOpen;

use App\App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.index');
    }

    //后台主页
    public function home()
    {
        $data = App::where('id', 1)->first();
        return view('admin.alipayopen.home', compact('data'));
    }
}
