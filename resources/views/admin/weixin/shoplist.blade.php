@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <a href="{{route('WxAddShop')}}"><button class="btn btn-success " type="button"><span class="bold">添加商户</span></button></a>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>微信支付商户列表</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>店铺ID</th>
                                    <th>公众号ID</th>
                                    <th>店铺名称</th>
                                    <th>商户号</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($datapage)
                                    @foreach($datapage as $v)
                                        <tr>
                                            <td>w{{$v['store_id']}}</td>
                                            <td><span class="pie">{{$v['app_id']}}</span></td>
                                            <td>{{$v['store_name']}}</td>
                                            <td>{{$v['mch_id']}}</td>
                                            <td>{{$v['created_at']}}</td>
                                            <td>
                                               <a href="{{url('/admin/weixin/WxEditShop?id='.$v['id'])}}"><button class="btn btn-info " type="button"><i class="fa fa-paste"></i>编辑</button></a>
                                                <a class="btn btn-success" href="{{url('admin/weixin/WxPayQr?mch_id='.$v['mch_id'])}}">
                                                    <i class="fa fa-weixin"> </i> 收款码
                                                </a>
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
@endsection