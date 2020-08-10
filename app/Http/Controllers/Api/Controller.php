<?php
/**
 *
 * @author : 尤嘉兴
 * @version: 2020/8/5 17:57
 */

namespace App\Http\Controllers\Api;

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
    public function success($data = [], string $msg = "", int $code = 200)
    {
        return json_success($data, $msg, $code, $this->headers);
    }

    // public function
}