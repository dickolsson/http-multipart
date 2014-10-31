<?php

namespace GuzzleHttp\Tests\Message;

use GuzzleHttp\Message\MultipartMessageFactory;

class MultipartMessageFactoryTest extends \PHPUnit_Framework_TestCase {

    public function testCreateResponse()
    {
        $factory = new MultipartMessageFactory();
        $response = $factory->createResponse(200, [], null);
        $this->assertInstanceOf('GuzzleHttp\Message\MultipartResponse', $response);

        $factory = new MultipartMessageFactory();
        $response = $factory->createResponse(200, [], 'foo');
        $this->assertInstanceOf('GuzzleHttp\Message\MultipartResponse', $response);
    }
}
