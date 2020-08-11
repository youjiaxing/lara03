<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;

class UploadImageRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'type' => ['required', 'in:avatar,topic'],
            'image' => ['required', 'mimes:jpg,jpeg,png,bmp']
        ];

        if ($this->input('type') == 'avatar') {
            $rules['image'][] = 'dimensions:min_width=200,min_height=200';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'type.in' => '图片业务类型错误',
            'image.mimes' => '图片格式错误',
            'image.dimensions' => '图片分辨率不足, 要求至少宽高200以上'
        ];
    }


}
