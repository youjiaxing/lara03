<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;

class ReplyRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => ['required', 'min:2', 'max:10000']
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'content' => '回复内容',
        ];
    }


}
