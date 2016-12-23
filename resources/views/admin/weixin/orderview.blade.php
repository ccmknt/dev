<link rel="stylesheet" href="//res.wx.qq.com/open/libs/weui/1.0.2/weui.min.css"/>
<div class="weui-cells">
    <div class="weui-cell">
        <div class="weui-cell__bd">
            <form action="{{route('order')}}"  method="post">
                {{csrf_field()}}
            <input class="weui-input" type="number" placeholder="请输入金额"/>
                <button type="submit">提交</button>
            </form>
        </div>
    </div>
</div>
<div class="weui-btn-area">
    <a class="weui-btn weui-btn_primary" onclick="submit()" href="javascript:" id="showTooltips">确定</a>
</div>