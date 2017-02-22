@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <a href="{{route('addAliPayWeixinStore')}}">
            <button class="btn btn-success " type="button"><span class="bold">添加商户</span></button>
        </a>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>支付宝微信二维码合一商户</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>店铺名称</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($datapage)
                                    @foreach($datapage as $v)
                                        <tr>
                                            <td>{{$v['alipay_auth_shop_name']}}</td>
                                            <td>
                                                <a href="{{url('admin/alipayweixin/qr?id='.$v['id'])}}">
                                                    <button type="button" class="btn  btn-success">二码合一</button>
                                                </a>
                                                <button type="button" onclick='del("{{$v['id']}}")'
                                                        class="btn btn-outline btn-danger">删除
                                                </button>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers"
                                     id="DataTables_Table_0_paginate">
                                    {{$paginator->render()}}
                                </div>
                            </div>
                        </div>
                        @else
                            没有记录

                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('js')
    <script>
        function del(id) {
            layer.confirm('确定删除', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{route('delAlipayWexin')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            window.location.href = "{{route('AlipayWexinLists')}}";
                        }, 'json');
            }, function () {

            });
        }
    </script>
@endsection