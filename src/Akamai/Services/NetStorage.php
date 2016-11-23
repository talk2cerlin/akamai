<?php

namespace Akamai\Services;

use Akamai\Services\AkamaiAuth;
use Akamai\Facades\Config;
use Akamai\Facades\ArrayHelper;
use Akamai\Exceptions\ValidationException;
use Akamai\Exceptions\InvalidDataTypeFoundException;
use Valitron\Validator as Valitron;

/*
    Original Source of this class can be found in https://github.com/raben/Akamai
*/

class NetStorage
{

    protected $host;
    protected $auth;
    protected $version = 1;

    private $_http;

    protected function __construct()
    {
        $this->config = $this->validateConfig(Config::getAkamaiConfig());
        $this->key = $this->config['AKAMAI_KEY'];
        $this->key_name = $this->config['AKAMAI_KEYNAME'];
        $this->host = $this->config['AKAMAI_HOST'];
        $this->auth = new AkamaiAuth($this->key, $this->key_name);
        // $this->version = $this->config->version;
    }

    private function validateConfig($config)
    {
        if (!is_array($config)) {
            // Throw exception
            throw new InvalidDataTypeFoundException("Expected array but found ". gettype($config) . " instead", 1004);
        }
        $validator = new Valitron($config);
        $validator->rule('required', ['AKAMAI_KEY', 'AKAMAI_KEYNAME', 'AKAMAI_HOST']);
        $validator->rule('length', 'AKAMAI_KEY', 50);

        if (!$validator->validate()) {
            // Concatinate all message and Throw exception

            $message = implode(', ', ArrayHelper::flatten($validator->errors()));

            throw new ValidationException($message, 1007);
        }

        return $config;
    }

    protected function upload($url, $body)
    {
        return $this->_updateAction('upload', $url, array('body' => $body));
    }

    protected function download($url)
    {
        return $this->_readOnlyAction('download', $url);
    }

    protected function du($url)
    {
        return $this->_readOnlyAction('du', $url);
    }

    protected function dir($url)
    {
        return $this->_readOnlyAction('dir', $url);
    }

    protected function stat($url)
    {
        return $this->_readOnlyAction('stat', $url);
    }

    private function _readOnlyAction($action, $url)
    {
        if (!$this->auth) {
            throw new Exception('it is not authorized yet.');
        }

        $action_string = 'version='.$this->version;
        $action_string .= '&action='.$action;

        if ($action != 'download') {
            $action_string .= "&format=xml";
        }

        $auth_data  = $this->auth->getAuthData();
        $auth_sign  = $this->auth->getAuthSign($url, $action_string);

        $headers    = array(
            "Accept:",
            "Accept-Encoding: identity",
            "X-Akamai-ACS-Auth-Data: {$auth_data}",
            "X-Akamai-ACS-Auth-Sign: {$auth_sign}",
            "X-Akamai-ACS-Action: {$action_string}"
        );

        return $this->request('GET', $url, null, $headers);
    }

    protected function mtime($url, $time)
    {
        return $this->_updateAction('mtime', $url, array('mtime' => $time));
    }

    protected function rename($url, $destination)
    {
        return $this->_updateAction('rename', $url, array('destination' => $destination));
    }

    protected function symlink($url, $target)
    {
        return $this->_updateAction('symlink', $url, array('target' => $target));
    }

    protected function mkdir($url)
    {
        return $this->_updateAction('mkdir', $url);
    }

    protected function rmdir($url)
    {
        return $this->_updateAction('rmdir', $url);
    }

    protected function delete($url)
    {
        return $this->_updateAction('delete', $url);
    }

    /**
     * quick_delete
     *
     * Used to perform a “quick-delete” of a selected directory (including all of its contents).
     * NOTE: The “quick-delete” action is disabled by default for security reasons, as it allows recursive
     *       removal of non-empty directory structures in a matter of seconds. If you wish to enable this feature,
     *       please contact your Akamai Representative with the NetStorage CPCode(s) for which you wish to
     *       use this feature.
     */
    protected function quickDelete($url, $qd_confirm)
    {
        return $this->_updateAction('quick-delete', $url, array('qd_confirm' => $qd_confirm));
    }

    private function _updateAction($action, $url, $options = array())
    {
        if (!$this->auth) {
            throw new Exception('it is not authorized yet.');
        }

        $action_string = 'version='.$this->version;
        $action_string .= '&action='.$action;
        if ($action != 'download') {
            $action_string .= "&format=xml";
        }

        foreach ($options as $key => $value) {
            if (in_array($key, array('index_zip', 'mtime', 'size', 'md5', 'sha1', 'md5', 'destination', 'target', 'qd_confirm'))) {
                if ($key == 'target' || $key == 'destination') {
                    $value = urlencode($value);
                }
                if ($key == 'qd_confirm') {
                    $key = 'quick-delete';
                }
                $action_string .= "&{$key}={$value}";
            }
        }

        $auth_data  = $this->auth->getAuthData();
        $auth_sign  = $this->auth->getAuthSign($url, $action_string);

        $headers    = array(
            "Accept:",
            "Accept-Encoding: identity",
            "X-Akamai-ACS-Auth-Data: {$auth_data}",
            "X-Akamai-ACS-Auth-Sign: {$auth_sign}",
            "X-Akamai-ACS-Action: {$action_string}"
        );

        $body = (isset($options["body"])) ? $options["body"] : "";
        $method = 'PUT';
        return $this->request($method, $url, $body, $headers);

    }

    private function request($method, $url, $body, $headers)
    {
        $curl = curl_init('https://'.$this->host.$url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        $tmpfile = ""; // Fix added from https://github.com/raben/Akamai/pull/4/files
        if ($method == 'PUT') {
            $length = strlen($body);
            if ($length != 0) {
                $tmpfile = tmpfile();
                fwrite($tmpfile, $body);
                fflush($tmpfile); // Fix added from https://github.com/raben/Akamai/pull/4/files
                fseek($tmpfile, 0);
                curl_setopt($curl, CURLOPT_INFILE, $tmpfile);
            }
            curl_setopt($curl, CURLOPT_UPLOAD, 1);
            curl_setopt($curl, CURLOPT_INFILESIZE, strlen($body));


        }

        $data = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($tmpfile) { // Fix added from https://github.com/raben/Akamai/pull/4/files
            fclose($tmpfile); // Fix added from https://github.com/raben/Akamai/pull/4/files
        }
        return ["data" => $data, "code" => $code];
    }
}
