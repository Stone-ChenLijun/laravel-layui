@extends('admin.layouts.admin')

@section('admin-body')
    <div class="layui-form layui-form-pane do-form-half" lay-filter="default-form">
        <div class="layui-form-item wide-label">
            <label class="layui-form-label">订单付款提醒</label>
            <div class="layui-input-block">
                <input type="text" name="notify_pay_order" class="layui-input" autocomplete="off" maxlength="64" placeholder="请输入模板编号，限长64" value="{{ $notify_pay_order }}">
            </div>
        </div>
        <div class="layui-form-item wide-label">
            <label class="layui-form-label">订单退款通知</label>
            <div class="layui-input-block">
                <input type="text" name="notify_order_refund" class="layui-input" autocomplete="off" maxlength="64" placeholder="请输入模板编号，限长64" value="{{ $notify_order_refund }}">
            </div>
        </div>
        <div class="layui-form-item wide-label">
            <label class="layui-form-label">审核结果通知</label>
            <div class="layui-input-block">
                <input type="text" name="approval_result_notify" class="layui-input" autocomplete="off" maxlength="64" placeholder="请输入模板编号，限长64" value="{{ $approval_result_notify }}">
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
                    url: '{{ route('api.config.edit_wechat_template') }}',
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
