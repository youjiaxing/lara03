<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class AuthRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'key' => [
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
