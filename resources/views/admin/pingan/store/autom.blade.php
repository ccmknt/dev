@extends('layouts.publicStyle')
@section('title','绑定银行账号')
@section('css')
@endsection
@section('content')
    <script src="{{asset('/js/check.js')}}" type="text/javascript"></script>
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>绑定结算银行账号</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            <input type="hidden" id="external_id" value="<?php echo $_GET['external_id']?>">
                            <input type="hidden" id="code_number" value="<?php echo $_GET['code_number']?>">
                            <div class="form-group">
                                <label>银行卡卡号</label>
                                <input required placeholder="银行卡卡号" class="form-control" value="" name="bank_card_no"
                                       id="bank_card_no"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>银行卡的开户人姓名</label>
                                <input required="required" placeholder="银行卡的开户人姓名" value="" class="form-control"
                                       name="card_holder" id="card_holder"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>该银行卡是否为对公账户</label>
                                <div class="radio">
                                    <label>
                                        <input onclick="Switch()" checked="checked" value="0" id="is_public_account"
                                               name="is_public_account" type="radio">否</label>
                                    <label>
                                        <input onclick="Switch()" value="1" id="is_public_account"
                                               name="is_public_account" type="radio">是</label>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group" id="ob" style="display: none">
                                <label>对公账户的开户行</label>
                                <input required="required" placeholder="对公账户的开户行，如:中国工商银行南京江宁分行"
                                       class="form-control" value="" name="open_bank"
                                       id="open_bank"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                        </form>
                    </div>
                </div>
                <a href="javascript:void(0)" onclick="addpost()">
                    <button style="width: 100%;height: 40px;font-size: 18px;" type="button" class="btn btn-primary">
                        下一步上传资质文件
                    </button>
                </a>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')
    <script>
        function addpost() {

            if (!CheckBankNo($("#bank_card_no"))) {
                layer.msg('请检查银行卡号');
                return false;
            }

            $.post("{{route("automPost")}}",
                    {
                        _token: '{{csrf_token()}}',
                        external_id: $("#external_id").val(),
                        code_number: $("#code_number").val(),
                        bank_card_no: $("#bank_card_no").val(),
                        card_holder: $("#card_holder").val(),
                        is_public_account: $('input:radio:checked').val(),
                        open_bank: $("#open_bank").val(),
                    },
                    function (result) {
                        if (result.success) {
                            window.location.href = "{{url('admin/pingan/autoFile?external_id=')}}" + $("#external_id").val() + '&code_number=' + $("#code_number").val();
                        } else {
                            layer.msg(result.error_message);
                        }
                    }, "json")

        }
        function Switch() {
            if ($('input:radio:checked').val() == 1) {
                $("#ob").css("display", "block");
            } else {
                $("#ob").css("display", "none");
            }
        }
    </script>

@endsection
@endsection