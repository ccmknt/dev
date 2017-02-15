@extends('layouts.koubei')
@section('title')
    {{$shop['auth_shop_name']}}
@endsection
@section('content')
    <div class="main">
        <p class="cite">
		<span>
			<img src="{{url('/img/site.jpg')}}">
		</span>{{$shop['auth_shop_name']}}
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
        <button type="button" id="payLogButton" class="btn db" style="font-size: 18px;">和店员已确认，立即买单</button>
        <input type="hidden" value="{{$shop['user_id']}}" id="u_id">
        <input type="hidden" id="token" value="{{csrf_token()}}">
@endsection
@section('js')
    <script>
        document.addEventListener('AlipayJSBridgeReady', function () {
            $("#payLogButton").click(function () {
                $.post("{{route('AlipayOqrCreate')}}", {
                    total_amount: $("#total_amount").val(),
                    u_id: $("#u_id").val(),
                    _token: $("#token").val()
                }, function (data) {
                    if (data.status == 1) {
                        AlipayJSBridge.call("tradePay", {
                            tradeNO: data.trade_no
                        }, function (result) {
                            //更新状态
                            $.post("{{route('OrderStatus')}}", {
                                        trade_no: data.trade_no,
                                        resultCode: result.resultCode,
                                        _token: $("#token").val()
                                    },
                                    function (dataStatus) {
                                        //付款成功
                                        if (result.resultCode == "9000") {
                                            window.location.href = "{{url('admin/alipayopen/PaySuccess?price=')}}" + $("#total_amount").val();
                                        }
                                        if (result.resultCode == "6001") {
                                            window.location.href = "{{url('admin/alipayopen/OrderErrors?code=6001')}}";
                                        }
                                    }, "json");

                        });
                    } else {
                        window.location.href = "{{url('admin/alipayopen/OrderErrors')}}";
                    }
                }, "json");
            });
        }, false);
    </script>
@endsection



