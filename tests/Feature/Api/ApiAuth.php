<?php
/**
 *
 * @author : 尤嘉兴
 * @version: 2020/8/11 14:59
 */

namespace Tests\Feature\Api;

trait ApiAuth
{
    protected $user;

    /**
     * @param string $guard
     *
     * @return $this
     */
    public function prepare($guard = 'api')
    {
        return $this->be($this->user, 'api');
    }
}