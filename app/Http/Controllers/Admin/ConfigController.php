<?php

namespace App\Http\Controllers\Admin;

use App\Annotations\Permission;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper;
use App\Models\AlbumImage;
use App\Services\AppConfigService;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    use ResponseHelper;

    public function appView()
    {
        $config = app('app.config')->scope(AppConfigService::SCOPE_APP);
        $logoId = $config->get('logo_id', 0)->value;
        $logo = AlbumImage::where('id', $logoId)->first();
        $logoUrl = $logo ? $logo->preview_url : asset('images/default_cover.gif');
        $defaultClassesCoverImageId = $config->get('default_classes_cover_image_id', 0)->value;
        $defaultClassesCover = AlbumImage::where('id',  $defaultClassesCoverImageId)->first();
        $defaultClassesCoverUrl = $defaultClassesCover ? $defaultClassesCover->preview_url : asset('images/default_cover.gif');
        return view('admin/config/app', [
            'site_name' => $config->get('site_name', config('app.name'))->value,
            'logo_id' => $logoId,
            'logo_url' => $logoUrl,
            'default_classes_cover_image_id' => $defaultClassesCoverImageId,
            'default_classes_cover_image_url' => $defaultClassesCoverUrl,
            'icp' => $config->get('icp', '浙ICP备18044046号')->value,
            'police_icp' => $config->get('police_icp')->value,
            'consumer_hotline' => $config->get('consumer_hotline')->value,
            'normal_order_cancel_minutes' => $config->get('normal_order_cancel_minutes', 30)->value,
            'unpaid_normal_order_notify_minutes' => $config->get('unpaid_normal_order_notify_minutes', 10)->value,
            'reservation_order_cancel_hours' => $config->get('reservation_order_cancel_hours', 72)->value,
            'unpaid_reservation_order_notify_hours' => $config->get('unpaid_reservation_order_notify_hours', 24)->value,
        ]);
    }

    public function smsView()
    {
        $config = app('app.config')->scope(AppConfigService::SCOPE_SMS);
        return view('admin/config/sms', [
            'app_id' => $config->get('app_id')->value,
            'app_secret' => $config->get('app_secret')->value,
            'sign' => $config->get('sign')->value,
            'template_code' => $config->get('template_code')->value,
        ]);
    }

    public function wechatPublicView()
    {
        $config = app('app.config')->scope(AppConfigService::SCOPE_WECHAT_PUBLIC);
        return view('admin/config/wechat_public', [
            'app_id' => $config->get('app_id')->value,
            'app_secret' => $config->get('app_secret')->value,
            'pay_app_id' => $config->get('pay_app_id')->value,
            'pay_app_secret' => $config->get('pay_app_secret')->value,
        ]);
    }

    public function wechatTemplateView()
    {
        $config = app('app.config')->scope(AppConfigService::SCOPE_WECHAT_TEMPLATE);
        return view('admin/config/wechat_template', [
            'notify_pay_order' => $config->get('notify_pay_order')->value,
            'notify_order_refund' => $config->get('notify_order_refund')->value,
            'approval_result_notify' => $config->get('approval_result_notify')->value,
        ]);
    }

    /**
     * 编辑站点基本信息
     * @Permission(action="admin.config.app")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editApp(Request $request)
    {
        $this->validate($request, [
            'site_name' => 'required|string|between:1,10',
            'logo_id' => 'sometimes|integer',
            'icp' => 'sometimes|string|max:40',
            'police_icp' => 'sometimes|string|max:40',
        ], [], [
            'site_name' => '站点名称',
            'logo_id' => 'logo图标',
            'icp' => 'icp备案号',
            'police_icp' => '公安备案号',
        ]);
        $config = app('app.config')->scope(AppConfigService::SCOPE_APP);
        if ($request->logo_id > 0) {
            $image = AlbumImage::find($request->logo_id);
            if (empty($image)) {
                return $this->respond(-1, '指定图片未找到');
            }
            if (!$config->set('logo_id', $image->id)) {
                return $this->respond(-1, '修改logo失败');
            }
        } elseif ($request->logo_id == 0 && !$config->set('logo_id', 0)) {
            return $this->respond(-1, '修改logo失败！');
        }
        $result = collect(['site_name', 'icp', 'police_icp', ])->every(function ($item) use ($request, $config) {
            return $config->set($item, $request->$item);
        });
        return $result ? $this->success_msg('修改成功') : $this->respond(-1, '修改失败');
    }

    /**
     * 编辑短信配置信息
     * @Permission(action="admin.config.sms")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editSms(Request $request)
    {
        $this->validate($request, [
            'app_id' => 'present|string|max:64',
            'app_secret' => 'present|string|max:64',
            'sign' => 'present|string|max:64',
            'template_code' => 'present|string|max:64',
        ], [], [
            'app_id' => '访问密钥id',
            'app_secret' => '访问密钥',
            'sign' => '签名',
            'template_code' => '模板编码',
        ]);
        $config = app('app.config')->scope(AppConfigService::SCOPE_SMS);
        $result = collect(['app_id', 'app_secret', 'sign', 'template_code'])->every(function ($item) use ($config, $request) {
            return $config->set($item, $request->$item);
        });
        return $result ? $this->success_msg('修改成功') : $this->respond(-1, '修改失败');
    }

    /**
     * 编辑公众号
     * @Permission(action="admin.config.wechat_public")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editWechatPublic(Request $request)
    {
        $this->validate($request, [
            'app_id' => 'present|string|max:64',
            'app_secret' => 'present|string|max:64',
            'pay_app_id' => 'present|string|max:64',
            'pay_app_secret' => 'present|string|max:64',
        ], [], [
            'app_id' => '访问密钥id',
            'app_secret' => '访问密钥',
            'pay_app_id' => '商户id',
            'pay_app_secret' => '商户密钥',
        ]);
        $config = app('app.config')->scope(AppConfigService::SCOPE_WECHAT_PUBLIC);
        $result = collect(['app_id', 'app_secret', 'pay_app_id', 'pay_app_secret'])->every(function ($item) use ($config, $request) {
            return $config->set($item, $request->$item);
        });
        return $result ? $this->success_msg('修改成功') : $this->respond(-1, '修改失败');
    }

    /**
     * 编辑公众号通知模板
     * @Permission(action="admin.config.wechat_public")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editWechatTemplate(Request $request)
    {
        $this->validate($request, [
            'notify_pay_order' => 'present|string|max:64',
            'notify_order_refund' => 'present|string|max:64',
            'approval_result_notify' => 'present|string|max:64',
        ], [], [
            'notify_pay_order' => '订单付款提醒',
            'notify_order_refund' => '订单退款通知',
            'approval_result_notify' => '审核结果通知',
        ]);
        $config = app('app.config')->scope(AppConfigService::SCOPE_WECHAT_TEMPLATE);
        $result = collect(['notify_pay_order', 'notify_order_refund', 'approval_result_notify'])->every(function ($item) use ($config, $request) {
            return $config->set($item, $request->$item);
        });
        return $result ? $this->success_msg('修改成功') : $this->respond(-1, '修改失败');
    }
}
