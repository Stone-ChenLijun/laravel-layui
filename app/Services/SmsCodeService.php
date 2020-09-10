<?php


namespace App\Services;


use App\Models\Member;
use App\Models\SmsRecord;
use Carbon\Carbon;

class SmsCodeService
{

    public function sendCaptcha($type, $phone, $code)
    {
        $phoneExist = Member::where('phone', $phone)->exists();
        switch ($type) {
            case SmsRecord::TYPE_REGISTER:
                if ($phoneExist) {
                    return '手机号已注册，请直接登录';
                }
                break;
            default:
                return '不支持的验证码类型';
        }
        if (!env('SAVE_SMS', false)) {
            $sender = app('aliyun.sms');
            $result = $sender->sendCaptcha($phone, $code);
            if ($result === false) {
                return '发送验证码失败';
            }
        }
        $record = new SmsRecord();
        $record->phone = $phone;
        $record->type = $type;
        $record->code = $code;
        $record->expire_at = Carbon::now()->addMinutes(3)->toDateTimeString();
        $record->sender = 'aliyun';
        $record->transaction_id = $result['transaction_id'] ?? '';
        if (!$record->save()) {
            return '发送验证码失败！';
        }
        return true;
    }

    /**
     * 校验验证码，通过返回数据库中对应的记录，否则返回失败理由
     * @param $type string 验证码类型
     * @param $phone string 手机号
     * @param $code string 验证码
     * @return string|SmsRecord 验证结果
     */
    public function validate($type, $phone, $code)
    {
        $record = SmsRecord::where('type', $type)->where('is_used', false)
            ->where('phone', $phone)->orderBy('expire_at', 'desc')->first();
        if (empty($record)) {
            return '请先发送验证码';
        }
        if ($record->code != $code) {
            return '验证码错误';
        }
        if (strtotime($record->expire_at) < time()) {
            return '验证码已过期';
        }
        return $record;
    }

    /**
     * 使用验证码
     * @param $id integer|SmsRecord 验证码id
     * @return bool 启用结果
     */
    public function use($id)
    {
        $record = $id instanceof SmsRecord ? $id : SmsRecord::where('is_used', false)->where('id', $id)->first();
        if (empty($record)) {
            return false;
        }
        $record->is_used = true;
        return $record->save();
    }
}
