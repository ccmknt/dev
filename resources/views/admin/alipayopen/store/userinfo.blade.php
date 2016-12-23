@extends('layouts.antui')
@section('content')
<div id="nowamagic"></div>
<div class="demo-content" id="remove">
    <input type="hidden" id="user_id" value="<?php echo $_GET['user_id']?>">
    <div class="am-list am-list-5lb form">
        <div class="am-list-header">请填写你的联系信息</div>
        <div class="am-list-body">
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">店铺名称</div>
                <div class="am-list-control">
                    <input  name="username" id="auth_shop_name" placeholder="请输入你的店铺名称" autocomplete="off" type="text">
                </div>
                <div class="am-list-clear"><i class="am-icon-clear am-icon" style="visibility: hidden;"></i></div>
            </div>
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">联系方式</div>
                <div class="am-list-control">
                    <input type="text" id="auth_phone" placeholder="请输入你的联系方式">
                </div>
                <div class="am-list-clear"><i class="am-icon-clear am-icon" style="visibility: hidden;"></i></div>
            </div>
        </div>
    </div>
</div>
<button type="button" class="am-button blue" id="remove1" onclick="sub()">确认信息</button>
@endsection
@section('js')
<script>
    function sub() {
        var t=$("#auth_shop_name").val();
        if(t.length==0){

        }else {
            $.post("{{route('userinfo')}}", {user_id: $("#user_id").val(),_token:"{{csrf_token()}}",auth_shop_name:$("#auth_shop_name").val(),auth_phone:$("#auth_phone").val()}, function (result) {
                if(result.code==200){
                    $("#remove").remove();
                    $("#remove1").remove();
                    $("#nowamagic").append('<div class="am-message result"> <i class="am-icon result wait"></i> <div class="am-message-main">等待</div> <div class="am-message-sub">已提交成功，等待支付宝口碑处理</div> </div>');
                }
            },"json")
        }

    }
</script>
@endsection

