@extends('layouts.antui')

@section('content')
    @if($code=="6001")
        <div class="am-message result">
            <i class="am-icon result error"></i>
            <div class="am-message-main">失败</div>
            <div class="am-message-sub">已经取消订单付款</div>
        </div>
    @else
        <div class="am-message result">
            <i class="am-icon result error"></i>
            <div class="am-message-main">失败</div>
            <div class="am-message-sub">交易失败请重新付款</div>
        </div>
    @endif
@endsection