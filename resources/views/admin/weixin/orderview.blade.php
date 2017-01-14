@extends('layouts.weixinpay')
@section('title','微信支付')
@section('css')
    <link href="{{asset('/css/weixinpay/wxpay.css')}}" rel="stylesheet">
@endsection
@section('content')
    <body ontouchstart>
    <input type="hidden" id="sub_merchant_id" value="<?php echo $_GET['sub_merchant_id']?>">
    <div class="weui-wepay-pay__ft">
        <p class="weui-wepay-pay__info" style="font-size: 18px;">{{$shop->store_name}}</p>
    </div>
    <div class="weui-wepay-pay">
        <div class="weui-wepay-pay__bd">
            <div class="weui-wepay-pay__inner">
                <h1 class="weui-wepay-pay__title">付款金额(元)</h1>
                <div class="weui-wepay-pay__inputs"><strong class="weui-wepay-pay__strong">￥</strong>
                    <input type="number" class="weui-wepay-pay__input" id="total_fee" placeholder="请输入金额"></div>
                <div class="weui-wepay-pay__intro">可询问服务员消费总额</div>
            </div>
        </div>
        <div class="weui-wepay-pay__ft">
            <div class="weui-wepay-pay__btn">
                <button  onclick="callpay()" class="weui-btn weui-btn_primary">立即支付</button>
            </div>

        </div>
    </div>
    </body>
    <script>
        //调用微信JS api 支付
        function onBridgeReady() {
            $.post("{{route('order')}}", {
                        sub_merchant_id: $("#sub_merchant_id").val(),
                        _token: "{{csrf_token()}}",
                        total_fee: $("#total_fee").val()
                    },
                    function (data) {
                        WeixinJSBridge.invoke(
                                'getBrandWCPayRequest', data,
                                function (res) {
                                    if (res.err_msg == "get_brand_wcpay_request:ok") {
                                        // 使用以上方式判断前端返回,微信团队郑重提示：
                                        // res.err_msg将在用户支付成功后返回
                                        // ok，但并不保证它绝对可靠。
                                    }
                                }
                        );
                    }, 'json');
        }
        function callpay() {
            if (typeof WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                    document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
                } else if (document.attachEvent) {
                    document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                    document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
                }
            } else {
                onBridgeReady();
            }
        }
    </script>

@endsection
