@extends('layouts.public')
@section('title','服务商配置')
@section('content')
<div class="col-sm-6">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>服务商配置</h5>
        </div>
        <div class="ibox-content">
            <div class="row">
                <div class="col-sm-12">
                    <form role="form">
                        <div class="form-group">
                            <label>app_id</label>
                            <input placeholder="请输入您app_id" class="form-control" name="app_id" type="text">
                        </div>
                        <div class="form-group">
                            <label>merchant_id</label>
                            <input placeholder="请输入您merchant_id" class="form-control" name="merchant_id" type="text">
                        </div>
                        <div class="form-group">
                            <label>key</label>
                            <input placeholder="请输入key" class="form-control" type="text" name="key">
                        </div>
                        <div class="form-group">
                            <label>cert_path</label>
                            <input  class="form-control" type="text" name="cert_path">
                            <input  class="form-chat" type="file" name="cert">
                        </div>
                        <div class="form-group">
                            <label>key_path</label>
                            <input  class="form-control" type="text" name="key_path">
                            <input  class="form-chat" type="file" name="key">
                        </div>
                        <div>
                            <button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><strong>保存</strong>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection