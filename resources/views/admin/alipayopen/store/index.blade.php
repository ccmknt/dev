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
                        <h5>门店列表</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>商户id</th>
                                    <th>企业名称</th>
                                    <th>门店名称</th>
                                    <th>地址</th>
                                    <th>联系方式</th>
                                    <th>状态</th>
                                    <th>操作</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $v)
                                    <tr>
                                        <td>{{$v['store_id']}}</td>
                                        <td><span class="pie">{{$v['licence_name']}}</span></td>
                                        <td><span class="pie">{{$v['main_shop_name']}}</span></td>
                                        <td>{{$v['address']}}</td>
                                        <td><span class="pie">{{$v['contact_number']}}</span></td>
                                        <td><button type="button" class="btn btn-outline btn-warning">审核中</button></td>
                                        @if($v['shop_id']=="")
                                        <th>
                                            <a href="{{'/admin/alipayopen/store/'.$v['id'].'/edit'}}">
                                            <button type="button" class="btn btn-info">重新提交</button>
                                          </a>
                                            <a href="{{url('admin/alipayopen/skm?id='.$v['id'])}}">
                                                <button type="button" class="btn  btn-sm">商家门店收款码</button></a>
                                            <a href="">
                                                <button type="button" class="btn">固定金额收款码</button></a>
                                        </th>
                                       @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('js')
@endsection