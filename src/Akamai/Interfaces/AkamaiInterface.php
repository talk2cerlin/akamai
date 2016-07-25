<?php

namespace Akamai\Interfaces;

interface AkamaiInterface
{

    public function upload($url, $body);

    public function dir($url);

    public function download($url);

    public function delete($url);
    
    public function rmdir($url);
}