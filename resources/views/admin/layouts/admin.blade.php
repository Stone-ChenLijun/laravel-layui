@extends('admin.layouts.base')

@inject('menuService', 'menu')
@inject('config', 'app.config')
@section('body')
    <div class="layui-layout layui-layout-admin do-layout-admin">
        <div class="layui-header layui-bg-black">
            <div style="width: 200px;">
                <a href="{{ route('view.welcome.welcome') }}" style="display: flex;align-items: center">
                    <img src="{{ resolve('app.config')->getSiteLogoUrl() }}" class="logo">
                    <div class="logo-title">{{ $config->getSiteName() }}</div>
                </a>
            </div>
            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        <img src="{{ url('images/default.png') }}" class="layui-nav-img">
                        @auth
                            {{ auth()->user()->name }}
                        @endauth
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript: ;" id="user_profile">个人资料</a></dd>
                        <dd><a href="javascript: ;" id="change_password">修改密码</a></dd>
                        <dd><a href="javascript: ;" id="logout">注销</a></dd>
                    </dl>
                </li>
            </ul>
        </div>

        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <ul class="layui-nav layui-nav-tree" lay-filter="side_nav">
                    @foreach($menuService->getMenuTree() as $menu)
                    <li class="layui-nav-item @if($menuService->isInMenuTree($menu)) layui-nav-itemed @if(!$menu->hasChildren())layui-this @endif @endif">
                        <a class="menu" href="@if(!$menu->hasChildren()){{ $menu->url }} @else javascript:; @endif">
                            <i class="menu-icon layui-icon {{ $menu->icon }}"></i>{{ $menu->name }}
                        </a>
                        @if($menu->hasChildren())
                        <dl class="layui-nav-child">
                            @foreach($menu->children as $m)
                            <dd>
                                <a class="menu @if($menuService->isInMenuTree($m)) layui-this @endif" href="{{ $m->url }}">
                                    <i class="menu-icon layui-icon {{ $m->icon }}"></i>
                                    {{ $m->name }}
                                </a>
                            </dd>
                            @endforeach
                        </dl>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="layui-body">
        @section('admin-body')
        @show
        </div>

        <div class="layui-footer">
            <a href="http://www.chenlijun.com">dogify</a> 版权所有 © 2019 ~ 2020
        </div>
    </div>
@endsection

@push('script')
    <script>
        layui.use(['element', 'jquery', 'layer'], function () {
            const element = layui.element, $ = layui.$, layer = layui.layer;

            // 修改密码
            $('#change_password').click(function () {
                $('#old_password').val('');
                $('#new_password').val('');
                $('#repeat_password').val('');
                layer.open({
                    type: 1,
                    title: '修改密码',
                    offset: '150px',
                    content: $('#change_password_div'),
                    btn: '确认修改',
                    btnAlign: 'c',
                    yes: function (index, dom) {
                        let oldPassword = $('#old_password').val();
                        let newPassword = $('#new_password').val();
                        let repeatPassword = $('#repeat_password').val();
                        if (oldPassword.length === 0) {
                            layer.msg('请输入旧密码');
                            return false;
                        }
                        if (newPassword !== repeatPassword) {
                            layer.msg('两次输入的密码不一致');
                            return false;
                        }
                        if (!/^\w{6,12}$/.test(newPassword)) {
                            layer.msg('请输入6-12位新密码，仅允许包含字母数字和下划线');
                            return false;
                        }
                        $.ajax({
                            url: '{{ route('api.user.change_password') }}',
                            method: 'post',
                            data: {
                                _token: '{{ csrf_token() }}',
                                old_password: oldPassword,
                                new_password: newPassword
                            },
                            success: function (ert) {
                                if (ert.code === 422) {
                                    layer.msg(getValidationMsg(ert.data));
                                    return;
                                }
                                layer.msg(ert.msg);
                                if (ert.code === 0) {
                                    layer.close(index);
                                }
                            },
                            error: function (ert) {
                                layer.msg('服务器错误');
                            }
                        })
                    }
                });
            });
            $('#user_profile').click(function () {
                layer.open({
                    type: 1,
                    title: '个人资料(不填则不修改)',
                    offset: '150px',
                    area: '500px',
                    content: $('#edit_user_div'),
                    btn: '确认修改',
                    btnAlign: 'c',
                    yes: function (index, dom) {
                        $.ajax({
                            url: '{{ route('api.user.edit_self') }}',
                            method: 'post',
                            data: {
                                _token: '{{ csrf_token() }}',
                                name: $(dom).find('#name').val(),
                                account: $(dom).find('#account').val(),
                                email: $(dom).find('#email').val(),
                            },
                            success: function (ert) {
                                if (ert.code === 422) {
                                    layer.msg(getValidationMsg(ert.data));
                                    return;
                                }
                                $('#username').val('');
                                $('#phone').val('');
                                layer.msg(ert.msg);
                                if (ert.code === 0) {
                                    layer.close(index);
                                }
                            },
                            error: function (ert) {
                                layer.msg('服务器错误');
                            }
                        })
                    }
                });
            });
            $('#logout').click(function () {
                let dialog = layer.confirm('是否注销登录', {
                    btn: ['删除', '取消'],
                    title: '提示',
                    yes: function () {
                        layer.close(dialog);
                        window.location.href = "{{ route('api.user.logout') }}";
                    }
                });
            });
        });
    </script>
@endpush

@push('after-body')
    <div class="layui-form layui-form-pane" id="change_password_div" style="margin: 10px;">
        <div class="layui-form-item">
            <label class="layui-form-label required">旧密码：</label>
            <div class="layui-input-block">
                <input type="password" id="old_password" placeholder="请输入旧密码" class="layui-input" maxlength="12" autocomplete="off">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label required">新密码：</label>
            <div class="layui-input-block">
                <input type="password" id="new_password" placeholder="请输入6-12位新密码" class="layui-input" maxlength="12">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label required">确认密码：</label>
            <div class="layui-input-block">
                <input type="password" id="repeat_password" placeholder="请再次输入新密码" class="layui-input" maxlength="12">
            </div>
        </div>
    </div>
    <div class="layui-form layui-form-pane" id="edit_user_div" style="margin: 10px;">
        <div class="layui-form-item">
            <label class="layui-form-label">用户名：</label>
            <div class="layui-input-block">
                <input type="text" id="name" placeholder="请输入新用户名" class="layui-input" maxlength="12" autocomplete="off">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">帐号：</label>
            <div class="layui-input-block">
                <input type="text" id="account" placeholder="请输入新帐号" class="layui-input" maxlength="12" autocomplete="off">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">邮箱：</label>
            <div class="layui-input-block">
                <input type="text" id="email" placeholder="请输入新邮箱" class="layui-input" maxlength="256">
            </div>
        </div>
    </div>
@endpush
