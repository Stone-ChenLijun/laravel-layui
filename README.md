# 简介
不分离项目模板，主要用于小型项目快速开发。

基于 Laravel 6.x 的前后端不分离项目模板，管理后台采用 Layui 2.5.6。

提供基本的登录，角色权限，相册，富文本，发送短信等功能。

为商城类应用提供常见的导航栏，轮播图，公告配置。

左侧菜单目前仅支持二层菜单。

此项目仅作为快速开始的项目模板，如遇功能差异，请自行修改。

此项目目前仅包含大多数项目中重复出现的功能。

# 部署
```
// 复制 .env.example 文件为 .env，修改APP_URL与数据库链接配置
cp .env.example .env
// 安装依赖
composer install
// 创建加密key
php artisan key:generate
// 创建 public/storage 到 storage 的软链接
php artisan storage:link
// 创建数据库表并填充基础数据
php artisan migrate --seed
```
