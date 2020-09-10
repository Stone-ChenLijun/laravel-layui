@extends('admin.layouts.base')

@push('head')
    <style>
        body {
            background-image: url({{ asset('images/login/' . random_int(1, 3) . '.jpg') }});
            background-repeat: no-repeat;
            background-size: 100% 100%;
            height: auto;
        }
    </style>
@endpush

@inject('config', 'app.config')

@section('body-class', 'login')

@section('body')
    <div class="login-container">
        <div class="login-header">
            <div class="head-text">
                <i class="layui-icon layui-icon-engine auto-heigh-icon"></i>{{ $config->getSiteName() }}
            </div>
        </div>
        <form class="layui-form layui-form-pane login-content">
            <div class="layui-form-item">
                <label class="layui-form-label">帐号：</label>
                <div class="layui-input-block">
                    <input type="text" id="account" name="account" placeholder="请输入帐号" class="layui-input" maxlength="12" autocomplete="off">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">密码：</label>
                <div class="layui-input-block">
                    <input type="password" id="password" name="password" placeholder="请输入密码" class="layui-input" maxlength="12">
                </div>
            </div>
            <button type="button" class="layui-btn layui-btn-normal" id="login" style="width: 100%">登录</button>
        </form>
    </div>
    <div class="bottom">
        <a class="copyright" href="http://www.chenlijun.com" target="_blank">dogify</a> 版权所有 &copy; 2019-2020
        @if(strlen($icp))
        <a class="icp" href="http://www.beian.miit.gov.cn" target="_blank">{{ $icp }}</a>
        @endif
        @if(strlen($policeIcp))
        <a class="police-icp" href="http://www.beian.gov.cn/portal/registerSystemInfo" target="_blank">{{ $policeIcp }}</a>
        @endif
    </div>
@endsection

@push('script')
    <script>
        layui.use(['layer', 'jquery'], function () {
            let $ = layui.$, layer = layui.layer;

            $('#login').click(login);
            $('#account,#password').keydown(enterLogin);

            function enterLogin(event) {
                if (event.keyCode === 13) {
                    login();
                }
            }

            function login() {
                $.ajax({
                    url: "{{ route('api.user.login') }}",
                    method: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                        account: $('#account').val(),
                        password: $('#password').val()
                    },
                    success: function(ert) {
                        if (ert.code === 422) {
                            layer.msg(getValidationMsg());
                            return ;
                        }
                        if (ert.code === 0) {
                            layer.msg(ert.msg, {time: 500}, function () {
                                window.location.href = ert.data.redirect_url;
                            })
                        } else {
                            layer.msg(ert.msg);
                        }
                    },
                    error: function (ert) {
                        if (ert.status === 422) {
                            layer.msg(getValidationMsg(ert.responseJSON.errors));
                        } else {
                            layer.msg(ert.responseJSON.message);
                        }
                    }
                })
            }
        });
    </script>
@endpush
