<?php
/**
 *
 * @author : 尤嘉兴
 * @version: 2020/8/6 15:53
 */

namespace App\Http\Requests\Api;

use App\Services\SmsCaptchaService;

class UserRequest extends \App\Http\Requests\Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'between:3,25',
            ],
            'phone' => [
                'required_if:type,register_phone',
                'string',
                'size:11',
                'regex:/[1-9][0-9]{10}/',
                'unique:users,phone'
            ],
            'password' => [
                'required',
                'between:6,20',
            ],
            'sms_code' => [
                'required',
                'string',
                'between:1,6',
                function ($attribute, $value, $fail) {
                    $smsCaptchaService = app(SmsCaptchaService::class);
                    try {
                        $smsCaptchaService->verifyApi($this->get('phone'), $value);
                    } catch (\Throwable $e) {
                        $fail($e->getMessage());
                        return;
                    }
                }
            ],
        ];
    }

    public function messages()
    {
        return [
            'phone.size' => '手机号无效',
            'phone.regex' => '手机号无效',
            'phone.unique' => '手机号已存在',
        ];
    }
}