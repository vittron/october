<?php

namespace Kibo\Phast\HTTP;

use Kibo\Phast\ValueObjects\URL;

class CURLClientTest extends \PHPUnit_Framework_TestCase {
    public function setUp() {
        parent::setUp();
        if (!function_exists('curl_init')) {
            $this->markTestSkipped('cURL is missing');
        }
    }

    public function testGet() {
        $client = new CURLClient();
        $result = $client->get(URL::fromString('http://whatismyip.akamai.com/'));
        $this->assertEquals(200, $result->getCode());
        $this->assertRegExp('/^\d+\.\d+\.\d+\.\d+/', $result->getContent());
        $this->assertArraySubset(['Content-Type' => 'text/html'], $result->getHeaders());
    }

    public function testPost() {
        $client = new CURLClient();
        $data = str_repeat('x', 5e6);
        $result = $client->post(URL::fromString('http://optimize.phast.io/?service=images'), $data);
        $this->assertNotContains('HTTP/', $result->getContent());
        $this->assertNotEmpty($result->getHeaders());
    }
}
