<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CaptchaVerifyException extends Exception
{
    const ERR_INVALID = 1;
    const ERR_CODE_NO_MATCH = 2;
    const ERR_EXT_NO_MATCH = 3;
    const ERR_TYPE_NO_MATCH = 4;

    public $context = [];
    public $err;

    public function __construct($err, $message = "", $context = [])
    {
        $this->err = $err;
        $this->context = $context;
        parent::__construct($message);
    }

}
