<?php

namespace Akamai\Util;

use Dotenv\Dotenv;
use Akamai\Model\AkamaiConfiguration;

class ConfigLoader
{

    protected $parser;

    protected $path;

    protected $flag = false;

    public function __construct()
    {
        $this->path = getcwd();
    }

    public function loadFromENV($path = null, $name = null)
    {
        $this->parser = new Dotenv(($path) ? $path : $this->path, ($name) ? $name : ".env.akamai");
        $this->parser->overload();
        $this->addRules();
        $this->flag = true;
        return $this->getAkamaiConfig();
    }

    private function addRules()
    {
        $this->parser->required('AKAMAI_HOST')->notEmpty();
        $this->parser->required('AKAMAI_KEY')->notEmpty();
        $this->parser->required('AKAMAI_KEYNAME')->notEmpty();
    }

    private function get($key)
    {
        return getenv($key);
    }

    // public function setAkamaiC

    public function getAkamaiConfig()
    {
        if (!$this->flag) {
            $this->loadFromENV();
        }
        return [
            "AKAMAI_HOST" => $this->get("AKAMAI_HOST"),
            "AKAMAI_KEY" => $this->get("AKAMAI_KEY"),
            "AKAMAI_KEYNAME" => $this->get("AKAMAI_KEYNAME")
        ];
    }
}
