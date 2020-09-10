UE.registerUI('insert-image', function (editor, uiName) {
    let $ = layui.$;
    return new UE.ui.Button({
        name: uiName,
        title: '插入图片',
        cssRules: 'background-position: -725px 42px;',
        onclick: function () {
            layer.open({
                type: 2,
                id: 'photo_selector',
                anim: 1,
                resize: false,
                area: ['950px', '700px'],
                title: '相册选择（最多只能选择5张' + '）',
                content: '/admin/config/album_photo_selector?max=5',
                btn: ['确认', '取消'],
                btnAlign: 'c',
                yes: function(index, layero) {
                    let images = [];
                    layero.find('iframe').contents().find('.img-item.a').each(function (index, elem) {
                        images.push({id: $(elem).data('id'), url: $(elem).data('url')});
                    });
                    if (images.length === 0) {
                        layer.msg('请至少选择一张图片');
                        return false;
                    } else {
                        layer.close(index);
                        let imageHtml = '';
                        for (let i = 0; i < images.length; i++) {
                            let d = images[i];
                            imageHtml += `<img src="${d.url}" _src="${d.url}">`;
                        }
                        editor.execCommand('inserthtml', imageHtml);
                    }
                }
            });
        }
    });
});
