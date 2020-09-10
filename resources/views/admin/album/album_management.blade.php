@extends('admin.layouts.admin')
@push('head')
    <link rel="stylesheet" href="{{ asset('layui_ext/dtree/dtree.css') }}">
    <link rel="stylesheet" href="{{ asset('layui_ext/dtree/font/dtreefont.css')}}">
@endpush
@section('body-class', 'album-management')
@section('admin-body')
    <div class="layui-fluid album-management-container">
        <div class="layui-row">
            <div class="layui-col-md3 album-tree-container">
                <ul id="album-tree" class="dtree" data-id="0"></ul>
            </div>
            <div class="layui-col-md9 album-image-container">
                <div class="upload-image-panel">
                    <button type="button" class="layui-btn" id="upload_image">
                        <i class="layui-icon layui-icon-upload"></i>上传图片
                    </button>
                    <span>限制大小5M，限制格式png,jpg,jpeg,gif</span>
                </div>
                <table id="photo_list" lay-filter="photo_list"></table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        layui.extend({
            dtree: '/layui_ext/dtree/dtree'   // {/}的意思即代表采用自有路径，即不跟随 base 路径
        }).use(['dtree', 'layer', 'jquery', 'table', 'upload'], function () {
            const dtree = layui.dtree, layer = layui.layer, $ = layui.jquery, table = layui.table, upload = layui.upload;
            var album_id = 0;
            // 初始化树
            var DemoTree = dtree.render({
                elem: "#album-tree",
                type: "all",
                data: @json($tree),
                initLevel: 2,
                toolbar: true,
                toolbarWay: 'follow',
                response: {
                    title: 'name',
                    parentId: 'parent_id'
                },
                toolbarFun: {
                    addTreeNode: function (treeNode, $div) {
                        $.ajax({
                            url: '{{ route("api.album.add") }}',
                            method: 'post',
                            data: {
                                _token: '{{ csrf_token() }}',
                                parent_id: treeNode.parentId,
                                name: treeNode.addNodeName
                            },
                            success: function (ert) {
                                if (ert.code === 0) {
                                    DemoTree.changeTreeNodeAdd(ert.data);
                                }
                                layer.msg(ert.msg);
                            },
                            error: function (ert) {
                                if (ert.status === 422) {
                                    layer.msg(getValidationMsg(ert.responseJSON.errors));
                                } else {
                                    layer.msg(ert.responseJSON.message);
                                }
                                DTree1.changeTreeNodeAdd(false);
                            }
                        });
                    },
                    editTreeNode: function (treeNode, $div) {
                        $.ajax({
                            url: '{{ route("api.album.edit") }}',
                            method: 'post',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: null,
                                name: 'treeNode.editNodeName'
                            },
                            success: function (ert) {
                                if (ert.code === 0) {
                                    DemoTree.changeTreeNodeEdit(true);
                                } else {
                                    DemoTree.changeTreeNodeEdit(false);
                                }
                                layer.msg(ert.msg);
                            },
                            error: function (ert) {
                                DemoTree.changeTreeNodeEdit(false);
                                if (ert.status === 422) {
                                    layer.msg(getValidationMsg(ert.responseJSON.errors));
                                } else {
                                    layer.msg(ert.responseJSON.message);
                                }
                            }
                        });
                    },
                    delTreeNode: function (treeNode, $div) {
                        $.ajax({
                            url: '{{ route("api.album.delete") }}',
                            method: 'post',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: treeNode.nodeId,
                            },
                            success: function (ert) {
                                if (ert.code === 0) {
                                    DemoTree.changeTreeNodeDel(true);
                                } else {
                                    DemoTree.changeTreeNodeDel(false);
                                }
                                layer.msg(ert.msg);
                            },
                            error: function (ert) {
                                DemoTree.changeTreeNodeDel(false);
                                if (ert.status === 422) {
                                    layer.msg(getValidationMsg(ert.responseJSON.errors));
                                } else {
                                    layer.msg(ert.responseJSON.message);
                                }
                            }
                        });
                    }
                }
            });

            const uploadIns = upload.render({
                elem: '#upload_image'
                , url: "{{ route('api.album_image.upload') }}"
                , size: 5120
                , acceptMime: 'image/*'
                , number: 5
                , multiple: true
                , data: {
                    _token: '{{ csrf_token() }}',
                    album_id: function () {
                        return album_id;
                    }
                }
                , done: function (res) {
                    layer.msg(res.msg);
                    tableIns.reload({
                        where: {
                            album_id: album_id
                        }
                    });
                }
                , error: function (ert) {
                    if (ert.status === 422) {
                        layer.msg(getValidationMsg(ert.responseJSON.errors), null, function () {
                            window.location.reload();
                        });
                    } else {
                        layer.msg(ert.responseJSON.message);
                    }
                }
            });

            let tableIns = table.render({
                elem: '#photo_list',
                even: true,
                url: '{{ route('api.album.image.load') }}',
                skin: 'line',
                method: 'get',
                height: 'full-200',
                id: 'album_photo_table',
                where: {
                    album_id: album_id
                },
                page: true,
                cols: [[
                    {field: 'key', title: '序号', fixed: 'left', width: 150, type: 'numbers'}
                    , {field: 'name', title: '文件名', align: 'center'}
                    , {
                        field: 'path', title: '图片(点击预览)', align: 'center', event: 'preview', templet: function (d) {
                            return '<img class="ablum-image" src="' + d.url + '">';
                        }
                    }
                    , {
                        field: 'size', title: '大小', width: 150, align: 'center', templet: function (d) {
                            return '<span>' + formatFileSize(d.size) + '</span>';
                        }
                    }
                    , {field: 'created_at', title: '上传时间', align: 'center'}
                    , {
                        field: 'operation',
                        title: '操作',
                        width: 200,
                        align: 'center',
                        fixed: 'right',
                        toolbar: '#operation'
                    }
                ]],
                text: {
                    none: '暂无内容'
                },
                request: {
                    pageName: 'page',
                    limitName: 'page_size'
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

            // 绑定节点点击
            dtree.on("node('demoTree')", function (obj) {
                album_id = obj.param.nodeId;
                tableIns.reload({
                    where: {
                        album_id: album_id
                    }
                });
            });

            table.on('tool(photo_list)', function (obj) {
                if (obj.event === 'preview') {
                    layer.photos({
                        photos: {
                            "title": "预览", //相册标题
                            "data": [
                                {
                                    "alt": obj.data.name,
                                    "pid": obj.data.id,
                                    "src": obj.data.url,
                                }
                            ]
                        }
                    });
                } else if (obj.event === 'del') {
                    let dialog = layer.confirm('是否删除图片', {
                        btn: ['删除', '取消'],
                        yes: function () {
                            $.ajax({
                                url: "{{ route('api.album.image.delete') }}",
                                method: 'post',
                                data: {
                                    id: obj.data.id,
                                    _token: '{{ csrf_token() }}',
                                },
                                success: function (ert) {
                                    if (ert.code === 422) {
                                        layer.msg(getValidationMsg(ert.responseJSON.errors));
                                        return;
                                    }
                                    layer.msg(ert.msg);
                                    table.reload('album_photo_table');
                                },
                                error: function (ert) {
                                    layer.msg('服务器错误');
                                }
                            });
                            layer.close(dialog);
                        }
                    });
                } else if (obj.event === 'edit') {
                    layer.prompt({
                        value: obj.data.name,
                        title: '修改文件名'
                    }, function (value, index, elem) {
                        $.ajax({
                            url: "{{ route('api.album.editImage') }}",
                            method: 'post',
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: obj.data.id,
                                name: value,
                            },
                            success: function (res) {
                                if (res.code === 422) {
                                    layer.msg(getValidationMsg(ert.responseJSON.errors));
                                    return;
                                }
                                layer.msg(res.msg);
                                if (res.code === 0) {
                                    layer.close(index);
                                    table.reload('album_photo_table');
                                }
                            },
                            error: function (ert) {
                                layer.msg('服务器错误');
                            }
                        })
                    })
                }
            });

            function formatFileSize(fileSize) {
                if (fileSize < 1024) {
                    return fileSize + 'B';
                } else if (fileSize < (1024 * 1024)) {
                    var temp = fileSize / 1024;
                    temp = temp.toFixed(2);
                    return temp + 'KB';
                } else if (fileSize < (1024 * 1024 * 1024)) {
                    var temp = fileSize / (1024 * 1024);
                    temp = temp.toFixed(2);
                    return temp + 'MB';
                } else {
                    var temp = fileSize / (1024 * 1024 * 1024);
                    temp = temp.toFixed(2);
                    return temp + 'GB';
                }
            }
        });
    </script>
    <script type="text/html" id="operation">
        <div class="layui-btn-group">
            <button class="layui-btn layui-btn-sm" lay-event="edit"><i class="layui-icon layui-icon-edit"></i></button>
            <button class="layui-btn layui-btn-sm" lay-event="del"><i class="layui-icon layui-icon-delete"></i></button>
        </div>
    </script>
@endpush
