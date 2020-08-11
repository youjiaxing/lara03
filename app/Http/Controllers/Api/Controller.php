<?php
/**
 *
 * @author : 尤嘉兴
 * @version: 2020/8/5 17:57
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller extends \App\Http\Controllers\Controller
{
    protected $headers = [];

    /**
     * @param array  $data
     * @param string $msg
     * @param int    $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($data = [], string $msg = "", int $statusCode = 200)
    {
        return json_success_response($data, $msg, $statusCode, $this->headers);
    }

    public function errorResponse(int $statusCode, string $msg = "", int $subCode = 0, $data = [])
    {
        return json_error_response($statusCode, $msg, $subCode, $data);
    }
}