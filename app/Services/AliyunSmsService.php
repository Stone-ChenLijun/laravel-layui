<?php


namespace App\Services;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AliyunSmsService
{
    private $accessKeyId;
    private $accessKeySecret;
    private $templateCode;
    private $sign;

    public function __construct()
    {
        $config = app('app.config')->scope(AppConfigService::SCOPE_SMS);
        $this->accessKeyId = $config->get('app_id')->value;
        $this->accessKeySecret = $config->get('app_secret')->value;
        $this->sign = $config->get('sign')->value;
        $this->templateCode = $config->get('template_code')->value;
    }

    public function sendCaptcha($phone, $code)
    {
        if (strlen($code) == 0) {
            return false;
        }
        $params = [
            'PhoneNumbers' => $phone,
            'TemplateCode' => $this->templateCode,
            'SignName' => $this->sign,
            'TemplateParam' => json_encode([
                'code' => $code,
                'product' => 'cs',
            ], JSON_UNESCAPED_UNICODE)
        ];
        return $this->send($params);
    }

    private function send($params, $secure = true, $method = 'POST') {
        if (!is_array($params)) {
            return false;
        }
        // 短信接口公共请求头参数
        $params = array_merge($params, [
            'AccessKeyId' => $this->accessKeyId,
            'Action' => 'SendSms',
            'Format' => 'json',
            'RegionId' => 'cn-hangzhou',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce' => Str::random(),
            'SignatureVersion' => '1.0',
            'Timestamp' => gmdate("Y-m-d\TH:i:s\Z"),
            'Version' => '2017-05-25'
        ]);
        Arr::forget($params, 'Signature');
        ksort($params);
        $collection = collect($params);
        $collection = $collection->map(function ($value, $key) {
            return $this->specialUrlEncode($key).'='.$this->specialUrlEncode($value);
        });
        $collection = $collection->values();
        $sortedQueryString = $collection->join('&');
        $unSignedString = "{$method}&{$this->specialUrlEncode('/')}&{$this->specialUrlEncode($sortedQueryString)}";
        $signedString = base64_encode(hash_hmac("sha1", $unSignedString, $this->accessKeySecret . "&", true));
        $collection->prepend($this->specialUrlEncode('Signature').'='.$this->specialUrlEncode($signedString));
        $url = ($secure ? "https" : "http")."://dysmsapi.aliyuncs.com";
        $content = $this->fetchContent($url, $method, "Signature={$signedString}&{$sortedQueryString}");
        $result = json_decode($content, true);
        if (Arr::get($result, 'Code') !== 'OK') {
            return false;
        }
        return ['transaction_id' => Arr::get($result, 'RequestId')];
    }

    private function specialUrlEncode($string) {
        $res = urlencode($string);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    private function fetchContent($url, $method, $body)
    {

        $ch = curl_init();

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        } else {
            $url .= '?' . $body;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "x-sdk-client" => "php/2.0.0"
        ));

        if (substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $rtn = curl_exec($ch);

        if ($rtn === false) {
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);

        return $rtn;
    }
}
