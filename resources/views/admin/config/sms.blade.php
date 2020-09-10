@extends('admin.layouts.admin')

@section('admin-body')
    <div class="layui-form layui-form-pane do-form-half" lay-filter="default-form">
        <div class="layui-form-item">
            <label class="layui-form-label">访问密钥id</label>
            <div class="layui-input-block">
                <input type="text" name="app_id" class="layui-input" autocomplete="off" maxlength="64" placeholder="请输入访问密钥id，限长64" value="{{ $app_id }}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">访问密钥</label>
            <div class="layui-input-block">
                <input type="text" name="app_secret" class="layui-input" autocomplete="off" maxlength="64" placeholder="请输入访问密钥，限长64" value="{{ $app_secret }}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">签名</label>
            <div class="layui-input-block">
                <input type="text" name="sign" class="layui-input" autocomplete="off" maxlength="64" placeholder="请输入签名，限长64" value="{{ $sign }}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">模板编码</label>
            <div class="layui-input-block">
                <input type="text" name="template_code" class="layui-input" autocomplete="off" maxlength="64" placeholder="请输入模板编码，限长64" value="{{ $template_code }}">
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
            $('#btn-default').click(function () {
                let data = form.val('default-form');
                data._token = '{{ csrf_token() }}';
                $.ajax({
                    url: '{{ route('api.config.edit_sms') }}',
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
