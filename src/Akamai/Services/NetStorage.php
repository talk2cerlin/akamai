<?php

namespace Akamai\Services;

use Akamai\Services\AkamaiAuth;

/*
    Original Source of this class can be found in https://github.com/raben/Akamai
*/

class NetStorage
{

    protected $host;
    protected $auth;
    protected $version = 1;

    private $_http;
    private $_last_status_code = null;

    protected function __construct()
    {
        $this->config = config('app.akamai');
        $this->key = $this->config['key'];
        $this->key_name = $this->config['key_name'];
        $this->host = $this->config['host'];
        $this->auth = new AkamaiAuth($this->key, $this->key_name, $this->version);
        // $this->version = $this->config->version;
    }

    protected function getLastStatusCode()
    {
        return $this->_last_status_code;
    }

    /*
        Implemented Method from Interface
    */

    protected function upload($url, $body)
    {
        $response_data = $this->_updateAction('upload', $url, array('body' => $body));
        return ['data' => $response_data,'code' => $this->_last_status_code];
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
        $this->_updateAction('mtime', $url, array('mtime' => $time));
    }

    protected function rename($url, $destination)
    {
        $this->_updateAction('rename', $url, array('destination' => $destination));
    }

    protected function symlink($url, $target)
    {
        $this->_updateAction('symlink', $url, array('target' => $target));
    }

    protected function mkdir($url)
    {
        $this->_updateAction('mkdir', $url);
    }

    protected function rmdir($url)
    {
        $this->_updateAction('rmdir', $url);
    }

    protected function delete($url)
    {
        $this->_updateAction('delete', $url);
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
        $this->_updateAction('quick-delete', $url, array('qd_confirm' => $qd_confirm));
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
        $this->_last_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($tmpfile) { // Fix added from https://github.com/raben/Akamai/pull/4/files
            fclose($tmpfile); // Fix added from https://github.com/raben/Akamai/pull/4/files
        }
        return $data;
    }
}
