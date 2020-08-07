<?php
/**
 *
 * @author : 尤嘉兴
 * @version: 2020/8/6 14:41
 */

namespace App\Handlers;

use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;

class ThrottleHandler extends ThrottleRequestsWithRedis
{
    protected $keyPrefix = "throttle:";

    public function limit($key, $maxAttempts = 60, $decayMinutes = 1)
    {
        if ($this->tooManyAttempts($this->keyPrefix . $key, $maxAttempts, $decayMinutes)) {
            throw $this->buildException($key, $maxAttempts);
        }
    }

    /**
     * @param string $key
     * @param array $limits = [
     *     'minutes' => 'maxAttempts',
     * ]
     */
    public function limits($key, $limits)
    {
        foreach ($limits as $minutes => $maxAttempts) {
            $this->limit($key . "_minutes_" . $minutes, $maxAttempts, $minutes);
        }
    }
}