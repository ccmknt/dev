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

//需要登陆
Route::group(['middleware' => 'auth'], function () {
    Route::post('/updateInfo', 'AppController@updateInfo')->name("updateInfo");
    Route::post('/appUpdateFile', 'AppController@appUpdateFile')->name("appUpdateFile");
    Route::get('/setApp', 'AppController@setApp')->name("setApp");
    Route::post('/setAppPost', 'AppController@setAppPost')->name("setAppPost");
});


//要登录的链接
Route::group(['namespace' => 'AlipayOpen', 'middleware' => 'auth', 'prefix' => 'admin/alipayopen'], function () {
    Route::resource('/store', 'StoreController');
    //admin 管理用户模块
    Route::get('/users', 'UsersController@users')->name('users');
    Route::get('/updateu', 'UsersController@updateu')->name('updateu');
    Route::post('/useradd', 'UsersController@useradd')->name('useradd');
    Route::post('/updateuSave', 'UsersController@updateuSave')->name('updateuSave');
    Route::post('/deleteu', 'UsersController@deleteu')->name('deleteu');
    //后台配置模块
    Route::get('/isvconfig', 'AlipayIsvConfigController@isvconfig')->name('isvconfig');
    Route::post('/saveconfig', 'AlipayIsvConfigController@saveconfig')->name('saveconfig');
    //商户业务流水操作查询
    Route::get('/ApplyorderBatchquery', 'ApplyorderBatchqueryController@query')->name('ApplyorderBatchquery');
    Route::get('/alipaytradelist', 'AlipayTradeListController@index')->name('alipaytradelist');
    //授权列表
    Route::get('/oauthlist', 'OauthController@oauthlist')->name('oauthlist');
    Route::get('/updateOauthUser', 'OauthController@updateOauthUser')->name('updateOauthUser');
    Route::post('/updateOauthUserPost', 'OauthController@updateOauthUserPost')->name('updateOauthUserPost');

    //各种提醒设置
    Route::post('/shopNotify', 'AlipayReturnController@shopNotify')->name('shopNotify');
    Route::get('/setWxNotify', 'AlipayReturnController@setWxNotify')->name('setWxNotify');
    Route::post('/setWxNotifyPost', 'AlipayReturnController@setWxNotifyPost')->name('setWxNotifyPost');


    //权限管理
    Route::resource('/role', 'RoleController');
    Route::resource('/permission', 'PermissionController');
    Route::get('/assignment', 'RolePermissionController@assignment')->name('assignment');
    Route::post('/assignmentpost', 'RolePermissionController@assignmentpost')->name('assignmentpost');
    Route::post('/delRole', 'RolePermissionController@delRole')->name('delRole');
    Route::get('/setRole', 'RolePermissionController@setRole')->name('setRole');
    Route::post('/setRolePost', 'RolePermissionController@setRolePost')->name('setRolePost');


});
Route::group(['namespace' => 'AlipayOpen'], function () {
    Route::get('/callback', 'OauthController@callback');
    Route::get('alipayopen/userinfo', 'OauthController@userinfo');
    Route::post('alipayopen/userinfoinsert', 'OauthController@userinfoinsert')->name('userinfo');
});

