<?php


return [
    [
        'name' => '首页管理',
        'action' => 'admin.index',
        'children' => [
            [
                'name' => '轮播图',
                'action' => 'admin.carousel.list',
                'children' => [
                    [
                        'name' => '添加',
                        'action' => 'admin.carousel.add',
                    ],
                    [
                        'name' => '修改',
                        'action' => 'admin.carousel.edit',
                    ],
                    [
                        'name' => '删除',
                        'action' => 'admin.carousel.delete',
                    ],
                ],
            ],
            [
                'name' => '导航栏',
                'action' => 'admin.nav.list',
                'children' => [
                    [
                        'name' => '添加',
                        'action' => 'admin.nav.add',
                    ],
                    [
                        'name' => '修改',
                        'action' => 'admin.nav.edit',
                    ],
                    [
                        'name' => '删除',
                        'action' => 'admin.nav.delete',
                    ],
                ]
            ],
            [
                'name' => '公告信息',
                'action' => 'admin.news.list',
                'icon' => 'layui-icon-read',
                'children' => [
                    [
                        'name' => '添加',
                        'action' => 'admin.news.add',
                    ],
                    [
                        'name' => '修改',
                        'action' => 'admin.news.edit',
                    ],
                    [
                        'name' => '删除',
                        'action' => 'admin.news.delete',
                    ],
                ]
            ],
        ]
    ],
    [
        'name' => '用户管理',
        'action' => 'admin.user_management',
        'children' => [
            [
                'name' => '用户',
                'action' => 'admin.user.list',
                'children' => [
                    [
                        'name' => '重置密码',
                        'action' => 'admin.user.reset_password',
                    ],
                    [
                        'name' => '添加',
                        'action' => 'admin.user.add',
                    ],
                    [
                        'name' => '修改',
                        'action' => 'admin.user.edit',
                    ],
                    [
                        'name' => '删除',
                        'action' => 'admin.user.delete',
                    ],
                ]
            ],
            [
                'name' => '角色',
                'action' => 'admin.role.list',
                'children' => [
                    [
                        'name' => '添加',
                        'action' => 'admin.role.add',
                    ],
                    [
                        'name' => '修改',
                        'action' => 'admin.role.edit',
                    ],
                    [
                        'name' => '删除',
                        'action' => 'admin.role.delete',
                    ],
                ]
            ],
        ]
    ],
    [
        'name' => '设置',
        'action' => 'admin.config',
        'children' => [
            [
                'name' => '基础设置',
                'action' => 'admin.config.app',
            ],
            [
                'name' => '短信配置',
                'action' => 'admin.config.sms',
            ],
            [
                'name' => '公众号配置',
                'action' => 'admin.config.wechat_public',
            ],
            [
                'name' => '模板消息配置',
                'action' => 'admin.config.wechat_template',
            ],
            [
                'name' => '相册管理',
                'action' => 'admin.album',
            ],
        ]
    ],
];
