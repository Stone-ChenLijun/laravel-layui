@extends('admin.layouts.admin')

@section('admin-body')
    <div class="layui-form search-fields" lay-filter="search_fields">
        <div class="layui-form-item">
            <input class="layui-input layui-input-inline" name="name" placeholder="用户名" maxlength="15">
            <input class="layui-input layui-input-inline" name="account" placeholder="帐号" maxlength="15">
            <input class="layui-input layui-input-inline" name="email" placeholder="邮箱" maxlength="15">
            <button id="btn-search" type="button" class="layui-btn layui-btn-normal">
                <i class="layui-icon layui-icon-search"></i>
            </button>
            @can('admin.user.add')
            <button id="btn-add" type="button" class="layui-btn">
                <i class="layui-icon layui-icon-add-1"></i>
            </button>
            @endcan
        </div>
    </div>
    <table id="default_table" lay-filter="default_table"></table>
@endsection

@push('script')
    <script>
        layui.use(['table', 'layer', 'form', 'jquery'], function () {
            const table = layui.table, $ = layui.$, form = layui.form;
            let tableIns = table.render({
                elem: '#default_table',
                even: true,
                id: 'default_table',
                url: '{{ route('api.user.list') }}',
                skin: 'line',
                method: 'post',
                height: 'full-200',
                page: true,
                cols: [[
                    {field: 'key', title: '序号', fixed: 'left', width: 50, type: 'numbers'}
                    , {field: 'name', title: '用户名', align: 'center'}
                    , {field: 'account', title: '帐号', align: 'center'}
                    , {field: 'email', title: '邮箱', align: 'center'}
                    , {field: 'roles', title: '角色', align: 'center', templet: '#roles'}
                    , {field: 'created_at', title: '创建时间', align: 'center'}
                    , {field: 'operation', title: '操作', align: 'center', templet: '#operation'}
                ]],
                text: {
                    none: '暂无内容'
                },
                request: {
                    pageName: 'page',
                    limitName: 'page_size'
                },
                where: {
                    '_token': "{{ csrf_token() }}"
                },
                parseData: function (res) {
                    return {
                        "code": res.code,
                        "msg": res.msg,
                        "count": res.data.total,
                        "data": res.data.data
                    }
                }
            });
            $('#btn-search').click(reload);
            function reload() {
                tableIns.reload({
                    where: form.val('search_fields'),
                    page: {
                        curr: 1
                    }
                });
            }
            $('#btn-add').click(function () {
                window.location.href = '{{ route('view.user.add') }}';
            });
            table.on('tool(default_table)', function (obj) {
                if (obj.event === 'reset_password') {
                    layer.open({
                        title: '请输入新密码',
                        content: '<div><input type="password" class="layui-input layui-input-inline" placeholder="请输入6-12位新密码"></div>',
                        btn: ['确认', '取消'],
                        btnAlign: 'c',
                        yes: function (index, layero) {
                            let newPassword = $(layero).find('input').val();
                            if (newPassword === '') {
                                layer.msg('请输入新密码', {type:1});
                                return false;
                            }
                            $.ajax({
                                url: "{{ route('api.user.reset_password') }}",
                                method: 'post',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    id: obj.data.id,
                                    new_password: newPassword
                                },
                                success: function (ert) {
                                    if (ert.code === 422) {
                                        layer.msg(getValidationMsg(ert.data));
                                        return;
                                    }
                                    if (ert.code === 0) {
                                        layer.msg(ert.msg, {time: 500}, function () {
                                            layer.close(index);
                                        });
                                    } else {
                                        layer.msg(ert.msg, {time:500,type:1});
                                    }
                                },
                                error: function (ert) {
                                    layer.msg('服务器错误');
                                }
                            });
                        }
                    });
                } else if (obj.event === 'edit') {
                    window.location.href = '{{ route('view.user.edit') }}?id=' + obj.data.id;
                } else if (obj.event === 'del') {
                    let dialog = layer.confirm('是否删除此项', {
                        title: '提示',
                        btn: ['删除', '取消'],
                        yes: function () {
                            $.ajax({
                                url: "{{ route('api.user.delete') }}",
                                method: 'post',
                                data: {
                                    id: obj.data.id,
                                    _token: '{{ csrf_token() }}',
                                },
                                success: function (ert) {
                                    if (ert.code === 422) {
                                        layer.msg(getValidationMsg(ert.data));
                                        return;
                                    }
                                    layer.msg(ert.msg);
                                    tableIns.reload();
                                },
                                error: function (ert) {
                                    layer.msg('服务器错误');
                                }
                            });
                            layer.close(dialog);
                        }
                    });
                }
            });
        });
    </script>
@endpush

@push('after-body')
    @verbatim
        <script type="text/html" id="roles">
        {{# if(d.is_super_admin) { }}
            <span>超级管理员无角色</span>
        {{# } else { }}
            {{# layui.each(d.roles, function(index, item) { }}
            <span class="layui-badge layui-bg-cyan">{{ item.name }}</span>
            {{# }); }}
        {{# } }}
        </script>
    <script type="text/html" id="operation">
        {{# if(d.is_super_admin) { }}
            <span>超级管理员无法操作</span>
        {{#  } else { }}
        <div class="layui-btn-group">
            @can('admin.user.reset_password')
            <button type="button" class="layui-btn layui-btn-sm" lay-event="reset_password">
                <i class="layui-icon layui-icon-password"></i>重置密码
            </button>
            @endcan
            @can('admin.user.edit')
            <button type="button" class="layui-btn layui-btn-sm" lay-event="edit">
                <i class="layui-icon layui-icon-edit"></i>
            </button>
            @endcan
            @can('admin.user.delete')
            <button type="button" class="layui-btn layui-btn-sm" lay-event="del">
                <i class="layui-icon layui-icon-delete"></i>
            </button>
            @endcan
        </div>
        {{# } }}
    </script>
    @endverbatim
@endpush
