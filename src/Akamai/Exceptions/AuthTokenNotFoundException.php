<?php

namespace Akamai\Exceptions;

use Akamai\Exceptions\BaseException;

class AuthTokenNotFoundException extends BaseException
{

    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}
