<?php

namespace GuzzleHttp\Tests\Message;

use GuzzleHttp\Message\MultipartResponse;
use GuzzleHttp\Stream\Stream;

class MultipartMessageFactoryTest extends \PHPUnit_Framework_TestCase {

    const bodySimple = "--delimiter\r\nLanguage: en\r\n\r\nhello\r\n--delimiter\r\nLanguage: se\r\n\r\nhejsan\r\n--delimiter--";
    const bodySpaced = "--delimiter\r\nLanguage:    en\r\n\r\nhello\r\n--delimiter\r\nLanguage: se\r\n\r\nhejsan\r\n--delimiter--";
    const bodyTabbed = "--delimiter\r\nLanguage:	en\r\n\r\nhello\r\n--delimiter\r\nLanguage:	se\r\n\r\nhejsan\r\n--delimiter--";

    public function testSimple()
    {
        $this->assertParseMultipartBody(self::bodySimple);
    }

    public function testSpaced()
    {
        $this->assertParseMultipartBody(self::bodySpaced);
    }

    public function testTabbed()
    {
        $this->assertParseMultipartBody(self::bodyTabbed);
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
}
