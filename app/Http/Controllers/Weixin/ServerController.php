<?php

namespace App\Http\Controllers\Weixin;
use Illuminate\Http\Request;
use EasyWeChat\Foundation\Application;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ServerController extends Controller
{
    //

    public function server()
    {

        $options = [
            'debug' => true,
            'app_id' => 'wx99dd6fe83cd87924',
            'secret' => 'ff48da35c0b54104396f43fff6c63d39',
            'token' => 'easywechat',
            // 'aes_key' => "hSOhQJytTWPaaMouzPjZcrhPPu4S3jDUjFfnF7LYG3b", // 可选
            'log' => [
                'level' => 'debug',
                'file' => storage_path().'/logs/easywechat.log', // XXX: 绝对路径！！！！
            ],
            //...
        ];
        $app = new Application($options);

        $server = $app->server;
        $user = $app->user;

        $server->setMessageHandler(function($message) use ($user) {
          //  $fromUser = $user->get($message->FromUserName);

           // return "{$fromUser->nickname} 您好！欢迎关注 overtrue!";

            return '您好！欢迎关注 ccmknt';
        });

      return  $server->serve()->send();
    }
}
