@extends('layouts.antui')
@section('content')
    <div class="am-list form">
        <div class="am-list-header">收款方:{{$shop['auth_shop_name']}}</div>
        <div class="am-list-body">
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">支付金额</div>
                <div class="am-list-control">
                    <input placeholder="请输入付款金额" id="total_amount" value="" type="number">
                </div>
                <div class="am-list-clear"><i class="am-icon-clear am-icon" style="visibility: hidden;"></i></div>
            </div>
        </div>
    </div>
    <input type="hidden" value="{{$shop['user_id']}}" id="u_id">
    <button type="button" id="payLogButton" class="am-button blue">确认付款</button>
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
                            //付款成功
                            if (result.resultCode=="9000") {
                           window.location.href="{{url('admin/alipayopen/PaySuccess?price=')}}"+$("#total_amount").val();
                            }
                            if(result.resultCode=="6001"){
                                window.location.href = "{{url('admin/alipayopen/OrderErrors?code=6001')}}";
                            }
                        });
                    } else {
                        window.location.href = "{{url('admin/alipayopen/OrderErrors')}}";
                    }
                }, "json");
            });
        }, false);
    </script>
@endsection


