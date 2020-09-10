@extends('admin.layouts.admin')

@push('head')
    <script src="{{ asset('js/ueditor/ueditor.config.js') }}"></script>
    <script src="{{ asset('js/ueditor/ueditor.all.min.js') }}"></script>
    <script src="{{ asset('js/ueditor/lang/zh-cn/zh-cn.js') }}"></script>
    <script src="{{ asset('js/ueditor/ueditor-insert-images.js') }}"></script>
@endpush
@section('admin-body')
    <div class="layui-form layui-form-pane" lay-filter="default-form" style="margin: 15px 15px 0 0">
        <div class="layui-form-item">
            <label class="layui-form-label required">标题</label>
            <div class="layui-input-block">
                <input type="text" name="title" class="layui-input" autocomplete="off" maxlength="32" placeholder="请输入标题，限长32个字符">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">排序</label>
            <div class="layui-input-block">
                <input type="number" name="sort" class="layui-input" autocomplete="off" placeholder="请输入排序值[-999,999]" value="0">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label required">显示状态</label>
            <div class="layui-input-block">
                <select name="is_show">
                    <option value="1" selected>是</option>
                    <option value="0">否</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label required">公告内容</label>
            <div class="layui-input-block">
                <script id="container" name="content" type="text/plain" style="height: 500px"></script>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button id="btn-default" type="button" class="layui-btn" lay-submit lay-filter="*">新建</button>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        layui.use(['form', 'layer', 'jquery'], function(){
            const form = layui.form, layer = layui.layer, $ = layui.$, ue = UE.getEditor('container');
            $('#btn-default').click(function () {
                let data = form.val('default-form');
                data.content = ue.getContent();
                data._token = '{{ csrf_token() }}';
                $.ajax({
                    method: 'post',
                    url: '{{ route('api.news.add') }}',
                    data: data,
                    success: function (ert) {
                        if (ert.code === 422) {
                            layer.msg(getValidationMsg(ert.data));
                            return;
                        }
                        if (ert.code === 0) {
                            layer.msg(ert.msg, {time:500}, function () {
                                window.location.href = '{{ route('view.news.list') }}';
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
