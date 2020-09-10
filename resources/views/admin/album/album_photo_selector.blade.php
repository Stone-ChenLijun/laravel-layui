@extends('admin/layouts/base')

@push('head')
    <link rel="stylesheet" href="{{ asset('layui_ext/dtree/dtree.css') }}">
    <link rel="stylesheet" href="{{ asset('layui_ext/dtree/font/dtreefont.css')}}">
    <script src="{{ asset('layui_ext/dtree/dtree.js') }}"></script>
    <style>
        html, body {
            height: 100%;
            background-color: #eeeeee;
            overflow-y: hidden;
        }
        .album-tree-container {
            background-color: white;
            width: 240px;
            min-width: 100px;
            height: auto;
            overflow-x: auto;
        }
        .photo-container {
            background-color: white;
            width: 100%;
            margin-left: 10px;
            padding: 5px;
        }
        #photo_flow {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 5px;
            overflow-y: auto;
            max-height: 90%;
        }
        .img-item {
            width: 100px;
            height: 100px;
            max-width: 100px;
            max-height: 100px;
            position: relative;
            border: 2px solid #eee;
            margin-left: 8px;
            margin-top: 8px;
            line-height: 100px;
        }
        .img-item:hover {
            cursor: pointer;
        }
        .a:after {
            content: '';
            position: absolute;
            bottom: 1px;
            right: 5px;
            border-color: #009933;
            border-style: solid;
            border-width: 0 0.3em 0.25em 0;
            height: 1em;
            width: 0.5em;
            z-index: 1;
            transform: rotate(45deg);
        }
        .a {
            border: 2px solid #009933 ;
        }
        .img-item>img {
            width: 100px;
            height: auto;
	    max-height: 100px;
        }
    </style>
@endpush

@section('body')
    <div style="display: flex;height: 100%;">
        <div class="album-tree-container">
            <ul id="album_tree" class="dtree" data-id="0"></ul>
        </div>
        <div class="photo-container">
            <div class="photo-ctl-panel">
                <button type="button" class="layui-btn" id="upload_image">
                    <i class="layui-icon layui-icon-upload"></i>上传图片
                </button>
                <span>限制大小5M，限制格式png,jpg,jpeg,gif</span>
            </div>
            <div id="photo_flow"></div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        layui.extend({
            dtree: '/layui_ext/dtree/dtree'   // {/}的意思即代表采用自有路径，即不跟随 base 路径
        }).use(['dtree', 'layer', 'upload', 'flow'], function () {
            const dtree = layui.dtree, layer = layui.layer, $ = layui.jquery, upload = layui.upload, flow = layui.flow;
            let album_id = 0;
            //执行实例
            var uploadInst = upload.render({
                elem: '#upload_image'
                , url: "{{ route('api.album_image.upload') }}"
                , size: 5120
                , accept: 'images'
                , exts: 'png|jpg|jpeg|gif'
                , multiple: true
                , data: {
                    _token: '{{ csrf_token() }}',
                    album_id: function () {
                        return album_id;
                    }
                }
                , done: function (res) {
                    layer.msg(res.msg);
                    loadFlow();
                }
                , error: function () {
                    if (ert.status === 422) {
                        layer.msg(getValidationMsg(ert.responseJSON.errors), null, function () {
                            window.location.reload();
                        });
                    } else {
                        layer.msg(ert.responseJSON.message);
                    }
                }
            });

            // 初始化树
            var DemoTree = dtree.render({
                elem: "#album_tree",
                type: "all",
                data: @json($tree),
                initLevel: 2,
                width: 150,
                toolbar: true,
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

            $('#confirm').click(function () {
                let checkStatus = table.checkStatus('album_photo_pop_table');
                localStorage.setItem('selected_photos', JSON.stringify(checkStatus.data));
                let index = parent.layer.getFrameIndex(window.name);
                parent.layer.close(index);
            });

            function loadFlow() {
                $('#photo_flow').empty();
                flow.load({
                    elem: '#photo_flow',
                    isLazyimg: true,
                    scrollElem: '#photo_flow',
                    isAuto: true,
                    end: '<div style="margin-left: 15px;">--已加载全部--</div>',
                    done: function (page, next) {
                        var list = [];
                        $.get('{{ route('api.album.image.load') }}?page_size=30&page=' + page, {album_id: album_id}, function (res) {
                            if (res.code !== 0) {
                                return false;
                            }
                            layui.each(res.data.data, function (index, item) {
                                list.push(`
                                    <li class="img-item" data-url="${item.url}" data-id="${item.id}"><img lay-src="${item.url}"></li>
                                `);
                            })
                            next(list.join(''), page <= res.data.last_page);
                        });
                    }
                });
            }
            loadFlow();

            let max = {{ request()->get('max', 1) }};

            $('#photo_flow').on('click', 'li', function (obj) {
                if (!$(this).hasClass('a') && $('li.a').length >= max) {
                    layer.msg('最多只能选择' + max + '张图片');
                    return false;
                }
                $(this).toggleClass('a');
            });
            // 绑定节点点击
            dtree.on("node('album_tree')", function (obj) {
                album_id = obj.param.nodeId;
                loadFlow();
            });
        });
    </script>
@endpush
