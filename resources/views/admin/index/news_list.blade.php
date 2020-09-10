@extends('admin.layouts.admin')

@section('admin-body')
    <div class="layui-form search-fields" lay-filter="search_fields">
        <div class="layui-form-item">
            <input class="layui-input layui-input-inline" name="title" placeholder="标题" maxlength="15">
            <div class="layui-input-inline">
                <select name="is_show">
                    <option value="">请选择显示状态</option>
                    <option value="1">是</option>
                    <option value="0">否</option>
                </select>
            </div>
            <button id="btn-search" type="button" class="layui-btn layui-btn-normal">
                <i class="layui-icon layui-icon-search"></i>
            </button>
            @can('admin.news.add')
            <button id="btn-add" type="button" class="layui-btn">
                <i class="layui-icon layui-icon-add-1"></i>
            </button>
            @endcan
        </div>
    </div>
    <table id="default_table" lay-filter="default_table" lay-size="lg"></table>
@endsection

@push('script')
    <script>
        layui.use(['table', 'layer', 'form', 'jquery'], function () {
            const table = layui.table, $ = layui.$, form = layui.form;
            let tableIns = table.render({
                elem: '#default_table',
                even: true,
                id: 'default_table',
                url: '{{ route('api.news.list') }}',
                skin: 'line',
                method: 'post',
                height: 'full-200',
                size: 'lg',
                page: true,
                cols: [[
                    {field: 'key', title: '序号', fixed: 'left', width: 50, type: 'numbers'}
                    , {field: 'title', title: '标题', align: 'center'}
                    , {field: 'is_show', title: '显示', align: 'center', templet: function (d) {
                            return d.is_show ? '是' : '否';
                        }}
                    , {field: 'sort', title: '排序', align: 'center'}
                    , {field: 'created_at', title: '创建时间', align: 'center'}
                    , {field: 'updated_at', title: '更新时间', align: 'center'}
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
                window.location.href = '{{ route('view.news.add') }}';
            });
            table.on('tool(default_table)', function (obj) {
                if (obj.event === 'edit') {
                    window.location.href = '{{ route('view.news.edit') }}?id=' + obj.data.id;
                } else if (obj.event === 'del') {
                    let dialog = layer.confirm('是否删除此项', {
                        title: '提示',
                        btn: ['删除', '取消'],
                        yes: function () {
                            $.ajax({
                                url: "{{ route('api.news.delete') }}",
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
    <script type="text/html" id="operation">
        <div class="layui-btn-group">
            @can('admin.news.edit')
            <button type="button" class="layui-btn layui-btn-sm" lay-event="edit">
                <i class="layui-icon layui-icon-edit"></i>
            </button>
            @endcan
            @can('admin.news.delete')
            <button type="button" class="layui-btn layui-btn-sm" lay-event="del">
                <i class="layui-icon layui-icon-delete"></i>
            </button>
            @endcan
        </div>
    </script>
@endpush
