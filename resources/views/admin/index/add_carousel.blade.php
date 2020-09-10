@extends('admin.layouts.admin')

@push('head')
    <script src="{{ asset('js/photo_selector.js') }}"></script>
@endpush
@section('admin-body')
    <div class="layui-form layui-form-pane do-form-half" lay-filter="default-form">
        <div class="layui-form-item">
            <label class="layui-form-label required">标题</label>
            <div class="layui-input-block">
                <input type="text" name="title" class="layui-input" autocomplete="off" maxlength="32" placeholder="请输入标题，限长32个字符">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label required">图片</label>
            <div class="layui-input-block">
                <input type="hidden" name="image_id" class="layui-input" autocomplete="off" maxlength="32">
                <img class="chose-image" src="{{ asset('images/default_cover.gif') }}">
                <button type="button" class="layui-btn layui-btn-normal btn-chose-image">选择图片</button>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">跳转链接</label>
            <div class="layui-input-block">
                <input type="text" name="url" class="layui-input" autocomplete="off" placeholder="请输入跳转链接，如http://t.cn，#不跳转">
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
            <div class="layui-input-block">
                <button id="btn-default" type="button" class="layui-btn" lay-submit lay-filter="*">新建</button>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        layui.use(['form', 'layer', 'jquery'], function(){
            const form = layui.form, layer = layui.layer, $ = layui.$;
            $('.btn-chose-image').click(function () {
                openAlbum(1, function (images) {
                    $('.chose-image').attr('src', images[0].url);
                    $('input[name="image_id"]').val(images[0].id);
                })
            });
            $('#btn-default').click(function () {
                let data = form.val('default-form');
                data._token = '{{ csrf_token() }}';
                $.ajax({
                    method: 'post',
                    url: '{{ route('api.carousel.add') }}',
                    data: data,
                    success: function (ert) {
                        if (ert.code === 422) {
                            layer.msg(getValidationMsg(ert.data));
                            return;
                        }
                        if (ert.code === 0) {
                            layer.msg(ert.msg, {time:500}, function () {
                                window.location.href = '{{ route('view.carousel.list') }}';
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
