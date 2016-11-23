<?php

namespace Akamai\Interfaces;

interface AkamaiInterface
{

    public function upload($url, $body);

    public function dir($url);

    public function download($url);

    public function delete($url);
    
    public function rmdir($url);

    public function mkdir($url);

    public function rename($url, $destination);

    public function generateToken($duration, $type);
}
