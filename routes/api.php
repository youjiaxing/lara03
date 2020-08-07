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

Route::prefix("v1")->name("api.v1.")->namespace('Api')->group(function () {
    Route::middleware('throttle:' . config('api.rate_limit.sign'))->group(function () {
        // 获取短信验证码
        Route::post('/captcha/sms', 'CaptchaController@storeSms');
        // 用户注册 - 手机号
        Route::post('/register/phone', 'AuthController@registerByPhone');
    });

    Route::middleware('throttle:' . config('api.rate_limit.default'))->group(function () {
        // 获取图形验证码
        Route::post('/captcha/img', 'CaptchaController@storeImg');
    });

    Route::middleware("auth:api")->group(function () {
        Route::get("/test", function () {
            return json_success([], "test ok");
        });
    });
});