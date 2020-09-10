<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'Admin\UserController@loginView')->middleware('guest');
Route::get('/admin', 'Admin\UserController@loginView')->name('view.user.login')->middleware('guest');
Route::post('/admin/user/login', 'Admin\UserController@login')->name('api.user.login');

Route::prefix('admin')->namespace('Admin')->middleware(['auth:web', 'annotation'])->group(function () {
    // 用户
    Route::get('/user/list_view', 'UserController@listView')->name('view.user.list');
    Route::get('/user/add_view', 'UserController@addView')->name('view.user.add');
    Route::get('/user/edit_view', 'UserController@editView')->name('view.user.edit');
    Route::get('/user/logout', 'UserController@logout')->name('api.user.logout');
    Route::post('/user/change_password', 'UserController@changePassword')->name('api.user.change_password');
    Route::post('/user/edit_self', 'UserController@editSelf')->name('api.user.edit_self');
    Route::post('/user/list', 'UserController@list')->name('api.user.list');
    Route::post('/user/add', 'UserController@add')->name('api.user.add');
    Route::post('/user/edit', 'UserController@edit')->name('api.user.edit');
    Route::post('/user/delete', 'UserController@delete')->name('api.user.delete');
    Route::post('/user/reset_password', 'UserController@resetPassword')->name('api.user.reset_password');

    // 角色
    Route::get('/role/list_view', 'RoleController@listView')->name('view.role.list');
    Route::get('/role/add_view', 'RoleController@addView')->name('view.role.add');
    Route::get('/role/edit_view', 'RoleController@editView')->name('view.role.edit');
    Route::post('/role/list', 'RoleController@list')->name('api.role.list');
    Route::post('role/add', 'RoleController@add')->name('api.role.add');
    Route::post('role/edit', 'RoleController@edit')->name('api.role.edit');
    Route::post('role/delete', 'RoleController@delete')->name('api.role.delete');

    // 上传图片至相册
    Route::post('/upload/image/album', 'UploadController@uploadImageToAlbum')->name('api.album_image.upload');
    // 富文本内上传图片
    Route::post('/upload/image', 'UploadController@uploadImage')->name('api.image.upload_image');
    // 设置-相册管理
    Route::get('/album/management_view', 'AlbumController@managementView')->name('view.album.management_view');
    Route::post('/config/album/add', 'AlbumController@addAlbum')->name('api.album.add');
    Route::post('/config/album/edit', 'AlbumController@editAlbum')->name('api.album.edit');
    Route::post('/config/album/editImage', 'AlbumController@editImage')->name('api.album.editImage');
    Route::post('/config/album/delete', 'AlbumController@deleteAlbum')->name('api.album.delete');
    Route::get('/config/album/images', 'AlbumController@getAlbumImage')->name('api.album.image.load');
    Route::post('/config/album_list/album/delete', 'AlbumController@deleteAlbum')->name('api.album.delete');
    Route::post('/config/album/image/delete', 'AlbumController@deleteImage')->name('api.album.image.delete');
    Route::get('/config/album_photo_selector', 'AlbumController@albumPhotoSelector')->name('album.photo.selector');

    // 欢迎页
    Route::get('/welcome', 'WelcomeController@welcomeView')->name('view.welcome.welcome');

    // 设置页
    Route::get('/config/app', 'ConfigController@appView')->name('view.config.app');
    Route::post('/config/edit_app', 'ConfigController@editApp')->name('api.config.edit_app');
    Route::get('/config/sms', 'ConfigController@smsView')->name('view.config.sms');
    Route::post('/config/edit_sms', 'ConfigController@editSms')->name('api.config.edit_sms');
    Route::get('/config/wechat_public', 'ConfigController@wechatPublicView')->name('view.config.wechat_public');
    Route::post('/config/edit_wechat_public', 'ConfigController@editWechatPublic')->name('api.config.edit_wechat_public');
    Route::get('/config/wechat_template', 'ConfigController@wechatTemplateView')->name('view.config.wechat_template');
    Route::post('/config/edit_wechat_template', 'ConfigController@editWechatTemplate')->name('api.config.edit_wechat_template');
    // 首页管理-轮播图
    Route::get('/carousel/list_view', 'CarouselController@listView')->name('view.carousel.list');
    Route::get('/carousel/add_view', 'CarouselController@addView')->name('view.carousel.add');
    Route::get('/carousel/edit_view', 'CarouselController@editView')->name('view.carousel.edit');
    Route::post('/carousel/list', 'CarouselController@list')->name('api.carousel.list');
    Route::post('/carousel/add', 'CarouselController@add')->name('api.carousel.add');
    Route::post('/carousel/edit', 'CarouselController@edit')->name('api.carousel.edit');
    Route::post('/carousel/delete', 'CarouselController@delete')->name('api.carousel.delete');
    // 首页管理-导航栏
    Route::get('/nav/list_view', 'NavController@listView')->name('view.nav.list');
    Route::get('/nav/add_view', 'NavController@addView')->name('view.nav.add');
    Route::get('/nav/edit_view', 'NavController@editView')->name('view.nav.edit');
    Route::post('/nav/list', 'NavController@list')->name('api.nav.list');
    Route::post('/nav/add', 'NavController@add')->name('api.nav.add');
    Route::post('/nav/edit', 'NavController@edit')->name('api.nav.edit');
    Route::post('/nav/delete', 'NavController@delete')->name('api.nav.delete');
    // 首页管理-公告
    Route::get('/news/list_view', 'NewsController@listView')->name('view.news.list');
    Route::get('/news/add_view', 'NewsController@addView')->name('view.news.add');
    Route::get('/news/edit_view', 'NewsController@editView')->name('view.news.edit');
    Route::post('/news/list', 'NewsController@list')->name('api.news.list');
    Route::post('/news/add', 'NewsController@add')->name('api.news.add');
    Route::post('/news/edit', 'NewsController@edit')->name('api.news.edit');
    Route::post('/news/delete', 'NewsController@delete')->name('api.news.delete');
});
