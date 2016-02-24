<?php

namespace Akamai;

use Akamai\Interfaces\AkamaiInterface;
use Akamai\Exceptions\FileNotFoundException;
use Akamai\Services\NetStorage;

class Akamai extends NetStorage implements AkamaiInterface
{
    public $client;

    public function __construct()
    {
        $this->client = new NetStorage();
    }

    public function dir($url)
    {
        return $this->client->dir($url);
    }

    public function delete($url)
    {
        $this->client->delete($url);
    }

    public function download($url)
    {
        return $this->client->download($url);
    }

    public function rmdir($url)
    {
        return $this->client->rmdir($url);
    }

    public function upload($filename, $raw_file_loc)
    {
        $response = $this->client->upload($filename, $this->readFileData($raw_file_loc));
        return $response;
    }

    private function readFileData($filename)
    {
        if (file_exists($filename)) {
            $handle = fopen($filename, "r");
            $contents = fread($handle, filesize($filename));
            fclose($handle);
            return $contents;
        } else {
            throw new FileNotFoundException('File Not Found in location "'.$filename.'"', 1001);
        }
    }
}