//支付宝通知页面
Route::group(['namespace' => 'AlipayOpen'], function () {
    Route::any('/notify', 'NotifyController@notify')->name('notify');
    Route::any('/operate_notify_url', 'NotifyController@operate_notify_url')->name('operate_notify_url');
    Route::any('/alipay_notify', 'NotifyController@alipay_notify')->name('alipay_notify');
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
    //单页面
    Route::get('/PaySuccess', 'AlipayPageController@PaySuccess')->name('PaySuccess');
    Route::get('/OrderErrors', 'AlipayPageController@OrderErrors')->name('OrderErrors');
    //创建订单确认金额视图路由
    Route::get('/create', 'AlipayOrderController@create')->name('create');
    //收款码
    Route::get('/skm', 'AlipayQrController@Skm')->name('skm');
    Route::get('/onlyskm', 'AlipayQrController@OnlySkm')->name('onlyskm');

    //员工推广界面提交
    Route::get('/selfserviceadd', 'SelfServiceShosController@selfserviceadd')->name('selfserviceadd');
    Route::post('/selfshoppost', 'SelfServiceShosController@SelfShopPost')->name('SelfShopPost');
});
//API
Route::group(['namespace' => 'Api'/*'middleware' => 'auth'*/, 'prefix' => 'admin/api'], function () {
    //收款码接口
    Route::post('/AlipayTradeCreate', 'AlipayTradeCreateController@AlipayTradeCreate')->name("AlipayTradeCreate");
    Route::post('/AlipayOqrCreate', 'AlipayTradeCreateController@AlipayOqrCreate')->name("AlipayOqrCreate");
    Route::post('/AlipayqrCreate', 'AlipayTradeCreateController@AlipayqrCreate')->name("AlipayqrCreate");
    Route::any('/getProvince', 'ProvinceCityController@getProvince')->name("getProvince");
    Route::any('/getCity', 'ProvinceCityController@getCity')->name("getCity");
    Route::any('/getCategory', 'AlipayShopCategoryController@getCategory')->name("getCategory");
    Route::any('/OrderStatus', 'AlipayTradeCreateController@OrderStatus')->name("OrderStatus");
    Route::any('/uploadImagePingAn', 'PublicController@uploadImagePingAn')->name("uploadImagePingAn");//上传至服务器

});
//API  AUTH
Route::group(['namespace' => 'Api', 'middleware' => 'auth', 'prefix' => 'admin/api'], function () {
    Route::any('/QueryStatus', 'AlipayTradeQueryController@QueryStatus')->name("QueryStatus");
    Route::any('/AlipayShopCategory', 'AlipayShopCategoryController@query')->name("AlipayShopCategory");
    /*Route::any('/getProvince', 'ProvinceCityController@getProvince')->name("getProvince");
    Route::any('/getCity', 'ProvinceCityController@getCity')->name("getCity");*/
    Route::any('/upload', 'PublicController@upload')->name("upload");
    Route::any('/uploadlocal', 'PublicController@uploadlocal')->name("uploadlocal");
    Route::any('/uploadfile', 'PublicController@uploadfile')->name("uploadfile");
    Route::any('/SummaryBatchquery', 'AlipayQueryController@index')->name("ShopSummaryBatchquery");
    Route::any('/batchquery', 'AlipayQueryController@batchquery')->name("batchquery");
    Route::any('/ApplyOrderBatchQuery', 'AlipayQueryController@ApplyOrderBatchQuery')->name("ApplyOrderBatchQuery");
    Route::any('/ShopQueryDetail', 'AlipayQueryController@ShopQueryDetail')->name("ShopQueryDetail");
});
//微信
Route::group(['namespace' => 'Weixin', 'prefix' => 'admin/weixin'], function () {
    Route::any('/server', 'ServerController@server');
    Route::any('/oauth', 'OauthController@oauth');
    Route::any('/oauth_callback', 'OauthController@oauth_callback');
    Route::any('/orderview', 'WeixinPayController@orderview');
    Route::any('/order', 'WeixinPayController@order')->name('order');
    Route::any('/createorder', 'WeixinPayController@createOrder');
    Route::any('/ordernotify', 'WeixinPayController@ordernotify');
    Route::get('/paySuccess', 'WeixinPayController@paySuccess')->name('paySuccess');

});
//需要登陆
Route::group(['namespace' => 'Weixin', 'middleware' => 'auth', 'prefix' => 'admin/weixin'], function () {
    //服务商设置
    Route::get('/spset', 'ServiceProviderController@spset')->name("spset");
    Route::post('/spsetPost', 'ServiceProviderController@spsetPost')->name("spsetPost");
    //商户添加
    Route::get('/shopList', 'ShopsListsController@index')->name("WxShopList");
    Route::get('/WxAddShop', 'ShopsListsController@WxAddShop')->name("WxAddShop");
    Route::post('/WxShopPost', 'ShopsListsController@WxShopPost')->name("WxShopPost");
    Route::get('/WxEditShop', 'ShopsListsController@WxEditShop')->name("WxEditShop");
    Route::post('/WxEditShopPost', 'ShopsListsController@WxEditShopPost')->name("WxEditShopPost");
    Route::get('/WxPayQr', 'ShopsListsController@WxPayQr')->name("WxPayQr");
    Route::get('/WxOrder', 'ShopsListsController@WxOrder')->name("WxOrder");
});
//设置
Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
    Route::get('/set', 'PageSetsController@setPage');
    Route::post('/setPagePost', 'PageSetsController@setPagePost')->name('setPagePost');
});
//支付宝微信 二码合一需要登陆
Route::group(['namespace' => 'AlipayWeixin', 'middleware' => 'auth', 'prefix' => 'admin/alipayweixin'], function () {
    //服务商设置
    Route::get('/AlipayWexinLists', 'AlipayWeixinController@AlipayWexinLists')->name("AlipayWexinLists");
    Route::get('/addAliPayWeixinStore', 'AlipayWeixinController@addAliPayWeixinStore')->name("addAliPayWeixinStore");
    Route::post('/addAliPayWeixinStorePost', 'AlipayWeixinController@addAliPayWeixinStorePost')->name("addAliPayWeixinStorePost");
    Route::post('/delAlipayWexin', 'AlipayWeixinController@delAlipayWexin')->name("delAlipayWexin");
    Route::get('/qr', 'AlipayWeixinController@qr');

});

