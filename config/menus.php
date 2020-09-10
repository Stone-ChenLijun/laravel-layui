<?php

return [
    [
        'name' => '欢迎页',
        'match' => '^/admin/welcome',
        'url' => '/admin/welcome',
        'icon' => 'layui-icon-chart-screen',
    ],
    [
        'name' => '首页管理',
        'icon' => 'layui-icon-app',
        'permission_action' => 'admin.index',
        'children' => [
            [
                'name' => '轮播图',
                'match' => '^/admin/carousel/',
                'url' => '/admin/carousel/list_view',
                'permission_action' => 'admin.carousel.list',
                'icon' => 'layui-icon-carousel',
            ],
            [
                'name' => '导航栏',
                'match' => '^/admin/nav/',
                'url' => '/admin/nav/list_view',
                'permission_action' => 'admin.nav.list',
                'icon' => 'layui-icon-carousel',
            ],
            [
                'name' => '公告',
                'match' => '^/admin/news/',
                'url' => '/admin/news/list_view',
                'permission_action' => 'admin.news.list',
                'icon' => 'layui-icon-read',
            ],
        ]
    ],
    [
        'name' => '用户管理',
        'url' => '/admin/user/list_view',
        'permission_action' => 'admin.user_management',
        'icon' => 'layui-icon-group',
        'children' => [
            [
                'name' => '用户管理',
                'match' => '^/admin/user/',
                'url' => '/admin/user/list_view',
                'permission_action' => 'admin.user.list',
                'icon' => 'layui-icon-user',
            ],
            [
                'name' => '角色管理',
                'match' => '^/admin/role/',
                'url' => '/admin/role/list_view',
                'permission_action' => 'admin.role.list',
                'icon' => 'layui-icon-group',
            ],
        ]
    ],
    [
        'name' => '设置',
        'url' => '/admin/config/app',
        'icon' => 'layui-icon-app',
        'permission_action' => 'admin.member_management',
        'children' => [
            [
                'name' => '基础设置',
                'match' => '^/admin/config/app',
                'url' => '/admin/config/app',
                'permission_action' => 'admin.config.app',
                'icon' => 'layui-icon-set',
            ],
            [
                'name' => '短信配置',
                'match' => '^/admin/config/sms',
                'url' => '/admin/config/sms',
                'permission_action' => 'admin.config.sms',
                'icon' => 'layui-icon-set',
            ],
            [
                'name' => '公众号配置',
                'match' => '^/admin/config/wechat_public',
                'url' => '/admin/config/wechat_public',
                'permission_action' => 'admin.config.wechat_public',
                'icon' => 'layui-icon-set',
            ],
            [
                'name' => '模板消息配置',
                'match' => '^/admin/config/wechat_template',
                'url' => '/admin/config/wechat_template',
                'permission_action' => 'admin.config.wechat_template',
                'icon' => 'layui-icon-set',
            ],
            [
                'name' => '相册管理',
                'match' => '^/admin/album/',
                'url' => '/admin/album/management_view',
                'permission_action' => 'admin.album',
                'icon' => 'layui-icon-picture',
            ]
        ]
    ],
];
