<?php

namespace Akamai\Services;

use Akamai\Exceptions\VersionNotSupportedException;

class AkamaiAuth
{

    public $key;
    public $key_name;
    public $client;
    public $server;
    public $unique_id;
    public $version = 5;

    public function __construct($key, $key_name, $version = 5)
    {
        $this->key = $key;
        $this->key_name = $key_name;
        $this->client   = '0.0.0.0';
        $this->server   = '0.0.0.0';
        $this->unique_id = mt_rand(1000000000, 9999999999);
        $this->version   = $version;

    }

    public function getAuthData()
    {
        return implode(', ', array(
            $this->version,
            $this->server,
            $this->client,
            time(),
            $this->unique_id,
            $this->key_name
        ));
    }

    public function getAuthSign($uri, $action)
    {
        $lf     = "\x0a";
        #$lf     = "\n";
        $label      = 'x-akamai-acs-action:';
        $authd      = $this->getAuthData();
        $sign_string    = $authd.$uri.$lf.$label.$action.$lf;

        $algorithm  = ($this->version == 3) ? "md5" :
                  ($this->version == 4) ? "sha1" :
                  ($this->version == 5) ? "sha256" :
                  null;
        if ($algorithm === null) {
            throw new VersionNotSupportedException('it is not supported version ['.$this->version.']', 1002);
        }
        return base64_encode(hash_hmac($algorithm, $sign_string, $this->key, true));
    }
}
