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
use App\Traits\PassportToken;
use Laminas\Diactoros\Response as Psr7Response;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Http\Controllers\ConvertsPsrResponses;
use League\OAuth2\Server\Exception\OAuthServerException as LeagueException;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends Controller
{
    use ConvertsPsrResponses;
    use PassportToken;

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

        return $this->successResponse([], '', 201);

        // $token = \Auth::login($user);
        // return $this->successResponse(
        //     [
        //         'token' => $token,
        //         'user' => (new UserResource($user))->showSensitive(true),
        //     ]
        // );
    }

    public function login(AuthRequest $request, \League\OAuth2\Server\AuthorizationServer $authorizationServer, ServerRequestInterface $serverRequest)
    {
        /*
        $credentials = [
            'password' => $request->input('password'),
        ];

        $username = $request->input('username');
        // 邮件
        if (filter_var($username, FILTER_VALIDATE_EMAIL) != false) {
            $credentials['email'] = $username;
        } // 手机号
        elseif (preg_match('/^[1-9][0-9]{10}$/', $username)) {
            $credentials['phone'] = $username;
        } else {
            throw ValidationException::withMessages(['key' => ['格式错误']]);
        }

        if (!$token = \Auth::guard('api')->attempt($credentials)) {
            throw ValidationException::withMessages(['key' => [trans('auth.failed')]]);
        }

        return $this->responseToken($token, auth('api')->user());
        */

        try {
            $psrResponse = $authorizationServer->respondToAccessTokenRequest($serverRequest, new Psr7Response);

            return $this->successResponse(json_decode($psrResponse->getBody(), true), "", 201, $psrResponse->getHeaders());
        } catch (LeagueException $e) {
            // return $this->errorResponse($e->getHttpStatusCode(), $e->getMessage(), 0, $e->getTrace());
            throw new OAuthServerException(
                $e,
                $this->convertResponse($e->generateHttpResponse(new Psr7Response))
            );
        }
    }

    public function refresh(\League\OAuth2\Server\AuthorizationServer $authorizationServer, ServerRequestInterface $serverRequest)
    {
        // $token = auth('api')->refresh(true);
        // return $this->responseToken($token);

        return $this->login(new AuthRequest(), $authorizationServer, $serverRequest);
    }

    public function logout()
    {
        // \Auth::guard('api')->logout();
        // return $this->successResponse([], "", 204);
        \Auth::user()->token()->revoke();
        return $this->successResponse([], '', 204);
    }

    // https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxe575c72d965dec82&redirect_uri=http://lara03.test&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect
    public function socialLogin(SocialRequest $request, $social, SocialService $socialService)
    {
        $token = $request->input('code');
        $user = $socialService->login($social, $token);
        $tokenData = $this->getBearerTokenByUser($user,"3", false);
        return $this->successResponse($tokenData);
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

        return $this->successResponse(
            $data
        );
    }
}