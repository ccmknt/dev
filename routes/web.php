<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|


*/
//前台
Route::get('/', 'IndexController@index');
Route::get('/logout', 'Auth\LoginController@logout');
Auth::routes();
//要登录的链接
Route::group(['namespace' => 'AlipayOpen', 'middleware' => 'auth', 'prefix' => 'admin/alipayopen'], function () {
    Route::resource('/store', 'StoreController');
    //admin 管理用户模块
    Route::get('/users', 'UsersController@users')->name('users');
    Route::get('/updateu', 'UsersController@updateu')->name('updateu');
    Route::post('/updateuSave', 'UsersController@updateuSave')->name('updateuSave');
    Route::post('/deleteu', 'UsersController@deleteu')->name('deleteu');
    //后台配置模块
    Route::get('/isvconfig', 'AlipayIsvConfigController@isvconfig')->name('isvconfig');
    Route::post('/saveconfig', 'AlipayIsvConfigController@saveconfig')->name('saveconfig');
    //商户业务流水操作查询
    Route::get('/ApplyorderBatchquery', 'ApplyorderBatchqueryController@query')->name('ApplyorderBatchquery');
});
Route::group(['namespace' => 'AlipayOpen'], function () {
    Route::get('/callback', 'OauthController@callback');
    Route::get('alipayopen/userinfo', 'OauthController@userinfo');
    Route::post('alipayopen/userinfoinsert', 'OauthController@userinfoinsert')->name('userinfo');
    Route::get('alipayopen/oauthlist', 'OauthController@oauthlist')->name('oauthlist');
});
Route::group(['namespace' => 'AlipayOpen', 'prefix' => 'admin/alipayopen'], function () {
    Route::get('/oauth', 'OauthController@oauth');
    Route::get('/auth', 'OauthController@auth');
    Route::get('/alipay_trade_precreate', 'AlipayTradePrecreateController@TradePrecreateQrCode')->name('alipay_trade_precreate');
    //输入金额页面
    Route::get('/alipay_trade_create', 'AlipayCreateOrderController@alipay_trade_create')->name('alipay_trade_create');
    //仅收款输入金额页
    Route::get('/alipay_oqr_create', 'AlipayCreateOrderController@alipay_oqr_create')->name('alipay_oqr_create');

    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@home')->name('home');
    Route::get('/notify', 'NotifyController@notify')->name('notify');
    Route::get('/operate_notify_url', 'NotifyController@operate_notify_url')->name('operate_notify_url');
    //单页面
    Route::get('/PaySuccess', 'AlipayPageController@PaySuccess')->name('PaySuccess');
    Route::get('/OrderErrors', 'AlipayPageController@OrderErrors')->name('OrderErrors');
    //创建订单确认金额视图路由
    Route::get('/create', 'AlipayOrderController@create')->name('create');
    //收款码
    Route::get('/skm', 'AlipayQrController@Skm')->name('skm');
    Route::get('/onlyskm', 'AlipayQrController@OnlySkm')->name('onlyskm');
});
//API
Route::group(['namespace' => 'Api'/*'middleware' => 'auth'*/, 'prefix' => 'admin/api'], function () {
    //收款码接口
    Route::post('/AlipayTradeCreate', 'AlipayTradeCreateController@AlipayTradeCreate')->name("AlipayTradeCreate");
    Route::post('/AlipayOqrCreate', 'AlipayTradeCreateController@AlipayOqrCreate')->name("AlipayOqrCreate");

    Route::any('/AlipayShopCategory', 'AlipayShopCategoryController@query')->name("AlipayShopCategory");
    Route::any('/getCategory', 'AlipayShopCategoryController@getCategory')->name("getCategory");
    Route::any('/getProvince', 'ProvinceCityController@getProvince')->name("getProvince");
    Route::any('/getCity', 'ProvinceCityController@getCity')->name("getCity");
    Route::any('/upload', 'PublicController@upload')->name("upload");
    Route::any('/uploadfile', 'PublicController@uploadfile')->name("uploadfile");
    Route::any('/SummaryBatchquery', 'AlipayQueryController@index')->name("ShopSummaryBatchquery");
    Route::any('/batchquery', 'AlipayQueryController@batchquery')->name("batchquery");
    Route::any('/ApplyOrderBatchQuery', 'AlipayQueryController@ApplyOrderBatchQuery')->name("ApplyOrderBatchQuery");
    Route::any('/ShopQueryDetail', 'AlipayQueryController@ShopQueryDetail')->name("ShopQueryDetail");
    Route::any('/QueryStatus', 'AlipayTradeQueryController@QueryStatus')->name("QueryStatus");
});
Route::group(['namespace' => 'Weixin', 'prefix' => 'admin/weixin'], function () {
    Route::any('/server', 'ServerController@server');
    Route::any('/oauth', 'OauthController@oauth');
    Route::any('/oauth_callback', 'OauthController@oauth_callback');
    Route::any('/orderview', 'WeixinPayController@orderview');
    Route::any('/order', 'WeixinPayController@order')->name('order');
    Route::any('/createorder', 'WeixinPayController@createOrder');
    Route::any('/ordernotify', 'WeixinPayController@ordernotify');
    Route::get('/spset', 'ServiceProviderController@spset')->name("spset");//服务商设置
});
