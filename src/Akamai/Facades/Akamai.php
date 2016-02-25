<?php

namespace Akamai\Facades;

use Akamai\Akamai as AkamaiCore;

class Akamai extends BaseFacade
{

    public static $instance = null;

    protected static function initialize()
    {
        if (self::$instance == null) {
            self::$instance = new AkamaiCore();
        }
    }
}
