@extends('layouts.public')
@section('content')
    <div class="col-sm-6">
        <a class="btn btn-white btn-bitbucket" href="{{url('/register')}}">
            <i class="fa fa-user-md"></i>
        </a>
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>员工列表</h5>
            </div>
            <div class="ibox-content">

                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>用户</th>
                        <th>电话</th>
                        <th>邮箱</th>
                        <th>添加时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($user as $v)
                        <tr>
                            <td>{{$v['id']}}</td>
                            <td>{{$v['name']}}</td>
                            <td>{{$v['phone']}}</td>
                            <td>{{$v['email']}}</td>
                            <td>{{$v['created_at']}}</td>
                            <td>
                                <button onclick="updateu('{{$v['id']}}')" type="button" class="btn btn-success">修改
                                </button>
                                <button type="button" onclick="deleteu('{{$v['id']}}')" class="btn  btn-danger">删除
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function updateu(id) {
            window.location.href = "/admin/alipayopen/updateu?id=" + id;
        }

        function deleteu(id) {
            layer.confirm('数据价值很重要！确定要删除用户信息？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.post("{{route('deleteu')}}",{id:id,_token:"{{csrf_token()}}"},function(result){
                    window.location.href = "{{route('users')}}";
                });
            }, function(){

            });

        }
    </script>
@endsection