<?php

namespace App\Providers;

use App\Services\AliyunSmsService;
use App\Services\AppConfigService;
use App\Services\ClassesService;
use App\Services\MenuService;
use App\Services\OrderService;
use App\Services\SmsCodeService;
use App\Services\WechatTemplateMessageService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('app.config', function ($app) {
            return new AppConfigService();
        });
        $this->app->singleton('menu', function ($app) {
            return new MenuService();
        });
        $this->app->singleton('sms', function ($app) {
            return new SmsCodeService();
        });
        $this->app->singleton('aliyun.sms', function ($app) {
            return new AliyunSmsService();
        });
        $this->app->singleton('wechat.template_message', function () {
            return new WechatTemplateMessageService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
