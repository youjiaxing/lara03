<?php
/**
 *
 * @author : 尤嘉兴
 * @version: 2020/8/6 15:46
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class AuthController extends Controller
{
    public function registerByPhone(UserRequest $request)
    {
        $data = $request->validated();
        $user = User::create(
            [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'password' => bcrypt($data['password']),
            ]
        );
        $token = \Auth::guard('api')->login($user);
        return $this->success(
            [
                'token' => $token,
                'user' => new UserResource($user),
            ]
        );
    }
}