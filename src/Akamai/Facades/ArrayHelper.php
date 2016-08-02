<?php

namespace Akamai\Facades;

use Akamai\Util\ArrayHelper as CoreArrayHelper;

class ArrayHelper extends BaseFacade
{

    public static $instance = null;

    protected static function initialize()
    {
        if (self::$instance == null) {
            self::$instance = new CoreArrayHelper();
        }
    }
}
