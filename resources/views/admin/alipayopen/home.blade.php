@extends('layouts.public')
@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="row row-sm text-center">
                            <div class="col-xs-6">
                                <div class="panel padder-v item bg-primary">
                                    <div class="h1 text-fff font-thin h1">{{$store_y}}</div>
                                    <span class="text-muted text-xs">昨日店铺</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item">
                                    <div class="font-thin h1">{{$total_y}}</div>
                                    <span class="text-muted text-xs">昨日交易</span>
                                    <div class="bottom text-left">
                                        <i class="fa fa-caret-up text-warning m-l-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item">
                                    <div class="h1 text-info font-thin h1">{{$stores}}</div>
                                    <span class="text-muted text-xs">店铺数量</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item bg-info">
                                    <div class="h1 text-fff font-thin h1">{{$total_amount}}</div>
                                    <span class="text-muted text-xs">总交易额</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    @permission('appsUadate')
                    <div class="col-sm-8">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>软件更新</h5>
                            </div>
                            <div class="ibox-content">
                                <button type="button"
                                        class="btn btn-outline btn-default">{{$data->app_version}}</button>
                                <span id="update"></span>
                                <ul>
                                    <li>{{$data->msg}}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endpermission
                </div>
                <div class="row">
                    <div class="col-sm-9" style="padding-right:0;">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title" style="border-bottom:none;background:#fff;">
                                <h5>交易数据</h5>
                            </div>
                            <div class="ibox-content" style="border-top:none;">
                                <div id="yesterday" style="height: 217px; padding: 0px; position: relative;">
                                    <canvas class="base" width="739" height="217"></canvas>
                                    <canvas class="overlay" width="739" height="217"
                                            style="position: absolute; left: 0px; top: 0px;"></canvas>
                                    <div class="tickLabels" style="font-size:smaller">
                                        <div class="xAxis x1Axis" style="color:#999999">
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:center;left:-19px;top:203px;width:82px">
                                                1月 2016
                                            </div>
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:center;left:80px;top:203px;width:82px">
                                                4月 2016
                                            </div>
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:center;left:179px;top:203px;width:82px">
                                                7月 2016
                                            </div>
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:center;left:280px;top:203px;width:82px">
                                                10月 2016
                                            </div>
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:center;left:381px;top:203px;width:82px">
                                                1月 2017
                                            </div>
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:center;left:481px;top:203px;width:82px">
                                                4月 2017
                                            </div>
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:center;left:580px;top:203px;width:82px">
                                                7月 2017
                                            </div>
                                        </div>
                                        <div class="yAxis y1Axis" style="color:#999999">
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:right;top:191px;right:722px;width:17px">
                                                0
                                            </div>
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:right;top:126px;right:722px;width:17px">
                                                50
                                            </div>
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:right;top:62px;right:722px;width:17px">
                                                100
                                            </div>
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:right;top:-3px;right:722px;width:17px">
                                                150
                                            </div>
                                        </div>
                                        <div class="yAxis y2Axis" style="color:#999999">
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:left;top:191px;left:708px;width:31px">
                                                ¥0.600
                                            </div>
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:left;top:126px;left:708px;width:31px">
                                                ¥0.667
                                            </div>
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:left;top:62px;left:708px;width:31px">
                                                ¥0.733
                                            </div>
                                            <div class="tickLabel"
                                                 style="position:absolute;text-align:left;top:-3px;left:708px;width:31px">
                                                ¥0.800
                                            </div>
                                        </div>
                                    </div>
                                    <div class="legend">
                                        <div style="position: absolute; width: 48px; height: 29px; bottom: 24px; left: 27px; background-color: rgb(255, 255, 255); opacity: 0.85;"></div>
                                        <table style="position:absolute;bottom:24px;left:27px;;font-size:smaller;color:#999999">
                                            <tbody>
                                            <tr>
                                                <td class="legendColorBox">
                                                    <div style="border:1px solid #ccc;padding:1px">
                                                        <div style="width:4px;height:0;border:5px solid rgb(247,249,251);overflow:hidden"></div>
                                                    </div>
                                                </td>
                                                <td class="legendLabel">支付宝</td>
                                            </tr>
                                            <tr>
                                                <td class="legendColorBox">
                                                    <div style="border:1px solid #ccc;padding:1px">
                                                        <div style="width:4px;height:0;border:5px solid rgb(175,216,248);overflow:hidden"></div>
                                                    </div>
                                                </td>
                                                <td class="legendLabel">微信</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = get;
        function get() {
            $.post("{{route('updateInfo')}}", {_token: "{{csrf_token()}}"},
                    function (data) {
                        if (data.status == 1) {
                            $("#update").append('<button type="button" onclick="updateFile()" class="btn btn-outline btn-success">更新系统</button>');
                        } else {
                            if (data.status != 404) {
                                layer.alert(data.msg, {icon: 5});
                            }
                        }
                    }, 'json');
        }
        //更新文件
        function updateFile() {
            $.post("{{route('appUpdateFile')}}", {_token: "{{csrf_token()}}"},
                    function (data) {
                        if (data.status == 200) {
                            layer.alert(data.msg, {icon: 6});
                        } else {
                            layer.alert(data.msg, {icon: 5});
                        }
                    }, 'json');
        }
    </script>
@endsection