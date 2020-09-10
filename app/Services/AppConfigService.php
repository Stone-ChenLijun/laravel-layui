<?php


namespace App\Services;


use App\Models\AlbumImage;
use App\Models\Config;

class AppConfigService
{
    const SCOPE_APP = 'app';
    const SCOPE_SMS = 'app.sms';
    const SCOPE_WECHAT_PUBLIC = 'app.wechat.public';
    const SCOPE_WECHAT_TEMPLATE = 'app.wechat.template';
    const SCOPE_ORDER = 'app.order';
    protected $scope;

    public function __construct($scope = self::SCOPE_APP)
    {
        $this->scope = $scope;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    public function scope($scope = self::SCOPE_APP)
    {
        $this->scope = $scope;
        return $this;
    }

    public function get($key, $default = '')
    {
        return Config::query()->where('scope', $this->scope)->firstOrCreate(['key' => $key], ['scope' => $this->getScope(), 'value' => $default]);
    }

    public function set($key, $value)
    {
        $item = Config::query()->where('scope', $this->scope)->where('key', $key)->first();
        if (empty($item)) {
            $item = new Config();
        }
        $item->fill(['scope' => $this->getScope(), 'key' => $key, 'value' => $value]);
        return $item->save();
    }

    public function all($keys = null)
    {
        $query = Config::query()->where('scope', $this->scope);
        if (is_null($keys)) {
            return $query->get();
        }
        return $query->whereIn('key', $keys)->get();
    }

    public function getSiteLogoUrl()
    {
        $siteLogo = Config::query()->where('scope', self::SCOPE_APP)->firstOrCreate(['key' => 'logo_id'],
            ['scope' => self::SCOPE_APP, 'value' => 0]);
        $image = AlbumImage::where('id', $siteLogo->value)->first();
        return $image ? $image->preview_url : asset('images/logo.png');
    }

    public function getSiteName()
    {
        $siteName = Config::query()->where('scope', self::SCOPE_APP)->firstOrCreate(['key' => 'site_name'],
            ['scope' => self::SCOPE_APP, 'value' => config('app.name')]);
        return $siteName->value;
    }

    public function getPublicConfig()
    {
        $config = app('app.config')->scope(AppConfigService::SCOPE_WECHAT_PUBLIC);
        return [
            'app_id' => $config->get('app_id')->value,
            'secret' => $config->get('app_secret')->value,
        ];
    }

    public function getWechatPayConfig()
    {
        $config = app('app.config')->scope(AppConfigService::SCOPE_WECHAT_PUBLIC);
        return [
            'app_id' => $config->get('app_id')->value,
            'mch_id' => $config->get('pay_app_id')->value,
            'key' => $config->get('pay_app_secret')->value,
            'notify_url' => route('wap.common.wechat_pay_notify'),
            'cert_path' => storage_path('wechat_cert/apiclient_cert.pem'),
            'key_path' => storage_path('wechat_cert/apiclient_key.pem'),
        ];
    }
}
