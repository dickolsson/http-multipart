<?php

namespace GuzzleHttp\Tests\Message;

use GuzzleHttp\Client;
use GuzzleHttp\Message\MultipartMessageFactory;
use GuzzleHttp\Tests\Ring\Client\Server;

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

    public function testClient()
    {
        $this->enqueueResponse();
        $client = new Client(['message_factory' => new MultipartMessageFactory()]);
        $response = $client->get(Server::$url);

        $this->assertEquals('hello', (string) $response->getBody());
        $this->assertEquals('hejsan', (string) $response->getBody());
    }

    protected function enqueueResponse()
    {
        Server::flush();
        $response = [
            'status' => 200,
            'headers' => ['Content-Type' => 'multipart/related; boundary="delimiter"'],
            'body' => "--delimiter\r\nLanguage: en\r\n\r\nhello\r\n--delimiter\r\nLanguage: se\r\n\r\nhejsan\r\n--delimiter--",
        ];
        Server::enqueue([$response]);
    }
}
