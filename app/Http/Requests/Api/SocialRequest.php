<?php
/**
 *
 * @author : 尤嘉兴
 * @version: 2020/8/6 15:53
 */

namespace App\Http\Requests\Api;

use App\Services\SmsCaptchaService;

class SocialRequest extends \App\Http\Requests\BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => [
                'required',
                'string',
            ]
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}