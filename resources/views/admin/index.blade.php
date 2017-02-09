@extends('layouts.public')
@section('title',"主页")
@section('content')
    <div id="wrapper">
        <!--左侧导航开始-->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="nav-close"><i class="fa fa-times-circle"></i>
            </div>
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                                    <span class="block m-t-xs" style="font-size:20px;">
                                        <i class="fa fa-area-chart"></i>
                                        <strong class="font-bold">店铺管理系统</strong>
                                    </span>
                                </span>
                            </a>
                        </div>
                        <div class="logo-element">Admin
                        </div>
                    </li>
                    <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
                        <span class="ng-scope">分类</span>
                    </li>
                    <li>
                        <a class="J_menuItem" href="{{route('home')}}">
                            <i class="fa fa-home"></i>
                            <span class="nav-label">主页</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="glyphicon glyphicon-th"></i>
                            <span class="nav-label">支付宝口碑店铺管理</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('admin/alipayopen/oauth')}}">我的授权二维码</a>
                            </li>
                            @permission('oauthlist')
                            <li>
                                <a class="J_menuItem" href="{{url('admin/alipayopen/oauthlist')}}">口碑开店收款</a>
                            </li>
                            @endpermission
                            @permission('store')
                            <li>
                                <a class="J_menuItem" href="{{url('admin/alipayopen/store')}}">口碑门店列表</a>
                            </li>
                            @endpermission
                            @permission('ApplyorderBatchquery')
                            <li>
                                <a class="J_menuItem" href="{{route('ApplyorderBatchquery')}}">商户操作查询</a>
                            </li>
                            @endpermission
                            @permission('alipaytradelist')
                            <li>
                                <a class="J_menuItem" href="{{route('alipaytradelist')}}">交易流水查询</a>
                            </li>
                            @endpermission
                            <li>
                                <a class="J_menuItem" href="">分类更新</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="glyphicon glyphicon-ok"></i>
                            <span class="nav-label">微信支付商户管理</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{route('WxShopList')}}">微信支付商户列表</a>
                            </li>
                            <li>
                                <a class="J_menuItem" href="{{route('WxOrder')}}">商户交易流水查询</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="glyphicon glyphicon-qrcode"></i>
                            <span class="nav-label">扫码支付多码合一</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            @permission('oauthlist')
                            <li>
                                <a class="J_menuItem" href="{{route('AlipayWexinLists')}}">微信支付宝二码合一</a>
                            </li>
                            @endpermission
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-bank"></i>
                            <span class="nav-label">平安银行通道商户管理</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{route('PingAnStoreQR')}}">我的商户码</a>
                            </li>
                            <li>
                                <a class="J_menuItem" href="{{route('PingAnStoreIndex')}}">商户列表</a>
                            </li>
                            @permission('pinganconfig')
                            <li>
                                <a class="J_menuItem" href="{{route('pinganconfig')}}">银行通道配置</a>
                            </li>
                            @endpermission
                        </ul>
                    </li>
                    @permission('isvconfigs')
                    <li>
                        <a href="#">
                            <i class="fa fa-desktop"></i>
                            <span class="nav-label">ISV服务商配置管理</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">

                            <li>
                                <a class="J_menuItem" href="{{route('isvconfig')}}">基础配置</a>
                            </li>

                        </ul>
                    </li>
                    @endpermission
                    @permission('spset')
                    <li>
                        <a href="#">
                            <i class="glyphicon glyphicon-ok-sign"></i>
                            <span class="nav-label">微信支付服务商管理</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{route("spset")}}">服务商配置</a>
                            </li>
                        </ul>
                    </li>
                    @endpermission
                    @permission('alipayadd')
                    <li>
                        <a href="#">
                            <i class="glyphicon glyphicon-send"></i>
                            <span class="nav-label">广告系统管理</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="">支付宝广告</a>
                            </li>
                        </ul>
                    </li>
                    @endpermission
                    @permission('set')
                    <li>
                        <a href="#">
                            <i class="fa fa-magic"></i>
                            <span class="nav-label">系统设置管理</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            @permission('users')
                            <li>
                                <a class="J_menuItem" href="{{url('admin/alipayopen/users')}}">员工管理</a>
                            </li>
                            @endpermission
                            @permission('role')
                            <li>
                                <a class="J_menuItem" href="{{url('admin/alipayopen/role')}}">角色管理</a>
                            </li>
                            @endpermission
                            @permission('permission')
                            <li>
                                <a class="J_menuItem" href="{{url('admin/alipayopen/permission')}}">权限管理</a>
                            </li>
                            @endpermission
                            @permission('users')
                            <li>
                                <a class="J_menuItem" href="{{route('setApp')}}">网站设置</a>
                            </li>
                            @endpermission

                        </ul>
                    </li>
                    @endpermission
                </ul>
            </div>
        </nav>
        <!--左侧导航结束-->
        <!--右侧部分开始-->
        <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header"><a class="navbar-minimalize minimalize-styl-2 btn btn-info " href="#"><i
                                    class="fa fa-bars"></i> </a>
                        <form role="search" class="navbar-form-custom" method="post" action="">
                            <div class="form-group">
                                <input placeholder="请输入您需要查找的内容 …" class="form-control" name="top-search"
                                       id="top-search" type="text">
                            </div>
                        </form>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li class="dropdown">欢迎您:{{Auth::user()->name}} </li>
                        <a href="/logout">
                            <button type="button" class="btn btn-default btn-xs">退出</button>
                        </a>
                        <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                                <i class="fa fa-envelope"></i> <span class="label label-warning">16</span>
                            </a>
                            <ul class="dropdown-menu dropdown-messages">
                                <li class="m-t-xs">
                                    <div class="dropdown-messages-box">
                                        <a href="" class="pull-left">
                                            <img alt="image" class="img-circle" src="img/a7.jpg">
                                        </a>
                                        <div class="media-body">
                                            <small class="pull-right">46小时前</small>
                                            <strong>小四</strong> 是不是只有我死了,你们才不骂爵迹
                                            <br>
                                            <small class="text-muted">3天前 2014.11.8</small>
                                        </div>
                                    </div>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <div class="dropdown-messages-box">
                                        <a href="" class="pull-left">
                                            <img alt="image" class="img-circle" src="img/a4.jpg">
                                        </a>
                                        <div class="media-body ">
                                            <small class="pull-right text-navy">25小时前</small>
                                            <strong>二愣子</strong> 呵呵
                                            <br>
                                            <small class="text-muted">昨天</small>
                                        </div>
                                    </div>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <div class="text-center link-block">
                                        <a class="J_menuItem" href="">
                                            <i class="fa fa-envelope"></i> <strong> 查看所有消息</strong>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                                <i class="fa fa-bell"></i> <span class="label label-primary">8</span>
                            </a>
                            <ul class="dropdown-menu dropdown-alerts">
                                <li>
                                    <a href="">
                                        <div>
                                            <i class="fa fa-envelope fa-fw"></i> 您有16条未读消息
                                            <span class="pull-right text-muted small">4分钟前</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="">
                                        <div>
                                            <i class="fa fa-qq fa-fw"></i> 3条新回复
                                            <span class="pull-right text-muted small">12分钟钱</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <div class="text-center link-block">
                                        <a class="J_menuItem" href="">
                                            <strong>查看所有 </strong>
                                            <i class="fa fa-angle-right"></i>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="row J_mainContent" id="content-main">
                <iframe id="J_iframe" width="100%" height="100%" src="{{route('home')}}" frameborder="0"
                        data-id="index_v1.html" seamless></iframe>
            </div>
        </div>
        <!--右侧部分结束-->
    </div>
@endsection