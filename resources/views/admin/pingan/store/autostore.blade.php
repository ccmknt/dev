@extends('layouts.publicStyle')
@section('title','商户注册门店')
@section('css')
@endsection
@section('content')
    <script src="{{asset('/js/check.js')}}" type="text/javascript"></script>
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>商户店铺资料</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            <input type="hidden" name="external_id" id="external_id"
                                   value="<?php echo 'p' . date('YmdHis', time())?>">
                            <input type="hidden" name="user_id" id="user_id"
                                   value="<?php echo $_GET['user_id']?>">
                            <input type="hidden" name="code_number" id="code_number"
                                   value="<?php echo $_GET['code_number']?>">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>门店分类</label>
                                <div class="col-sm-10">
                                    <select class="form-control m-b" name="category_id" id="category_id">
                                        <option>请选择分类</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户全称</label>
                                <input placeholder="商户全称,须与商户相关执照一致" class="form-control" name="name" id="name"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户简称</label>
                                <input required="required" placeholder="商户简称,在支付宝、微信支付时展示" class="form-control"
                                       name="alias_name" id="alias_name"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系人名称</label>
                                <input required="required" placeholder="联系人名称" class="form-control" name="contact_name"
                                       id="contact_name"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系电话</label>
                                <input required="required" placeholder="联系电话" class="form-control"
                                       name="service_phone" id="service_phone" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                        </form>
                    </div>
                </div>
                <a href="javascript:void(0)" onclick="addpost()">
                    <button style="width: 100%;height: 40px;font-size: 18px;" type="button" class="btn btn-primary">
                        下一步绑定银行卡号
                    </button>
                </a>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')
    <script>
        function addpost() {
            if (!IsTel($("#service_phone").val())) {
                layer.msg('手机号码不正确');
                return false;
            }
            $.post("{{route("autoStorePost")}}",
                    {
                        _token: '{{csrf_token()}}',
                        category_id: $("#category_id").val(),
                        name: $("#name").val(),
                        user_id: $('#user_id').val()
                        ,
                        alias_name: $("#alias_name").val(),
                        service_phone: $("#service_phone").val(),
                        contact_name: $("#contact_name").val()
                        ,
                        external_id: $("#external_id").val(),

                    },
                    function (result) {
                        if (result.success) {
                            window.location.href = "{{url('admin/pingan/autom?external_id=')}}" + $("#external_id").val() + '&code_number=' + $("#code_number").val();
                        } else {
                            layer.msg(result.error_message);
                        }
                    }, "json")

        }
        window.onload = get;
        function get() {
            getCategory();
        }
        //获得分类
        function getCategory() {
            $.post("{{route("getCategory")}}", {_token: $("#token").val()}, function (data) {
                for (var key in data) {
                    var selObj = $("#category_id");
                    var value = data[key].category_id;
                    var text = data[key].link;
                    selObj.append("<option value='" + value + "'>" + text + "</option>");
                }
            }, "json");
        }
    </script>

@endsection
@endsection