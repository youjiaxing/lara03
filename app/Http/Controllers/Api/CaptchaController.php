<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ThrottleHandler;
use App\Http\Requests\Api\ImageCaptchaRequest;
use App\Http\Requests\Api\SmsCaptchaRequest;
use App\Services\ImageCaptchaService;
use App\Services\SmsCaptchaService;

class CaptchaController extends Controller
{

    /**
     * 创建图形验证码
     *
     * @param ImageCaptchaRequest $request
     * @param ImageCaptchaService $captchaService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeImg(ImageCaptchaRequest $request, ImageCaptchaService $captchaService)
    {
        $type = $request->input('type');
        $ext = [];

        if ($type == ImageCaptchaService::TYPE_REGISTER_PHONE) {
            $ext['phone'] = $request->input('phone');
        }

        //TODO 可以根据 type 规则, 读取 TTL, VerifyLimit 配置

        $captcha = $captchaService->createApi($type, 5 * 60, 3, $ext);

        $resp = [
            'base64' => $captcha['base64'],
            'key' => $captcha['key'],
            'expire' => $captcha['expire']->toDateTimeString(),
        ];

        if (app()->isLocal()) {
            $resp['code'] = $captcha['code'];
        }

        return $this->success($resp);
    }

    /**
     * 短信验证码 - 注册
     *
     * @param SmsCaptchaRequest $request
     * @param SmsCaptchaService $smsCaptchaService
     * @param ThrottleHandler   $throttle
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function storeSms(SmsCaptchaRequest $request, SmsCaptchaService $smsCaptchaService, ThrottleHandler $throttle)
    {
        $phone = $request->input('phone');
        // 针对手机号请求次数限制
        $throttle->limits(
            $phone,
            [
                60 * 24 => 20,
                60 => 10,
                1 => 1,
            ]
        );

        [$code, $expire] = $smsCaptchaService->sendRegisterCaptcha($phone, 5 * 60, 3);

        $resp = [
            'expire' => $expire->toDateTimeString(),
        ];

        if (app()->isLocal()) {
            $resp['code'] = $code;
        }

        return $this->success($resp);
    }
}
