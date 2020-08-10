<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;
use App\Models\Image;
use Auth;
use Illuminate\Validation\Rule;

class UserRequest extends Request
{
    public function rules()
    {
        return [
            'name' => 'between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name,' . Auth::id(),
            'introduction' => 'max:80',
            'avatar_id' => [Rule::exists('images','id')->where('user_id', Auth::id())->where('type', Image::TYPE_AVATAR)],
            // 'avatar_id' => ['exists:images,id'],
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => '用户名已被占用，请重新填写',
            'name.regex' => '用户名只支持英文、数字、横杠和下划线。',
            'name.between' => '用户名必须介于 3 - 25 个字符之间。',
            'name.required' => '用户名不能为空。',
        ];
    }
}
