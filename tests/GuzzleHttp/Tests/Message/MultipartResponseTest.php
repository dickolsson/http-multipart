<?php

namespace GuzzleHttp\Tests\Message;

use GuzzleHttp\Message\MultipartResponse;
use GuzzleHttp\Stream\Stream;

class MultipartResponseTest extends \PHPUnit_Framework_TestCase {

    const BODY_SIMPLE = "--delimiter\r\nLanguage: en\r\n\r\nhello\r\n--delimiter\r\nLanguage: se\r\n\r\nhejsan\r\n--delimiter--";
    const BODY_SPACED = "--delimiter\r\nLanguage:    en\r\n\r\nhello\r\n--delimiter\r\nLanguage: se\r\n\r\nhejsan\r\n--delimiter--";
    const BODY_TABBED = "--delimiter\r\nLanguage:	en\r\n\r\nhello\r\n--delimiter\r\nLanguage:	se\r\n\r\nhejsan\r\n--delimiter--";

    public function testParsingSimple()
    {
        $this->assertParseMultipartBody(self::BODY_SIMPLE);
    }

    public function testParsingSpaced()
    {
        $this->assertParseMultipartBody(self::BODY_SPACED);
    }

    public function testParsingTabbed()
    {
        $this->assertParseMultipartBody(self::BODY_TABBED);
    }

    public function testParsingEmpty()
    {
        $stream = Stream::factory('');
        $parts = MultipartResponse::parseMultipartBody($stream);

        $this->assertTrue(is_array($parts));
        $this->assertEmpty($parts);
    }

    public function testEmptyResponse()
    {
        $response = $this->newMultipartResponse('');

        $this->assertEquals('', (string) $response->getBody());
    }

    public function testConsecutiveCalls()
    {
        $response = $this->newMultipartResponse(self::BODY_SIMPLE);

        $this->assertEquals('hello', (string) $response->getBody());
        $this->assertEquals('hejsan', (string) $response->getBody());
    }

    public function testWhileLoop()
    {
        $response = $this->newMultipartResponse(self::BODY_SIMPLE);

        $count = 0;
        while ($body = (string) $response->getBody()) {
            $this->assertNotEmpty($body);
            $count++;
        }
        $this->assertEquals(2, $count);
    }

    protected function assertParseMultipartBody($body)
    {
        $stream = Stream::factory($body);
        $parts = MultipartResponse::parseMultipartBody($stream);

        $this->assertCount(2, $parts);
        $this->assertEquals('en', $parts[0]['headers']['language']);
        $this->assertEquals('hello', $parts[0]['body']);
        $this->assertEquals('se', $parts[1]['headers']['language']);
        $this->assertEquals('hejsan', $parts[1]['body']);
    }

    protected function newMultipartResponse($body)
    {
        $stream = Stream::factory($body);
        return new MultipartResponse(200, [], $stream);
    }
}
