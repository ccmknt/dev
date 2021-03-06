@extends('layouts.public')
@section('content')
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>交易流水
                <small>所有微信支付订单的查询，商户可以通过该接口主动查询订单状态</small>
            </h5>
        </div>
        <div class="ibox-content">
            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper form-inline" role="grid">
                <table class="table table-striped table-bordered table-hover dataTables-example dataTable"
                       id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
                    <thead>
                    <tr role="row">
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 189px;" aria-label="渲染引擎：激活排序列升序" aria-sort="ascending">商户订单号
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 308px;" aria-label="平台：激活排序列升序">店铺ID
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 308px;" aria-label="平台：激活排序列升序">店铺名称
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 142px;" aria-label="引擎版本：激活排序列升序">创建时间
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 144px;" aria-label="CSS等级：激活排序列升序">更新时间
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 144px;" aria-label="CSS等级：激活排序列升序">总金额
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 144px;" aria-label="CSS等级：激活排序列升序">交易状态
                        </th>
                    </tr>
                    </thead>
                    <tbody id="appends">
                    @foreach($wxorder as $v)
                        <tr class='gradeA odd'>
                            <td class=''>{{$v->out_trade_no}}</td>
                            <td class=''>w{{$v->mch_id}}</td>
                            <td class=''>{{$v->store_name}}</td>
                            <td class=''>{{$v->created_at}}</td>
                            <td class=''>{{$v->updated_at}}</td>
                            <td class=''>{{$v->total_fee}}</td>

                            @if($v->status=="SUCCESS")
                                <td style="color: green">
                                    <button type="button" class="btn btn-outline btn-success">付款成功</button>
                                </td>

                            @else
                                <td class=''>等待买家付款</td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
                            {{$wxorder->links()}}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('js')
    {{--
        <script>
            window.onload = get;
            function get(){
                getinfo();
            }
            function getinfo() {
                $.post("{{route("ApplyOrderBatchQuery")}}", {_token:"{{csrf_token()}}"}, function (data) {
                    for (var key in data) {
                        var selObj = $("#appends");
                        selObj.append(
                                "<tr class='gradeA odd' >"+
                                "<td class=''>"+data[key].action+"</td>" +
                                "<td class=''>"+data[key].apply_id+"</td>" +
                                "<td class=''>"+data[key].biz_id+"</td>" +
                                "<td class=''>"+data[key].create_time+"</td>" +
                                "<td class=''>"+data[key].update_time+"</td>" +
                                "<td class=''>"+data[key].result_code+"</td>"+
                                "<td class=''>"+data[key].result_code+"</td>"+
                                "</tr>"
                        );
                    }
                }, "json");
            }
        </script>--}}
@endsection