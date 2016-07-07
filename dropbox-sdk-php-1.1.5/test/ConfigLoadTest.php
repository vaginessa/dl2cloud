<?php

require_once __DIR__.'/../lib/Dropbox/strict.php';

use \Dropbox as dbx;

class ConfigLoadTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
        @unlink('test.json');
    }

    public function testMissingAppJson()
    {
        $this->setExpectedException('\Dropbox\AppInfoLoadException');
        dbx\AppInfo::loadFromJsonFile('missing.json');
    }

    public function testBadAppJson()
    {
        $this->setExpectedException('\Dropbox\AppInfoLoadException');
        file_put_contents('test.json', 'Not JSON.  At all.');
        dbx\AppInfo::loadFromJsonFile('test.json');
    }

    public function testNonHashAppJson()
    {
        $this->setExpectedException('\Dropbox\AppInfoLoadException');
        file_put_contents('test.json', json_encode(123, true));
        dbx\AppInfo::loadFromJsonFile('test.json');
    }

    public function testBadAppJsonFields()
    {
        $correct = [
            'key'    => 'an_app_key',
            'secret' => 'an_app_secret',
        ];

        // check that we detect every missing field
        foreach ($correct as $key => $value) {
            $tmp = $correct;
            unset($tmp[$key]);

            file_put_contents('test.json', json_encode($tmp, true));

            try {
                dbx\AppInfo::loadFromJsonFile('test.json');
                $this->fail('Expected exception');
            } catch (dbx\AppInfoLoadException $e) {
                // Expecting this exception.
            }
        }

        // check that we detect non-string fields
        foreach ($correct as $key => $value) {
            $tmp = $correct;
            $tmp[$key] = 123;

            file_put_contents('test.json', json_encode($tmp, true));

            try {
                dbx\AppInfo::loadFromJsonFile('test.json');
                $this->fail('Expected exception');
            } catch (dbx\AppInfoLoadException $e) {
                // Expecting this exception.
            }
        }
    }

    public function testAppJsonServer()
    {
        $correct = [
            'key'         => 'an_app_key',
            'secret'      => 'an_app_secret',
            'access_type' => 'AppFolder',
            'host'        => 'test.droppishbox.com',
        ];

        $str = json_encode($correct, true);
        self::tryAppJsonServer($str);
        self::tryAppJsonServer("\xEF\xBB\xBF".$str);  // UTF-8 byte order mark
    }

    public function tryAppJsonServer($str)
    {
        file_put_contents('test.json', $str);
        $appInfo = dbx\AppInfo::loadFromJsonFile('test.json');
        $this->assertEquals($appInfo->getHost()->getContent(), 'api-content-test.droppishbox.com');
        $this->assertEquals($appInfo->getHost()->getApi(), 'api-test.droppishbox.com');
        $this->assertEquals($appInfo->getHost()->getWeb(), 'meta-test.droppishbox.com');
    }

    public function testMissingAuthJson()
    {
        $this->setExpectedException('\Dropbox\AuthInfoLoadException');
        dbx\AuthInfo::loadFromJsonFile('missing.json');
    }

    public function testBadAuthJson()
    {
        $this->setExpectedException('\Dropbox\AuthInfoLoadException');
        file_put_contents('test.json', 'Not JSON.  At all.');
        dbx\AuthInfo::loadFromJsonFile('test.json');
    }

    public function testNonHashAuthJson()
    {
        $this->setExpectedException('\Dropbox\AuthInfoLoadException');
        file_put_contents('test.json', json_encode(123, true));
        dbx\AuthInfo::loadFromJsonFile('test.json');
    }

    public function testBadAuthJsonFields()
    {
        $minimal = [
            'access_token' => 'an_access_token',
        ];

        // check that we detect every missing field
        foreach ($minimal as $key => $value) {
            $tmp = $minimal;
            unset($tmp[$key]);

            file_put_contents('test.json', json_encode($tmp, true));

            try {
                dbx\AuthInfo::loadFromJsonFile('test.json');
                $this->fail('Expected exception');
            } catch (dbx\AuthInfoLoadException $e) {
                // Expecting this exception.
            }
        }

        $correct = [
            'access_token' => 'an_access_token',
            'host'         => 'test-server.com',
        ];

        // check that we detect non-string fields
        foreach ($correct as $key => $value) {
            $tmp = $correct;
            $tmp[$key] = 123;

            file_put_contents('test.json', json_encode($tmp, true));

            try {
                dbx\AuthInfo::loadFromJsonFile('test.json');
                $this->fail('Expected exception');
            } catch (dbx\AuthInfoLoadException $e) {
                // Expecting this exception.
            }
        }
    }
}
