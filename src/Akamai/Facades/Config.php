<?php

namespace Akamai\Facades;

use Akamai\Util\ConfigLoader;

class Config extends BaseFacade
{

    public static $instance = null;

    protected static function initialize()
    {
        if (self::$instance == null) {
            self::$instance = new ConfigLoader();
        }
    }
}
