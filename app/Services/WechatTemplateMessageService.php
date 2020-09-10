<?php


namespace App\Services;


use App\Models\Order;
use App\Models\OrderRemind;
use Carbon\Carbon;
use EasyWeChat\Factory;

class WechatTemplateMessageService
{
    protected $appConfig;
    protected $templateConfig;
    protected $officialAccount;
    public function __construct()
    {
        $this->appConfig = app('app.config')->scope(AppConfigService::SCOPE_APP);
        $this->templateConfig = app('app.config')->scope(AppConfigService::SCOPE_WECHAT_TEMPLATE);
        $this->officialAccount = Factory::officialAccount(app('app.config')->getPublicConfig());
    }
}
