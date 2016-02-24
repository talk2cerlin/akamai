<?php

namespace Akamai\Facades;

use Akamai\Akamai;

class AkamaiFacade
{

    public static $instance = null;

    protected static function initialize()
    {
        if (self::$instance == null) {
            self::$instance = new Akamai();
        }
    }

    public static function __callStatic($method, $args)
    {
        self::initialize();
        switch (count($args)) {
            case 0:
                return self::$instance->$method();

            case 1:
                return self::$instance->$method($args[0]);

            case 2:
                return self::$instance->$method($args[0], $args[1]);

            case 3:
                return self::$instance->$method($args[0], $args[1], $args[2]);

            case 4:
                return self::$instance->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array([self::$instance, $method], $args);
        }
    }
}
