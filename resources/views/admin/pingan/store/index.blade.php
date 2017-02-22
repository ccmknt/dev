@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <a href="{{route('PingAnStoreAdd')}}">
            <button class="btn btn-success " type="button"><span class="bold">添加商户</span></button>
        </a>
        <button type="button" class="btn btn-outline btn-default">还原商户</button>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>门店列表</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>商户id</th>
                                    <th>商户全称</th>
                                    <th>商户简称</th>
                                    <th>联系人名称</th>
                                    <th>联系人手机号</th>
                                    <th>状态</th>
                                    <th>归属员工</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($datapage)
                                    @foreach($datapage as $v)
                                        <tr>
                                            <td>{{$v['external_id']}}</td>
                                            <td><span class="pie">{{$v['name']}}</span></td>
                                            <td><span class="pie">{{$v['alias_name']}}</span></td>
                                            <td>{{$v['contact_name']}}</td>
                                            <td><span class="pie">{{$v['contact_mobile']}}</span></td>
                                            <td><span class="pie">{{$v['status']}}</span></td>
                                            <td><span class="pie">{{$v['user_name']}}</span></td>
                                            <td>
                                                <a href="{{url('admin/pingan/SetStore?id='.$v['id'])}}"><button type="button" class="btn btn-outline btn-primary">商户设置</button></a>
                                                <a href="{{url('admin/pingan/setMerchantRate?id='.$v['id'])}}"> <button type="button" class="btn btn-outline btn-info">费率调整</button></a>
                                                <button onclick='del("{{$v['id']}}")' type="button"
                                                        class="btn btn-outline btn-warning">删除
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
                        没有任何记录
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
@section('js')
    <script>
        function del(id) {
            //询问框
            layer.confirm('确定要删除', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{route('DelPinanStore')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            window.location.href = "{{route('PingAnStoreIndex')}}";
                        }, "json");
            }, function () {

            });
        }
    </script>

@endsection