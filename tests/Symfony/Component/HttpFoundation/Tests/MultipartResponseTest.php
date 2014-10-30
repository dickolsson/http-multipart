<?php

namespace Symfony\Component\HttpFoundation\Tests;

use Symfony\Component\HttpFoundation\MultipartResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MultipartResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $response = new MultipartResponse();
        $this->assertEquals('mixed', $response->subtype);
        $this->assertNotEmpty($response->boundary);

        $response = new MultipartResponse('related');
        $this->assertEquals('related', $response->subtype);
    }

    public function testPrepare()
    {
        $request = new Request();
        $response = new MultipartResponse();
        $response->prepare($request);

        $this->assertEquals("multipart/mixed; boundary=\"{$response->boundary}\"", $response->headers->get('content-type'));
        $this->assertEquals('chunked', $response->headers->get('transfer-encoding'));
    }

    public function testPrepareWithPart()
    {
        $request = new Request();

        $part = new Response('hello', 200, array('content-type' => 'text/plain', 'language' => 'en'));

        $response = new MultipartResponse();
        $response->setPart($part);
        $response->prepare($request);

        $this->assertEquals("multipart/mixed; boundary=\"{$response->boundary}\"", $response->headers->get('content-type'));
        $this->assertNull($response->headers->get('language'));
    }

    public function testSendContent()
    {
        $request = new Request();

        /** @var Response[] $parts */
        $parts = array();
        $parts[] = new Response('hello', 200, array('content-type' => 'text/plain', 'language' => 'en'));
        $parts[] = new Response('hejsan', 200, array('content-type' => 'text/plain', 'language' => 'se'));

        $response = new MultipartResponse('related');
        $response->setParts($parts);
        $response->prepare($request);

        ob_start();
        $response->sendContent();
        $actual = ob_get_clean();

        $boundary = $response->boundary;

        $dates = array();
        $dates[] = $parts[0]->headers->get('date');
        $dates[] = $parts[0]->headers->get('date');

        $expected = "--$boundary\r\nCache-Control: no-cache\r\nContent-Type:  text/plain\r\nDate:          {$dates[0]}\r\nLanguage:      en\r\n\r\nhello\r\n--$boundary\r\nCache-Control: no-cache\r\nContent-Type:  text/plain\r\nDate:          {$dates[1]}\r\nLanguage:      se\r\n\r\nhejsan\r\n--$boundary--";

        $this->assertEquals($expected, $actual);
    }

    public function testSetContent()
    {
        $response = new MultipartResponse();
        try {
            $response->setContent('foo');
            $this->fail('LogicException was not thrown.');
        } catch(\Exception $e) {
            $this->assertInstanceOf('LogicException', $e);
        }
    }

    public function testGetContent()
    {
        $response = new MultipartResponse();
        $this->assertFalse($response->getContent());
    }
}
