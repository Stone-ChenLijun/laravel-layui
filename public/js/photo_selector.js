function openAlbum(max, yes) {
    let $ = layui.$;
    if (max <= 1) {
        max = 1;
    }
    layer.open({
        type: 2,
        id: 'photo_selector',
        anim: 1,
        resize: false,
        area: ['950px', '700px'],
        title: '相册选择（最多只能选择' + max + '张' + '）',
        content: '/admin/config/album_photo_selector?max=' + max,
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
                if (yes !== null) {
                    yes(images);
                }
            }
        }
    });
}
