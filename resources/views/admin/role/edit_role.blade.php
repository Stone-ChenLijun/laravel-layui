@extends('admin.layouts.admin')

@section('admin-body')
    <div class="layui-form layui-form-pane do-form-half">
        <div class="layui-form-item">
            <label class="layui-form-label required">角色名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" class="layui-input" autocomplete="off" maxlength="32" placeholder="请输入角色名称，限长32个字符" value="{{ $item->name }}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label required">排序</label>
            <div class="layui-input-block">
                <input type="number" name="sort" class="layui-input" autocomplete="off" placeholder="请输入排序值[-999,999]" value="{{ $item->sort }}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label required">启用状态</label>
            <div class="layui-input-block">
                <select name="is_enable">
                    <option value="1" @if($item->is_enable) selected @endif>是</option>
                    <option value="0" @if(!$item->is_enable) selected @endif>否</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label required">选择权限</label>
            <div class="layui-input-block">
                <div id="permission-tree"></div>
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
        layui.extend({
            authtree: '/layui_ext/authtree/authtree'   // {/}的意思即代表采用自有路径，即不跟随 base 路径
        }).use(['form', 'layer', 'jquery', 'authtree'], function(){
            const form = layui.form, layer = layui.layer, $ = layui.$, authtree = layui.authtree;

            // 如果后台返回的不是树结构，请使用 authtree.listConvert 转换
            let tree = authtree.listConvert({!! $permissions !!},
                {
                    parentKey: 'parent_id',
                    checkedKey: @json($checkedPermissionIds),
                });
            authtree.render('#permission-tree', tree, {
                layfilter: 'lay-check-auth',
                autowidth: true,
                openall: true,
                theme: 'auth-skin-default',
                themePath: '/layui_ext/authtree/tree_themes/'
            });
            $('#btn-default').click(function () {
                $.ajax({
                    method: 'post',
                    url: '{{ route('api.role.edit') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: '{{ request()->get('id') }}',
                        name: $('input[name="name"]').val(),
                        sort: $('input[name="sort"]').val(),
                        is_enable: $('select[name="is_enable"]').val(),
                        permission_id_array: authtree.getChecked('#permission-tree')
                    },
                    success: function (ert) {
                        if (ert.code === 422) {
                            layer.msg(getValidationMsg(ert.data));
                            return;
                        }
                        if (ert.code === 0) {
                            layer.msg(ert.msg, {time:500}, function () {
                                window.location.href = '{{ route('view.role.list') }}';
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
