@extends('layouts.weixinpay')
@section('title','微信支付')
@section('css')
    <link href="{{asset('/css/weixinpay/wxpay.css')}}" rel="stylesheet">
@endsection
@section('content')
    <body ontouchstart class="weui-wepay-pay-wrap">
    <div class="msg_success">
        <div class="weui-msg">
            <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
            <div class="weui-msg__text-area">
                <h2 class="weui-msg__title">付款成功</h2>
                <p class="weui-msg__desc">欢迎下次光临</p>
            </div>
            <div class="weui-msg__opr-area">
                <p class="weui-btn-area">
                    <a href="javascript:;" class="weui-btn weui-btn_primary">确认</a>
                    <!-- <a href="javascript:history.back();" class="weui-btn weui-btn_default">辅助操作</a> -->
                </p>
            </div>
        </div>
        <div class="weui-wepay-logos weui-wepay-logos_ft">
            <i class="weui-wepay-logo-default weui-wepay-logo_gray"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    </body>


@endsection
