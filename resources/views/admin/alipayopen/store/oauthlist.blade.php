@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>商户第三方门店授权</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>店铺id</th>
                                    <th>店铺名称</th>
                                    <th>联系电话</th>
                                    <th>授权时间</th>
                                    <th>更新时间</th>
                                    <th>归属员工</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($datapage)
                                    @foreach($datapage as $v)
                                        <tr>
                                            <td>o{{$v->user_id}}</td>
                                            <td>{{$v->auth_shop_name}}</td>
                                            <td><span class="pie">{{$v->auth_phone}}</span></td>
                                            <td>{{$v->created_at}}</td>
                                            <td>{{$v->updated_at}}</td>
                                            <td>{{$v->name}}</td>
                                            <td>

                                                <a href="{{url('/admin/alipayopen/store/create?app_auth_token='.$v->app_auth_token)}}">
                                                    <button type="button" class="btn  btn-success">口碑开店</button>
                                                </a>
                                                <a href="{{url('/admin/alipayopen/onlyskm?user_id='.$v->user_id)}}">
                                                    <button type="button" class="btn  btn-success">收款码</button>
                                                </a>
                                                <a href="{{url('/admin/alipayopen/updateOauthUser?id='.$v->id)}}">
                                                <button type="button" class="btn btn-outline btn-info">修改</button>
                                                 </a>
                                               {{-- <a href="{{url('admin/alipayopen/setWxNotify?set_type=oskm&id='.$v->id)}}">
                                                    <button type="button" class="btn  btn-default">收银提醒设置</button>
                                                </a>--}}
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