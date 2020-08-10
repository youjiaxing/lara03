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
        // 限流--登录
        Route::middleware('throttle:' . config('api.rate_limit.sign'))->group(
            function () {
                // 获取短信验证码
                Route::post('/captcha/sms', 'CaptchaController@storeSms');
                // 用户注册 - 手机号
                Route::post('/auth/register/phone', 'AuthController@registerByPhone');
            }
        );

        // 限流--普通
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

                // 活跃用户列表
                Route::get('/users/activate', 'UserController@activeList');
                // 用户
                Route::get('/users/{user}', 'UserController@show')->name('users.show');

                // 文章分类列表
                Route::get('/categories', 'CategoryController@index');
                // 分类下的文章列表
                Route::get('/categories/{category}', 'CategoryController@show');

                // 文章列表
                Route::get('/topics', 'TopicController@index');
                // 文章详情
                Route::get('/topics/{topic}', 'TopicController@show');
                // 用户的文章列表
                Route::get('/users/{user}/topics', 'TopicController@index');

                // 推荐链接(PY 交易链接)
                Route::get('/links', 'LinkController@index');

                Route::middleware("auth:api")->group(
                    function () {
                        // 登出
                        Route::delete('/auth/logout', 'AuthController@logout');

                        // 获取个人资料
                        Route::get('/user', 'UserController@me');
                        // 更新个人资料
                        Route::patch('/user', 'UserController@update');

                        // 上传图片
                        Route::post('/images', 'UploadController@storeImage');

                        // 创建文章
                        Route::post('/topics', 'TopicController@store');
                        // 删除文章
                        Route::delete('/topics/{topic}', 'TopicController@destroy');
                        // 修改文章
                        Route::match(['PUT', 'PATCH'], '/topics/{topic}', 'TopicController@update');
                    }
                );
            }
        );
    }
);