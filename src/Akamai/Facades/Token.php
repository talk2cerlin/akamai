<?php

namespace Akamai\Facades;

use Akamai\Util\Token as TokenGenerator;

class Token extends BaseFacade
{

    public static $instance = null;

    protected static function initialize()
    {
        if (self::$instance == null) {
            self::$instance = new TokenGenerator();
        }
    }
}