//平安银行 需要登录
Route::group(['namespace' => 'PingAn', 'middleware' => 'auth', 'prefix' => 'admin/pingan'], function () {
    Route::get('/index', 'StoreController@index')->name('PingAnStoreIndex');
    Route::get('/add', 'StoreController@add')->name('PingAnStoreAdd');
    Route::post('/addPost', 'StoreController@addpost')->name('PingAnStoreAddPost');
    Route::post('/DelPinanStore', 'StoreController@DelPinanStore')->name('DelPinanStore');
    Route::post('/PayStatus', 'StoreController@PayStatus')->name('PayStatus');
    Route::get('/SetStore', 'StoreController@SetStore')->name('SetStore');
    Route::post('/SetStorePost', 'StoreController@SetStorePost')->name('SetStorePost');
    Route::get('/setMerchantRate', 'StoreController@setMerchantRate')->name('setMerchantRate');
    Route::post('/setMerchantRatePost', 'StoreController@setMerchantRatePost')->name('setMerchantRatePost');
    Route::get('/PingAnStoreQR', 'StoreController@PingAnStoreQR')->name('PingAnStoreQR');
    Route::get('/OrderQuery', 'StoreController@OrderQuery')->name('PingAnOrderQuery');
    //空码生成
    Route::get('/QrLists', 'PinganQrController@QrLists')->name('QrLists');
    Route::post('/createQr', 'PinganQrController@createQr')->name('createQr');
    Route::any('/DownloadQr', 'PinganQrController@DownloadQr')->name('DownloadQr');

    //二维码跳转地址
    Route::any('/Qrcode', 'PinganQrController@Qrcode')->name('Qrcode');

    //通道配置模块
    Route::get('/pinganconfig', 'PingAnConfigController@pinganconfig')->name('pinganconfig');
    Route::post('/savepinganconfig', 'PingAnConfigController@savepinganconfig')->name('savepinganconfig');


    //支付宝 微信
    Route::get('/alipay', 'AlipayController@alipay');

    //商户资料
    Route::get('/MerchantFile', 'StoreController@MerchantFile')->name('MerchantFile');
});

//平安银行 不要登录可访问
Route::group(['namespace' => 'PingAn', 'prefix' => 'admin/pingan'], function () {
    //支付宝 微信
    Route::get('/alipay', 'AlipayController@alipay');
    Route::post('/PingAnAlipay', 'AlipayController@PingAnAlipay')->name('PingAnAlipay');
    Route::get('/ReturnStatus', 'AlipayController@ReturnStatus')->name('ReturnStatus');
    Route::get('/PaySuccess', 'AlipayController@PaySuccess');
    Route::get('/weixin/orderview', 'WeiXinController@orderview');
    Route::post('/PAWxOrder', 'WeiXinController@PAWxOrder')->name('PAWxOrder');
    Route::any('/notify_url', 'NotifyController@notify_url');
    Route::any('/wx_notify_url', 'NotifyController@wx_notify_url');

});

//链接
Route::group(['namespace' => 'PingAn'], function () {
    Route::get('/Qrcode', 'PinganQrController@QrCode');
});
//商户自助提交
Route::group(['namespace' => 'PingAn', 'prefix' => 'admin/pingan'], function () {
    Route::get('/autoStore', 'StoreController@autoStore')->name('autoStore');
    Route::post('/autoStorePost', 'StoreController@autoStorePost')->name('autoStorePost');
    Route::get('/success', 'StoreController@success')->name('PingAnSuccess');
    Route::get('/autom', 'StoreController@autom')->name('autom');
    Route::post('/automPost', 'StoreController@automPost')->name('automPost');
    Route::get('/autoFile', 'StoreController@autoFile')->name('autoFile');
    Route::post('/autoFilePost', 'StoreController@autoFilePost')->name('autoFilePost');

});
//商户
Route::group(['namespace' => 'Merchant', 'prefix' => 'merchant', 'middleware' => 'auth.merchant'], function () {
    Route::any('logout', 'LoginController@logout');
    Route::get('index', 'IndexController@index');
});
//商户
Route::group(['namespace' => 'Merchant', 'prefix' => 'merchant'], function () {
    Route::get('login', 'LoginController@showLoginForm')->name('merchantLogin');
    Route::post('login', 'LoginController@login');
    Route::get('register', 'RegisterController@showRegister')->name('showRegister');
    Route::post('register1', 'RegisterController@register1');

});