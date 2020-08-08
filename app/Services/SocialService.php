<?php
/**
 *
 * @author : 尤嘉兴
 * @version: 2020/8/7 23:49
 */

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Laravel\Socialite\Facades\Socialite;

class SocialService
{
    const DRIVER_WEIXIN = 'weixin';

    // protected $refreshTokenExpire = [
    //     'weixin' => 30 * 24 * 3600,
    // ];

    /**
     * @param string $driver
     * @param string $token
     *
     * @return User
     * @throws AuthenticationException
     */
    public function login($driver, $token)
    {
        try {
            $socialDriver = Socialite::driver($driver);
            /*
             * 微信 oauth2 正确时的返回结果
             * @var array $socialResponse = [
             * "access_token" => "36_MUvQAjVTlwtCXD6vSz0XGdncmvxP8-AoeJJU8fYWkP-AJKaEG15p3An2uYavAqNOiP0LSnJFexQ96Oy_Fo8ubA",
             * "expires_in" => 7200,
             * "refresh_token" => "36_0VLfi8JiHK3xzgECAeY1j87AOHsi6n60CsrSaw3f_CVF98_HMOfVJhOu81eFQ6DOYiEP364VtRHb8UPA7unSnw",
             * "openid" => "o7fx30bq2Fjh95oDLT58KpiJm6zQ",
             * "scope" => "snsapi_userinfo",
             * ]
             */
            $socialResponse = @$socialDriver->getAccessTokenResponse($token);
            if (empty($socialResponse['openid'])) {
                throw new AuthenticationException("token 无效");
            }

            $socialUser = $socialDriver->userFromToken($socialResponse['access_token']);

            $extraData = [];
            if ($driver == self::DRIVER_WEIXIN && isset($socialResponse['unionid'])) {
                $extraData['weixin_unionid'] = $socialResponse['unionid'];
            }

            $openId = $socialUser->id;
            // 若不存在用户, 则创建
            $openIdKey = $driver . "_openid";
            if (is_null($user = User::query()->where($openIdKey, $openId)->first())) {
                $user = User::create(
                    array_merge(
                        [
                            $openIdKey => $openId,
                            'avatar' => $socialUser->avatar,
                            'name' => $socialUser->nickname,
                            'email' => $socialUser->email,
                        ],
                        $extraData
                    )
                );
            }

            return $user;
        } catch (\Throwable $e) {
            throw new AuthenticationException($e->getMessage());
        }
    }

    // protected function cacheTokens($userId, $driver, $accessToken, $refreshToken, $accessTokenExpire)
    // {
    //     $key = $this->getCacheKey($userId, $driver);
    //     $accessTokenTTL = now()->addSeconds($accessTokenExpire - 60);
    //     $refreshTokenTTL = now()->addSeconds(Arr::get($this->refreshTokenExpire, $driver, 30*24*3600) - 60);
    //     $data = [
    //         // 'driver' => $driver,
    //         'access_token' => $accessToken,
    //         'refresh_token' => $refreshToken,
    //         'access_token_ttl' => $accessTokenTTL,
    //         'refresh_token_ttl' => $refreshTokenTTL,
    //     ];
    //     \Cache::set($key, $data, $refreshTokenTTL);
    // }
    //
    // protected function getCacheKey($userId, $driver)
    // {
    //     return "social_token:${userId}_$driver";
    // }
    //
    // public function getTokens($userId, $driver)
    // {
    //     $key = $this->getCacheKey($userId, $driver);
    //     $data = \Cache::get($key, []);
    //     if (empty($data)) {
    //         return [];
    //     }
    //
    //     if ($data['access_token_ttl']->isPast()) {
    //         if ($data['refresh_token_ttl']->isPast()) {
    //             \Cache::forget($key);
    //             return [];
    //         }
    //         // 通过 refreshToken 获取 accessToken
    //          这里碰壁了, 无法使用 socialite 来刷新 access_token....
    //
    //     }
    // }
}