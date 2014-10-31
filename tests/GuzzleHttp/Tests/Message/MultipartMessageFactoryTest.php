<?php

namespace GuzzleHttp\Tests\Message;

use GuzzleHttp\Client;
use GuzzleHttp\Message\MultipartMessageFactory;
use GuzzleHttp\Tests\Ring\Client\Server;

class MultipartMessageFactoryTest extends \PHPUnit_Framework_TestCase {

    public function testConsecutiveCalls()
    {
        $this->enqueueResponse();
        $client = new Client(['message_factory' => new MultipartMessageFactory()]);
        $response = $client->get(Server::$url);

        $this->assertEquals('hello', (string) $response->getBody());
        $this->assertEquals('hejsan', (string) $response->getBody());
    }

    public function testWhileLoop()
    {
        $this->enqueueResponse();
        $client = new Client(['message_factory' => new MultipartMessageFactory()]);
        $response = $client->get(Server::$url);

        $count = 0;
        while ($body = (string) $response->getBody()) {
            $this->assertNotEmpty($body);
            $count++;
        }
        $this->assertEquals(2, $count);
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
