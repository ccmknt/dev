<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/1/15
 * Time: 16:04
 */

namespace App\Http\Controllers\AlipayWeixin;


use App\Http\Controllers\Controller;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayWeixin;
use App\Models\WeixinShopList;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class AlipayWeixinController extends Controller
{
    public function AlipayWexinLists(Request $request)
    {
        //
        $data = AlipayWeixin::where('promoter_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        if (Auth::user()->hasRole('admin')) {
            $data = AlipayWeixin::orderBy('created_at', 'desc')->get();
        }
        if ($data->isEmpty()) {
            $paginator = "";
            $datapage = "";
        } else {
            $data = $data->toArray();
            //非数据库模型自定义分页
            $perPage = 9;//每页数量
            if ($request->has('page')) {
                $current_page = $request->input('page');
                $current_page = $current_page <= 0 ? 1 : $current_page;
            } else {
                $current_page = 1;
            }
            $item = array_slice($data, ($current_page - 1) * $perPage, $perPage); //注释1
            $total = count($data);
            $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
            $datapage = $paginator->toArray()['data'];
        }
        return view('admin.alipayweixin.list', compact('datapage', 'paginator'));
    }

    public function addAliPayWeixinStore(Request $request)
    {
        $store = AlipayAppOauthUsers::all();
        return view('admin.alipayweixin.add', compact('store'));
    }

    public function addAliPayWeixinStorePost(Request $request)
    {
        $id = $request->get('id');
        $alistore = AlipayAppOauthUsers::where('id', $id)->first();
        $wxstore = WeixinShopList::where('store_name', $alistore->auth_shop_name)->first();
        if ($wxstore) {
            $umxnt = AlipayWeixin::where('alipay_user_id', $alistore->user_id)->first();
            if ($umxnt) {
                return back()->with('errors', '此店铺已经生成过二码合一');  //返回一次性错误
            }
            $data = [
                'alipay_user_id' => $alistore->user_id,
                'alipay_auth_shop_name' => $alistore->auth_shop_name,
                'promoter_id' => $alistore->promoter_id,
                'alipay_app_auth_token' => $alistore->app_auth_token,
                'weixin_mch_id' => $wxstore->mch_id
            ];
            AlipayWeixin::create($data);
            return redirect(route('AlipayWexinLists'));
        } else {
            return back()->with('errors', '此店铺没有添加微信商户信息');  //返回一次性错误
        }

    }

    public function delAlipayWexin(Request $request)
    {
        $id = $request->get('id');
        if ($id) {
            AlipayWeixin::where('id', $id)->delete();
        }
        $data = [
            'status' => 1,
        ];
        return json_encode($data);
    }

    public function qr(Request $request)
    {
        $id = $request->get('id');
        AlipayWeixin::where('id', $id)->first();
        return view('admin/alipaeweixin/');
    }
}