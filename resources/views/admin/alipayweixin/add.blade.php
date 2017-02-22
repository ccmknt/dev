@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>选择商户</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{route('addAliPayWeixinStorePost')}}" method="post">
                           {{csrf_field()}}
                            <div class="form-group">
                                <label class="col-sm-2 control-label">选择商户</label>
                                <div class="col-sm-10">
                                    <select class="form-control m-b" name="id">
                                        @foreach($store as $v)
                                            @if($v->auth_shop_name)
                                                <option value="{{$v->id}}">{{$v->auth_shop_name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @if(count($errors)>0)
                                        <div class="mark">
                                            @if(is_object($errors))
                                                @foreach($errors->all() as $error)
                                                    <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$error}}</span>
                                                @endforeach
                                            @else
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors}}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                        type="submit">
                                    <strong>生成二码合一</strong>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="con"></div>
@section('js')

@endsection
@endsection