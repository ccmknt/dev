<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/21
 * Time: 15:33
 */

namespace App\Http\Controllers\PingAn;


use Alipayopen\Sdk\AopClient;
use App\Models\PageSets;
use App\Models\PinganConfig;
use App\Models\PinganTradeQueries;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotifyController extends BaseController
{

    //支付宝异步通知
    public function notify_url(Request $request)
    {
        $check = $this->Check($request->all());
        Log::info('A_' . $check);
        if ($check) {
            //改变状态数据库的状态
            $data = $request->all();
            $PinganTradeQuery = PinganTradeQueries::where('trade_no', $data['trade_no'])->first();
            //如果状态不相同修改数据库状态
            if ($PinganTradeQuery->status != $data['trade_status']) {
                PinganTradeQueries::where('trade_no', $data['trade_no'])->update([
                    'status' => $data['trade_status'],
                    'total_amount' => $data['total_amount'],
                ]);
                //微信通知商户收营员
                try {
                    //店铺通知微信
                    if ($data['trade_status'] == 'TRADE_SUCCESS') {
                        $store_id = $PinganTradeQuery->store_id;
                        $WeixinPayNotifyStore = WeixinPayNotify::where('store_id', $store_id)->first();
                        //实例化
                        $config = WeixinPayConfig::where('id', 1)->first();
                        $options = [
                            'app_id' => $config->app_id,
                            'secret' => $config->secret,
                            'token' => '18851186776',
                            'payment' => [
                                'merchant_id' => $config->merchant_id,
                                'key' => $config->key,
                                'cert_path' => $config->cert_path, // XXX: 绝对路径！！！！
                                'key_path' => $config->key_path,      // XXX: 绝对路径！！！！
                                'notify_url' => $config->notify_url,       // 你也可以在下单时单独设置来想覆盖它
                            ],
                        ];
                        $app = new Application($options);
                        $userService = $app->user;
                        $template = PageSets::where('id', 1)->first();
                        $notice = $app->notice;
                        $userIds = $WeixinPayNotifyStore->receiver;
                        $open_ids = explode(",", $userIds);
                        $templateId = $template->string1;
                        $url = $WeixinPayNotifyStore->linkTo;
                        $color = $WeixinPayNotifyStore->topColor;
                        $data = array(
                            "keyword1" => $PinganTradeQuery->total_amount,
                            "keyword2" => '支付宝(' . $data['buyer_logon_id'] . ')',
                            "keyword3" => '' . $PinganTradeQuery->updated_at . '',
                            "keyword4" => $data['trade_no'],
                            "remark" => '祝' . $WeixinPayNotifyStore->store_name . '生意红火',
                        );
                        foreach ($open_ids as $v) {
                            $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($v)->send();
                        }

                    }

                } catch (\Exception $exception) {
                    Log::info($exception);
                    return json_encode([
                        'status' => 1,
                    ]);
                }
            }

        }

        return 'success';

    }

    //微信异步通知
    public function wx_notify_url(Request $request)
    {
        $check = $this->Check($request->all());
        Log::info('w_' . $check);
        if ($check) {
            //改变状态数据库的状态
            $data = $request->all();
            $PinganTradeQuery = PinganTradeQueries::where('out_trade_no', $data['out_trade_no'])->first();
            //通过接口查状态
            $aop = $this->AopClient();
            $aop->method = "fshows.liquidation.alipay.trade.query";
            $payStatus = [
                'trade_no' => $data['transaction_id']
            ];
            $dataStatus = array('content' => json_encode($payStatus));
            try {
                $response = $aop->execute($dataStatus);
                $responseArray = json_decode($response, true);
            } catch (\Exception $exception) {
                Log::info($exception);
            }
            //如果状态不相同修改数据库状态
            if ($PinganTradeQuery->status != $responseArray['return_value']['trade_state']) {
                PinganTradeQueries::where('out_trade_no', $data['out_trade_no'])->update([
                    'status' =>$responseArray['return_value']['trade_state'],
                    'total_amount' => $responseArray['return_value']['total_fee'],
                ]);
                //微信通知商户收营员
                try {
                    //店铺通知微信
                    if ($responseArray['return_value']['trade_state']== 'SUCCESS') {
                        $store_id = $PinganTradeQuery->store_id;
                        $WeixinPayNotifyStore = WeixinPayNotify::where('store_id', $store_id)->first();
                        //实例化
                        $config = WeixinPayConfig::where('id', 1)->first();
                        $options = [
                            'app_id' => $config->app_id,
                            'secret' => $config->secret,
                            'token' => '18851186776',
                            'payment' => [
                                'merchant_id' => $config->merchant_id,
                                'key' => $config->key,
                                'cert_path' => $config->cert_path, // XXX: 绝对路径！！！！
                                'key_path' => $config->key_path,      // XXX: 绝对路径！！！！
                                'notify_url' => $config->notify_url,       // 你也可以在下单时单独设置来想覆盖它
                            ],
                        ];
                        $app = new Application($options);
                        $userService = $app->user;
                        $user = $userService->get($responseArray['return_value']['openid']);//买家open_id
                        $template = PageSets::where('id', 1)->first();
                        $notice = $app->notice;
                        $userIds = $WeixinPayNotifyStore->receiver;
                        $open_ids = explode(",", $userIds);
                        $templateId = $template->string1;
                        $url = $WeixinPayNotifyStore->linkTo;
                        $color = $WeixinPayNotifyStore->topColor;
                        $andData= array(
                            "keyword1" => $PinganTradeQuery->total_amount,
                            "keyword2" => '微信(' . $user->nickname. ')',
                            "keyword3" => '' . $PinganTradeQuery->updated_at . '',
                            "keyword4" => $responseArray['return_value']['trade_no'],
                            "remark" => '祝' . $WeixinPayNotifyStore->store_name . '生意红火',
                        );
                        foreach ($open_ids as $v) {
                            $notice->uses($templateId)->withUrl($url)->andData($andData)->andReceiver($v)->send();
                        }

                    }

                } catch (\Exception $exception) {
                    Log::info($exception);
                    return json_encode([
                        'status' => 1,
                    ]);
                }
            }

        }
        return 'success';

    }

    public function Check($request)
    {
        $config = PinganConfig::where('id', 1)->first();
        //支付异步通知
        $a = $this->AopClient();
        $aop = new AopClient();
        $aop->appId = $a->appId;
        $aop->rsaPrivateKey = $a->rsaPrivateKey;
        $aop->gatewayUrl = $a->gatewayUrl;
        $aop->signType = 'RSA';
        $aop->alipayrsaPublicKey = $config->pinganrsaPublicKey;
        $true = $aop->rsaCheckUmxnt($request, $config->pinganrsaPublicKey, 'RSA');
        return $true;
    }


}