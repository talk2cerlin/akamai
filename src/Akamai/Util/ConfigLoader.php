<?php

namespace Akamai\Util;

use Dotenv\Dotenv;
use Akamai\Exceptions\InvalidDataTypeFoundException;
use Akamai\Exceptions\InvalidKeyFoundException;
use Akamai\Exceptions\DotEnvException;

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
        try {
            $this->parser = new Dotenv(($path) ? $path : $this->path, ($name) ? $name : ".env.akamai");
            $this->parser->overload();
        } catch (\Exception $e) {
            throw new DotEnvException($e->getMessage(), 1006);
        }
        $this->flag = true;
        return $this->getAkamaiConfig();
    }

    public function get($key)
    {
        return getenv($key);
    }

    public function set($key, $value)
    {
        putenv($key . "=" . $value);
    }

    public function setAkamaiConfig($config)
    {
        if (is_array($config)) {
            foreach ($config as $key => $value) {
                $this->set($key, $value);
            }
        } else {
            throw new InvalidDataTypeFoundException("Expected array but found ". gettype($config) . " instead", 1004);
        }

        $this->flag = true;

        return $this->getAkamaiConfig();
    }

    public function getAkamaiConfig()
    {
        if (!$this->flag) {
            $this->loadFromENV();
        }
        $result = [];

        foreach ($this->getKeys() as $value) {
            $result[$value] = ($this->get($value)) ? $this->get($value) : "";
        }

        return $result;
    }

    private function getKeys()
    {
        return [
            "AKAMAI_HOST",
            "AKAMAI_KEY",
            "AKAMAI_KEYNAME",
            "AKAMAI_VIDEO_TOKEN"
        ];
    }
}
