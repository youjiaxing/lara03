<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function show(User $user)
    {
        return $this->success(new UserResource($user));
    }

    function me(Request $request)
    {
        $user = $request->user();
        return $this->success((new UserResource($user))->showSensitive(true));
    }
}
