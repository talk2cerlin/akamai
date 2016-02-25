<?php

namespace Akamai\Util;

use Dotenv\Dotenv;

class ConfigLoader
{

    protected $parser;

    protected $path;

    public function __construct()
    {
        $this->path = __DIR__."/../";
    }

    public function load($path = null)
    {
        $this->parser = new Dotenv(($path) ? $path : $this->path);
        $this->parser->overload();
        $this->addRules();
        return $this->parser;
    }

    public function addRules()
    {
        $this->parser->required('name')->notEmpty();
    }
}
