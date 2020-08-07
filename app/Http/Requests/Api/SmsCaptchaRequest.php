<?php

namespace App\Http\Requests\Api;

use App\Exceptions\CaptchaVerifyException;
use App\Services\ImageCaptchaService;

class SmsCaptchaRequest extends \App\Http\Requests\Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'captcha_key' => ['required', 'string'],
            'captcha_code' => [
                'required',
                'string',
                'between:1,6',
                function ($attribute, $value, $fail) {
                    $key = $this->request->get('captcha_key');
                    $captchaService = app(ImageCaptchaService::class);
                    try {
                        $captchaService->verifyApi(ImageCaptchaService::TYPE_REGISTER_PHONE, $key, $value, $ext = ['phone' => $this->request->get('phone')
                        ]);
                    } catch (CaptchaVerifyException $e) {
                        $fail($e->getMessage());
                        return;
                    }
                }
            ],
            'phone' => [
                'required_if:type,register_phone',
                'string',
                'size:11',
                'regex:/[1-9][0-9]{10}/',
                'unique:users,phone'
            ],
        ];
    }

    public function messages()
    {
        return [
            'phone.regex' => '手机号无效',
            'phone.unique' => '手机号已存在',
        ];
    }


}
