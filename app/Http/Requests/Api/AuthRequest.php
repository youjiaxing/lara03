<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;

class AuthRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => [
                'required',
                'string',
            ],
            'password' => [
                'required',
                'string',
            ]
        ];
    }
}
