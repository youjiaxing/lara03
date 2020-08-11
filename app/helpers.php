<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function category_nav_active($category_id)
{
    return active_class((if_route('categories.show') && if_route_param('category', $category_id)));
}

function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return Str::limit($excerpt, $length);
}

function model_admin_link($title, $model)
{
    return model_link($title, $model, 'admin');
}

function model_link($title, $model, $prefix = '')
{
    // 获取数据模型的复数蛇形命名
    $model_name = model_plural_name($model);

    // 初始化前缀
    $prefix = $prefix ? "/$prefix/" : '/';

    // 使用站点 URL 拼接全量 URL
    $url = config('app.url') . $prefix . $model_name . '/' . $model->id;

    // 拼接 HTML A 标签，并返回
    return '<a href="' . $url . '" target="_blank">' . $title . '</a>';
}

function model_plural_name($model)
{
    // 从实体中获取完整类名，例如：App\Models\User
    $full_class_name = get_class($model);

    // 获取基础类名，例如：传参 `App\Models\User` 会得到 `User`
    $class_name = class_basename($full_class_name);

    // 蛇形命名，例如：传参 `User`  会得到 `user`, `FooBar` 会得到 `foo_bar`
    $snake_case_name = Str::snake($class_name);

    // 获取子串的复数形式，例如：传参 `user` 会得到 `users`
    return Str::plural($snake_case_name);
}


/**
 * @param array  $data
 * @param string $msg
 * @param int    $statusCode
 * @param array  $headers
 *
 * @return \Illuminate\Http\JsonResponse
 */
function json_success_response($data = [], string $msg = "", int $statusCode = 200, $headers = [], int $subCode = 0)
{
    // ResourceCollection 保留分页的 meta 和 links 信息
    if ($data instanceof \Illuminate\Http\Resources\Json\ResourceCollection){
        // 让 ResourceCollection 所有数据在 'data' 字段下
        // $data = $data->toResponse(request())->getData(true);

        // meta 和 links 作为顶级字段, data 仅包含 collection 的有效数据(不包含元数据)
        return $data->additional(['message' => $msg, 'code' => $statusCode])->toResponse(request())->setStatusCode($statusCode)->withHeaders($headers);
    }

    return response()->json(
        [
            'code' => $statusCode,
            'sub_code' => $subCode,
            'data' => $data,
            'message' => $msg,
        ],
        $statusCode,
        $headers
    );
}

function json_error_response(int $statusCode, string $msg = "", int $subCode = 0, $data = [])
{
    return json_success_response($data, $msg, $statusCode, [], $subCode);
}

function re_phone($full = true)
{
    $base = '[0-9][1-9]{10}';
    return $full ? '/^' . $base . '$/' : $base;
}