@extends('admin.layouts.admin')

@push('head')
    <script src="{{ asset('js/xm-select.js') }}"></script>
@endpush

@section('admin-body')
    <div class="layui-form layui-form-pane do-form-half">
        <div class="layui-form-item">
            <label class="layui-form-label required">用户名</label>
            <div class="layui-input-block">
                <input type="text" name="name" class="layui-input" autocomplete="off" maxlength="32" placeholder="请输入用户名，限长32个字符" value="{{ $item->name }}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label required">帐号</label>
            <div class="layui-input-block">
                <input type="text" name="account" class="layui-input" autocomplete="off" maxlength="32" placeholder="请输入帐号[2,32]" value="{{ $item->account }}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">邮箱</label>
            <div class="layui-input-block">
                <input type="text" name="email" class="layui-input" autocomplete="off" maxlength="255" placeholder="请输入邮箱，限长255" value="{{ $item->email }}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label required">角色</label>
            <div class="layui-input-block">
                <div id="role-list"></div>
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
        var roleSelector = xmSelect.render({
            el: '#role-list',
            language: 'zn',
            prop: {
                value: 'id',
            },
            initValue: @json($checkRoleIds),
            data: @json($roles),
        });
        layui.use(['form', 'layer', 'jquery'], function(){
            const form = layui.form, layer = layui.layer, $ = layui.$;

            $('#btn-default').click(function () {
                $.ajax({
                    method: 'post',
                    url: '{{ route('api.user.edit') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: '{{ request()->get('id') }}',
                        name: $('input[name="name"]').val(),
                        account: $('input[name="account"]').val(),
                        email: $('input[name="email"]').val(),
                        role_id_array: roleSelector.getValue('value'),
                    },
                    success: function (ert) {
                        if (ert.code === 422) {
                            layer.msg(getValidationMsg(ert.data));
                            return;
                        }
                        if (ert.code === 0) {
                            layer.msg(ert.msg, {time:500}, function () {
                                window.location.href = '{{ route('view.user.list') }}';
                            })
                        } else {
                            layer.msg(ert.msg)
                        }
                    },
                    error: function (ert) {
                        layer.msg('服务器错误');
                    }
                });
            });
        });
    </script>
@endpush
