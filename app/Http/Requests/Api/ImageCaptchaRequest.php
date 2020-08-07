<?php
namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class ImageCaptchaRequest extends \App\Http\Requests\Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 具体业务
            'type' => ['required', 'string', 'between:1,20', 'alpha_dash'],

            'phone' => [
                'required_if:type,register_phone',
                'string',
                'size:11',
                'regex:/[1-9][0-9]{10}/',
                'unique:users,phone',
            ]
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
