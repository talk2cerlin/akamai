<?php

namespace Akamai;

use Akamai\Interfaces\AkamaiInterface;
use Akamai\Exceptions\FileNotFoundException;
use Akamai\Services\NetStorage;
use Akamai\Services\TokenGenerator;

class Akamai extends NetStorage implements AkamaiInterface
{
    public $client;

    public function __construct()
    {
        $this->client = new NetStorage();
    }

    public function generateToken($duration, $type = "hdnea")
    {
        if (!in_array($type, ['hdnts', 'hdnea'])) {
            $type = "hdnts";
        }
        $this->tokenGenerator = new TokenGenerator($duration);
        return strtolower($type) . "=" . $this->tokenGenerator->getToken();
    }

    public function dir($url)
    {
        return $this->client->dir($url);
    }

    public function delete($url)
    {
        return $this->client->delete($url);
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
        return $this->client->upload($filename, $this->readFileData($raw_file_loc));
    }

    public function mkdir($url)
    {
        return $this->client->mkdir($url);
    }

    public function rename($url, $destination)
    {
        return $this->client->rename($url, $destination);
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
