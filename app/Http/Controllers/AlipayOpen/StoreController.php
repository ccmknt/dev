<?php

namespace App\Http\Controllers\AlipayOpen;

use Alipayopen\Sdk\Request\AlipayOfflineMarketShopCategoryQueryRequest;
use Alipayopen\Sdk\Request\AlipayOfflineMarketShopCreateRequest;
use Alipayopen\Sdk\Request\AlipayOfflineMarketShopModifyRequest;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayShopCategory;
use App\Models\AlipayShopLists;
use App\Models\ProvinceCity;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class StoreController extends AlipayOpenController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = AlipayShopLists::orderBy('created_at', 'desc')->get();
        if ($data->isEmpty()){
            $paginator="";
            $datapage="";
        }else{
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

        return view('admin.alipayopen.store.index', compact('datapage','paginator'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.alipayopen.store.create');

    }

    /**创建店铺
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $config = AlipayIsvConfig::where('id', 1)->first();
        if ($config) {
            $config = $config->toArray();
        }
        $longitude_latitude = explode(',', $request->get("longitude_latitude"));//经纬度
        $longitude = $longitude_latitude[0];//经度
        $latitude = $longitude_latitude[1];//纬度
        $store_id = $request->get('store_id');
        $data = [
            "store_id" => "" . $store_id . "",
            "apply_id" => "",
            "shop_id" => "",
            "user_id" => Auth::user()->id,
            "category_id" => "" . $request->get('category_id') . "",
            "app_auth_token" => "" . $request->get('app_auth_token') . "",
            "brand_name" => "" . $request->get('brand_name') . "",
            "brand_logo" => "" . $request->get('brand_logo') . "",
            "main_shop_name" => "" . $request->get('main_shop_name') . "",
            "branch_shop_name" => "" . $request->get('branch_shop_name') . "",
            "province_code" => "" . $request->get('province_code') . "",
            "city_code" => "" . $request->get('city_code') . "",
            "district_code" => "" . $request->get('district_code') . "",
            "address" => "" . $request->get('address') . "",
            "longitude" => $longitude,
            "latitude" => "" . $latitude . "",
            "contact_number" => "" . $request->get('contact_number') . "",
            "notify_mobile" => "" . $request->get('notify_mobile') . "",
            "main_image" => "" . $request->get('main_image') . "",
            "audit_images" => "" . $request->get('audit_images1') . "," . $request->get('audit_images2') . "," . $request->get('audit_images3') . "",
            "business_time" => "" . $request->get('business_time') . "",
            "wifi" => "" . $request->get('wifi') . "",
            "parking" => "" . $request->get('parking') . "",
            "value_added" => "" . $request->get('value_added') . "",
            "avg_price" => "" . $request->get('avg_price') . "",
            "isv_uid" => "" . $config['pid'] . "",
            "licence" => "" . $request->get('licence') . "",
            "licence_code" => "" . $request->get('licence_code') . "",
            "licence_name" => "" . $request->get('licence_name') . "",
            "business_certificate" => "" . $request->get('business_certificate') . "",
            "business_certificate_expires" => "" . $request->get('business_certificate_expires') . "",
            "auth_letter" => "" . $request->get('auth_letter') . "",
            "is_operating_online" => "" . $request->get('is_operating_online') . "",
            "online_url" => "" . $request->get('online_url') . "",
            "operate_notify_url" => "" . $request->get('operate_notify_url') . "",
            "implement_id" => "" . $request->get('implement_id') . "",
            "no_smoking" => "" . $request->get('no_smoking') . "",
            "box" => "" . $request->get('box') . "",
            "request_id" => "" . $request->get('request_id') . "",
            "other_authorization" => "" . $request->get('other_authorization') . "",
            "licence_expires" => "" . $request->get('licence_expires') . "",
            "op_role" => "ISV",
            "biz_version" => "2.0",
        ];
        $shop = AlipayShopLists::where('store_id', $store_id)->first();
        if ($shop) {
            AlipayShopLists::where('store_id', $store_id)->update($data);
        } else {
            AlipayShopLists::create($data);
        }

        //提交到口碑
        $aop = $this->AopClient();
        $aop->apiVersion = "2.0";
        $aop->method = 'alipay.offline.market.shop.create';
        $requests = new AlipayOfflineMarketShopCreateRequest();
        $requests->setBizContent("{" .
            "\"store_id\":\"" . $request->get('store_id') . "\"," .
            "\"category_id\":\"" . $request->get('category_id') . "\"," .
            "\"brand_name\":\"" . $request->get('brand_name') . "\"," .
            "\"brand_logo\":\"" . $request->get('brand_logo') . "\"," .
            "\"main_shop_name\":\"" . $request->get('main_shop_name') . "\"," .
            "\"branch_shop_name\":\"" . $request->get('branch_shop_name') . "\"," .
            "\"province_code\":\"" . $request->get('province_code') . "\"," .
            "\"city_code\":\"" . $request->get('city_code') . "\"," .
            "\"district_code\":\"" . $request->get('district_code') . "\"," .
            "\"address\":\"" . $request->get('address') . "\"," .
            "\"longitude\":" . $longitude . "," .
            "\"latitude\":\"" . $latitude . "\"," .
            "\"contact_number\":\"" . $request->get('contact_number') . "\"," .
            "\"notify_mobile\":\"" . $request->get('notify_mobile') . "\"," .
            "\"main_image\":\"" . $request->get('main_image') . "\"," .
            "\"audit_images\":\"" . $request->get('audit_images1') . "," . $request->get('audit_images2') . "," . $request->get('audit_images3') . "\"," .
            "\"business_time\":\"" . $request->get('business_time') . "\"," .
            "\"wifi\":\"" . $request->get('wifi') . "\"," .
            "\"parking\":\"" . $request->get('parking') . "\"," .
            "\"value_added\":\"" . $request->get('value_added') . "\"," .
            "\"avg_price\":\"" . $request->get('avg_price') . "\"," .
            "\"isv_uid\":\"" . $config['pid'] . "\"," .
            "\"licence\":\"" . $request->get('licence') . "\"," .
            "\"licence_code\":\"" . $request->get('licence_code') . "\"," .
            "\"licence_name\":\"" . $request->get('licence_name') . "\"," .
            "\"business_certificate\":\"" . $request->get('business_certificate') . "\"," .
            "\"business_certificate_expires\":\"" . $request->get('business_certificate_expires') . "\"," .
            "\"auth_letter\":\"" . $request->get('auth_letter') . "\"," .
            "\"is_operating_online\":\"" . $request->get('is_operating_online') . "\"," .
            "\"online_url\":\"" . $request->get('online_url') . "\"," .
            "\"operate_notify_url\":\"" . $config['operate_notify_url'] . "\"," .
            "\"implement_id\":\"" . $request->get('implement_id') . "\"," .
            "\"no_smoking\":\"" . $request->get('no_smoking') . "\"," .
            "\"box\":\"" . $request->get('box') . "\"," .
            "\"request_id\":\"" . $request->get('request_id') . "\"," .
            "\"other_authorization\":\"" . $request->get('other_authorization') . "\"," .
            "\"licence_expires\":\"" . $request->get('licence_expires') . "\"," .
            "\"op_role\":\"ISV\"," .
            "\"biz_version\":\"2.0\"" .
            "  }");
        try {
            $result = $aop->execute($requests, NULL, $request->get('app_auth_token'));
            $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        } catch (\Exception $exception) {
            return $exception;
        }
        if ($result->$responseNode->code == 1000) {
            //存储数据库
            $updata = [
                "apply_id" => $result->$responseNode->apply_id,
            ];
            AlipayShopLists::where('store_id', $store_id)->update($updata);
        }
        // $resultCode = $result->$responseNode->code;
        $re = [
            'code' => $result->$responseNode->code,
            'msg' => $result->$responseNode->msg,
            'sub_code' => $result->$responseNode->sub_code,
            'sub_msg' => $result->$responseNode->sub_msg,
        ];
        return json_encode($re);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //分类
        $category = AlipayShopCategory::all();
        if ($category) {
            $category = $category->toArray();
        }

        $shop = AlipayShopLists::where('id', $id)->first();
        if ($shop) {
            $shop = $shop->toArray();
        }
        $audit_images = explode(',', $shop['audit_images']);
        $shop['audit_images1'] = $audit_images[0];
        $shop['audit_images2'] = $audit_images[1];
        $shop['audit_images3'] = $audit_images[2];
        //地区 省
        $province = ProvinceCity::where('areaParentId',1)->get();
        $city = ProvinceCity::where('areaCode', $shop['city_code'])->get();
        $district = ProvinceCity::where('areaCode', $shop['district_code'])->get();
        if ($province) {
            $province = $province->toArray();
        }
        if ($city) {
            $city = $city->toArray();
        }
        if ($district) {
            $district = $district->toArray();
        }
        $province_city_district = [
            'province' => $province,
            'city' => $city,
            'district' => $district
        ];
        return view('admin.alipayopen.store.edit', compact('shop', 'category', 'province_city_district'));
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
