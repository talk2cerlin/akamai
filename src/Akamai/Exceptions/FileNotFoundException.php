<?php

namespace Akamai\Exceptions;

use Akamai\Exceptions\BaseException;

class FileNotFoundException extends BaseException
{

    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}
