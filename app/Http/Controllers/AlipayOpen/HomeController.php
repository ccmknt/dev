<?php

namespace App\Http\Controllers\AlipayOpen;

use App\App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

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
        if (Auth::user()->hasRole('admin')) {
            //总交易量
            $total_amount = DB::table('alipay_trade_queries')->where('status', 'TRADE_SUCCESS')->sum('total_amount');
            //总店铺数
            $stores = DB::table('alipay_shop_lists')->where('audit_status', 'AUDIT_SUCCESS')->count();
            //昨日店铺
            $store_y = DB::table('alipay_shop_lists')
                ->where('audit_status', 'AUDIT_SUCCESS')
                ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                ->count();
            //昨天流水
            $total_y = DB::table('alipay_trade_queries')
                ->where('status', 'TRADE_SUCCESS')
                ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                ->sum('total_amount');

        } else {
            $total_amount = '计算中';
            $stores = '计算中';
            $total_y = '计算中';
            $store_y = '计算中';

        }
        return view('admin.alipayopen.home', compact('data', 'total_amount', 'stores','total_y','store_y'));
    }
}
