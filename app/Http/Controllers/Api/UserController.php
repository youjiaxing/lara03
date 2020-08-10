<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\User as UserResource;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends Controller
{
    function show(User $user)
    {
        return $this->success(UserResource::make($user));
    }

    function me(Request $request)
    {
        return $this->success(UserResource::make(\Auth::user())->showSensitive(true));
    }

    function activeList()
    {
        $users = (new User())->getActiveUsers();
        return $this->success(UserResource::collection($users));
    }

    function update(UserRequest $request)
    {
        $data = $request->only('name', 'introduction');
        if ($avatarId = $request->input('avatar_id')) {
            $data['avatar'] = Image::query()->findOrFail($avatarId)->path;
        }

        if (empty($data)) {
            throw new BadRequestHttpException("没有需要更新的字段");
        }

        if (!\Auth::user()->update($data)) {
            throw new \Exception("更新失败");
        }

        return $this->success(UserResource::make(\Auth::user())->showSensitive(true));
    }
}
