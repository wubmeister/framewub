<?php

use PHPUnit\Framework\TestCase;

use Framewub\Http\Message\Message;
use Framewub\Http\Message\Stream\AbstractStream;
use Psr\Http\Message\StreamInterface;

class MessageTest extends TestCase
{
    public function testConstruct() {
        $request = new Message();
    }

    public function testGetProtocolVersion() {
        $request = new Message();
        $this->assertEquals('1.1', $request->getProtocolVersion());

        $request2 = $request->withProtocolVersion('1.0');
        $this->assertEquals('1.0', $request2->getProtocolVersion());
    }

    public function testHeaders() {
        $request = new Message();

        $headers = $request->getHeaders();
        $this->assertInternalType('array', $headers);

        $request2 = $request->withHeader('Content-Type', 'application/json');
        $headers = $request2->getHeaders();
        $this->assertInternalType('array', $headers);
        $this->assertInternalType('array', $headers['Content-Type']);
        $this->assertTrue($request2->hasHeader('Content-Type'));
        $this->assertTrue($request2->hasHeader('CONTENT-TYPE'));
        $this->assertEquals('application/json', $headers['Content-Type'][0]);
        $this->assertEquals('application/json', $request2->getHeader('Content-Type')[0]);
        $this->assertEquals('application/json', $request2->getHeader('content-type')[0]);
        $this->assertEquals('application/json', $request2->getHeaderLine('content-type'));

        $request3 = $request2->withAddedHeader('Content-Type', 'text/plain');
        $headers = $request3->getHeaders();
        $this->assertEquals('application/json', $headers['Content-Type'][0]);
        $this->assertEquals('text/plain', $headers['Content-Type'][1]);

        $request4 = $request3->withoutHeader('Content-Type');
        $header = $request4->getHeader('content-type');
        $this->assertEquals(0, count($header));
    }

    public function testBody()
    {
        $request = new Message();

        $body = $request->getBody();
        $this->assertInstanceOf(StreamInterface::class, $body);

        $stream = new AbstractStream();
        $request2 = $request->withBody($stream);
        $this->assertEquals($stream, $request->getBody());
    }
}