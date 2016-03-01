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
        $this->path = __DIR__."/../";
    }

    public function loadFromENV($path = null, $name = null)
    {
        $this->parser = new Dotenv(($path) ? $path : $this->path, ($name) ? $name : ".env");
        $this->parser->overload();
        $this->addRules();
        $this->flag = true;
        return $this->getAkamaiConfig();
    }

    private function addRules()
    {
        $this->parser->required('AKA_FTP_HOST')->notEmpty();
        $this->parser->required('AKA_FTP_KEY')->notEmpty();
        $this->parser->required('AKA_FTP_KEYNAME')->notEmpty();
        $this->parser->required('AKA_FTP_BASIC_URL')->notEmpty();
        $this->parser->required('AKA_FTP_FOLDER')->notEmpty();
        $this->parser->required('AKA_VERSION')->isInteger();
    }

    public function get($key)
    {
        return getenv($key);
    }

    public function getAkamaiConfig()
    {
        if (!$this->flag) {
            $this->loadFromENV();
        }
        return [
            "AKA_FTP_HOST" => $this->get("AKA_FTP_HOST"),
            "AKA_FTP_KEY" => $this->get("AKA_FTP_KEY"),
            "AKA_FTP_KEYNAME" => $this->get("AKA_FTP_KEYNAME"),
            "AKA_FTP_BASIC_URL" => $this->get("AKA_FTP_BASIC_URL"),
            "AKA_FTP_FOLDER" => $this->get("AKA_FTP_FOLDER"),
            "AKA_VERSION" => $this->get("AKA_VERSION"),
        ];
    }
}
