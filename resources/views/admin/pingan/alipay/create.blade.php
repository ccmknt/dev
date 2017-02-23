@extends('layouts.koubei')
@section('title')
    {{$shop['main_shop_name']}}
@endsection
@section('content')
    <div class="main">
        <p class="cite">
		<span>
			<img src="{{url('/img/site.jpg')}}">
		</span>{{$shop['main_shop_name']}}
        </p>
        <div class="type">
            <div class="top clear">
                <span>消费总金额（元）</span>
                <input id="total_amount" value="" type="number" placeholder="请询问服务员后输入">
            </div>
            <div class="bot clear">
                <span class="no">不参与优惠金额（元）</span>
                <input type="number" placeholder="请询问服务员后输入">
            </div>
        </div>
        <div class="type type_bot">
            <div class="bot clear">
                <span>选填备注</span>
                <input type="text" placeholder="如包房号、服务员号等" class="notice">
            </div>
        </div>
        {{--   <p class="sale">商家优惠</p>
           <p class="down">8.5折</p>--}}
        <button type="button" onclick="pay()" class="btn db" style="font-size: 18px;">和店员已确认，立即买单</button>
        <input type="hidden" value="<?php echo $_GET['u_id']?>" id="u_id">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        @endsection
        @section('js')
            <script>
                function pay() {
                    $.post("{{route('PingAnAlipay')}}", {
                        total_amount: $("#total_amount").val(),
                        u_id: $("#u_id").val(),
                        _token: $("#token").val()
                    }, function (data) {
                        if (data.success) {
                            window.location.href = 'https://openapi-liquidation.51fubei.com/alipayPage/?prepay_id=' + data.return_value.prepay_id + '&callback_url='+'{{url('/admin/pingan/ReturnStatus')}}'+'&trade_no=' + data.return_value.trade_no;
                        } else {
                            window.location.href = "{{url('admin/alipayopen/OrderErrors')}}";
                        }
                    }, "json");
                }
            </script>
@endsection



