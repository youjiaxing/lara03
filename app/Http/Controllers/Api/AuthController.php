<?php
/**
 *
 * @author : 尤嘉兴
 * @version: 2020/8/6 15:46
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthRequest;
use App\Http\Requests\Api\RegisterByPhoneRequest;
use App\Http\Requests\Api\SocialRequest;
use App\Http\Resources\User as UserResource;
use App\Models\User;
use App\Services\SocialService;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function registerByPhone(RegisterByPhoneRequest $request)
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
                'user' => (new User($user))->showSensitive(true),
            ]
        );
    }

    public function login(AuthRequest $request)
    {
        $credentials = [
            'password' => $request->input('password'),
        ];

        $key = $request->input('key');
        // 邮件
        if (filter_var($key, FILTER_VALIDATE_EMAIL) != false) {
            $credentials['email'] = $key;
        } // 手机号
        elseif (preg_match('/^[1-9][0-9]{10}$/', $key)) {
            $credentials['phone'] = $key;
        } else {
            throw ValidationException::withMessages(['key' => ['格式错误']]);
        }

        if (!$token = \Auth::guard('api')->attempt($credentials)) {
            throw ValidationException::withMessages(['key' => [trans('auth.failed')]]);
        }

        return $this->responseToken($token, auth('api')->user());
    }

    public function refresh()
    {
        $token = auth('api')->refresh(true);
        return $this->responseToken($token);
    }

    public function logout()
    {
        \Auth::guard('api')->logout();
        return $this->success([], "", 204);
    }

    // https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxe575c72d965dec82&redirect_uri=http://lara03.test&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect
    public function socialLogin(SocialRequest $request, $social, SocialService $socialService)
    {
        $token = $request->input('code');
        $user = $socialService->login($social, $token);
        $token = \Auth::guard('api')->login($user);
        return $this->responseToken($token, $user);
    }

    protected function responseToken($token, User $user = null)
    {
        $data = [
            'token' => $token,
            'token_ttl' => \Auth::guard('api')->factory()->getTTL() * 60,
        ];
        if ($user) {
            $data['user'] = UserResource::make(new User($user))->showSensitive(true);
        }

        return $this->success(
            $data
        );
    }
}