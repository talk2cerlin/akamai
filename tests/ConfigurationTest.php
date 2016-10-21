<?php

namespace Test;

error_reporting(-1);
ini_set('display_errors', 'On');

require_once __DIR__ . "/../vendor/autoload.php";

use Akamai\Facades\Config;
use Akamai\Facades\Akamai;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function testGetConfigWithOutLoadingConfig()
    {
        // Make sure a valid .env.akami file is there in root directory
        $this->assertInternalType('array', Config::getAkamaiConfig());
    }

    public function testConfigLoader()
    {
        Config::loadFromENV('./tests/');
        $config = [
            "AKAMAI_HOST" => "testinginline",
            "AKAMAI_KEY" => "U5Z1mKb18pTPvBs5sgbOGOWlqMiJ8Rm4uKCax6abw2wCiHD8Dk",
            "AKAMAI_KEYNAME" => "testinginline",
            "AKAMAI_VIDEO_TOKEN" => "b30Hlb8VG1BGTyC3qKllXrOQgM5QHBlo"
        ];
        $this->assertEquals(Config::getAkamaiConfig(), $config);
    }

    public function testGetSuccess()
    {
        $this->assertEquals(Config::get("AKAMAI_HOST"), "testinginline");
        $this->assertEquals(Config::get("AKAMAI_KEY"), "U5Z1mKb18pTPvBs5sgbOGOWlqMiJ8Rm4uKCax6abw2wCiHD8Dk");
        $this->assertEquals(Config::get("AKAMAI_KEYNAME"), "testinginline");
        $this->assertEquals(Config::get("AKAMAI_VIDEO_TOKEN"), "b30Hlb8VG1BGTyC3qKllXrOQgM5QHBlo");
    }

    public function testGetFailure()
    {
        $this->assertEquals(Config::get("random"), false);
    }

    public function testSet()
    {
        Config::set("AKAMAI_HOST", "testDataOnRuntime");
        Config::set("AKAMAI_KEY", "LwsXEawybVJgEEAhWy6k7MvF0kr7trwLg7PRhDOc4x5xAf9BA3");
        Config::set("AKAMAI_KEYNAME", "testdataonruntime");
        Config::set("AKAMAI_VIDEO_TOKEN", "Vam2Kz0i0aSCQ1EpMZUFzO8PxSUBheo0");
        
        $this->assertNotEquals(Config::get("AKAMAI_HOST"), "testinginline");
        $this->assertNotEquals(Config::get("AKAMAI_KEY"), "U5Z1mKb18pTPvBs5sgbOGOWlqMiJ8Rm4uKCax6abw2wCiHD8Dk");
        $this->assertNotEquals(Config::get("AKAMAI_KEYNAME"), "testinginline");
        $this->assertNotEquals(Config::get("AKAMAI_VIDEO_TOKEN"), "b30Hlb8VG1BGTyC3qKllXrOQgM5QHBlo");

        $this->assertEquals(Config::get("AKAMAI_HOST"), "testDataOnRuntime");
        $this->assertEquals(Config::get("AKAMAI_KEY"), "LwsXEawybVJgEEAhWy6k7MvF0kr7trwLg7PRhDOc4x5xAf9BA3");
        $this->assertEquals(Config::get("AKAMAI_KEYNAME"), "testdataonruntime");
        $this->assertEquals(Config::get("AKAMAI_VIDEO_TOKEN"), "Vam2Kz0i0aSCQ1EpMZUFzO8PxSUBheo0");
    }

    public function testSetAkamaiConfig()
    {
        Config::setAkamaiConfig($config = [
            "AKAMAI_HOST" => "testSettingDataInBulk",
            "AKAMAI_KEY" => "zq4tXojyAZ3lSJAkWo5968CmHkzehCLPXfzfHzhFFL082MTv3U",
            "AKAMAI_KEYNAME" => "testSettingDataInBulk",
            "AKAMAI_VIDEO_TOKEN" => "kfTyWl8Ai9LjTMnYsoS1BoENsENXCWxR"
        ]);

        $this->assertEquals(Config::get("AKAMAI_HOST"), "testSettingDataInBulk");
        $this->assertEquals(Config::get("AKAMAI_KEY"), "zq4tXojyAZ3lSJAkWo5968CmHkzehCLPXfzfHzhFFL082MTv3U");
        $this->assertEquals(Config::get("AKAMAI_KEYNAME"), "testSettingDataInBulk");
        $this->assertEquals(Config::get("AKAMAI_VIDEO_TOKEN"), "kfTyWl8Ai9LjTMnYsoS1BoENsENXCWxR");

    }
    
    public function testSetAkamaiConfigFailureTwo()
    {

        $this->expectException('Akamai\Exceptions\InvalidDataTypeFoundException');

        Config::setAkamaiConfig("passing string instead of array");
    }

    public function testDotEnvException()
    {
        $this->expectException('\Akamai\Exceptions\DotEnvException');
        Config::loadFromENV('./tests/', 'invalid.configuration.file');
    }
}
