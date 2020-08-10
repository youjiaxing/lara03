<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix("v1")->name("api.v1.")->namespace('Api')->group(
    function () {
        Route::middleware('throttle:' . config('api.rate_limit.sign'))->group(
            function () {
                // 获取短信验证码
                Route::post('/captcha/sms', 'CaptchaController@storeSms');
                // 用户注册 - 手机号
                Route::post('/auth/register/phone', 'AuthController@registerByPhone');
            }
        );

        Route::middleware('throttle:' . config('api.rate_limit.default'))->group(
            function () {
                // 获取图形验证码
                Route::post('/captcha/img', 'CaptchaController@storeImg');

                // 第三方登录
                Route::post('/social/{social}', 'AuthController@socialLogin')->where('social', 'weixin');

                // 非第三方登录
                Route::post('/auth', 'AuthController@login');

                // 刷新 access_token
                Route::post('/auth/refresh', 'AuthController@refresh');

                Route::get('/users/{user}', 'UserController@show')->name('users.show');

                Route::middleware("auth:api")->group(
                    function () {
                        // 登出
                        Route::delete('/auth/logout', 'AuthController@logout');

                        Route::get('/user', 'UserController@me');
                    }
                );
            }
        );
    }
);