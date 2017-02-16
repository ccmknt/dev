<?php

namespace App\Http\Controllers\Weixin;

use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayShopLists;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use Illuminate\Http\Request;
use EasyWeChat\Foundation\Application;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ServerController extends Controller
{
    //

    public function server()
    {

        //实例化
        $config = WeixinPayConfig::where('id', 1)->first();
        $options = [
            'app_id' => $config->app_id,
            'secret' => $config->secret,
            'token'=>'18851186776',
            'payment' => [
                'merchant_id' => $config->merchant_id,
                'key' => $config->key,
                'cert_path' => $config->cert_path, // XXX: 绝对路径！！！！
                'key_path' => $config->key_path,      // XXX: 绝对路径！！！！
                'notify_url' => $config->notify_url,       // 你也可以在下单时单独设置来想覆盖它
            ],
        ];
        $app = new Application($options);

        $server = $app->server;
        $user = $app->user;
        $server->setMessageHandler(function ($message) use ($user) {
            $open_id = $message->FromUserName;//获得发信息的open_id
            $substr = substr($message->Content, 0, 1);
            try {
                if ($substr == "o") {
                    $s1 = AlipayAppOauthUsers::where('user_id', substr($message->Content, 1))->first();
                    $store_id = 'o' . $s1->user_id;
                    $store_name = $s1->auth_shop_name;
                } else {
                    $s2 = AlipayShopLists::where('store_id', $message->Content)->first();
                    $store_id = $s2->store_id;
                    $store_name = $s2->main_shop_name;
                }
                if ($s1 or $s2) {
                    $WeixinPayNotify = WeixinPayNotify::where('store_id', $store_id)->first();
                    if ($WeixinPayNotify) {
                        if ($WeixinPayNotify->receiver) {
                            $open_ids = explode(",", $WeixinPayNotify->receiver);
                            if (in_array($open_id, $open_ids)) {
                                return '你已经绑定过' . $store_name . '收银提醒,不需要重复绑定！';
                            }
                            $ids = $WeixinPayNotify->receiver . ',' . $open_id;
                            WeixinPayNotify::where('store_id', $store_id)->update([
                                'receiver' => $ids,
                            ]);
                        } else {
                            WeixinPayNotify::where('store_id', $store_id)->update([
                                'receiver' => $open_id,
                            ]);
                        }

                    } else {
                        WeixinPayNotify::create([
                            'receiver' => $open_id,
                        ]);
                    }

                    return '你成功绑定' . $store_name . '收银提醒';

                }
            } catch (\Exception $exception) {
                return '绑定失败店铺';
            }
        });

        return $server->serve()->send();
    }
}
