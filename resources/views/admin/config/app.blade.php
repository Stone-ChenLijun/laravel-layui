@extends('admin.layouts.admin')

@section('body-class', 'config-app')

@push('head')
    <script src="{{ asset('js/photo_selector.js') }}"></script>
    <style>
        .cover-chose-image {
            width: 92px;
            height: 92px;
        }
        .btn-chose-image2 {
            display: block;
        }
    </style>
@endpush
@section('admin-body')
    <div class="layui-form layui-form-pane do-form-half" lay-filter="default-form">
        <div class="layui-form-item">
            <label class="layui-form-label">站点名称</label>
            <div class="layui-input-block">
                <input type="text" name="site_name" class="layui-input" autocomplete="off" maxlength="10" placeholder="请输入站点名称，限长10" value="{{ $site_name }}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">站点logo</label>
            <div class="layui-input-block">
                <input type="hidden" name="logo_id" class="layui-input" autocomplete="off" maxlength="32" value="{{ $logo_id }}">
                <img class="chose-image" src="{{ $logo_url }}">
                <button type="button" class="layui-btn layui-btn-normal btn-chose-image">选择图片</button>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">ICP备案号</label>
            <div class="layui-input-block">
                <input type="text" name="icp" class="layui-input" autocomplete="off" maxlength="40" placeholder="请输入ICP备案号，限长40" value="{{ $icp }}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">公安备案号</label>
            <div class="layui-input-block">
                <input type="text" name="police_icp" class="layui-input" autocomplete="off" maxlength="40" placeholder="请输入公安备案号，限长40" value="{{ $police_icp }}">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button id="btn-default" type="button" class="layui-btn" lay-submit lay-filter="*">保存</button>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        layui.use(['form', 'jquery', 'layer'], function () {
            const form = layui.form, $ = layui.$, layer = layui.layer;
            $('.btn-chose-image').click(function () {
                openAlbum(1, function (images) {
                    $('.chose-image').attr('src', images[0].url);
                    $('input[name="logo_id"]').val(images[0].id);
                })
            });
            $('#btn-default').click(function () {
                let data = form.val('default-form');
                data._token = '{{ csrf_token() }}';
                $.ajax({
                    url: '{{ route('api.config.edit_app') }}',
                    method: 'post',
                    data: data,
                    success: function (res) {
                        if (res.code === 422) {
                            layer.msg(getValidationMsg(res.data));
                            return;
                        }
                        if (res.code === 0) {
                            layer.msg(res.msg, {time:500}, function () {
                                window.location.reload();
                            });
                            return;
                        }
                        layer.msg(res.msg);
                    },
                    error: function (res) {
                        layer.msg('服务器错误');
                    }
                })
            });
        });
    </script>
@endpush
