<?php

use PHPUnit\Framework\TestCase;

use Framewub\Http\Message\Response;
use Framewub\Http\Message\Stream\PHPOutput;

class ResponseTest extends TestCase
{
    public function testContentConstructor()
    {
        ob_start();
        $response = new Response('Hello world');
        ob_end_clean();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello world', $response->getBody()->getMockContents());

        ob_start();
        $response = new Response('Hello world', 302);
        ob_end_clean();

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('Hello world', $response->getBody()->getMockContents());
    }

    public function testStatus()
    {
        $response = new Response();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testWithStatus()
    {
        $response = new Response();

        $response2 = $response->withStatus(404);
        $this->assertEquals(404, $response2->getStatusCode());
        $this->assertEquals("Not Found", $response2->getReasonPhrase());

        $response3 = $response->withStatus(403, "You are not allowed here");
        $this->assertEquals(403, $response3->getStatusCode());
        $this->assertEquals("You are not allowed here", $response3->getReasonPhrase());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWithInvalidStatus()
    {
        $response = new Response();
        $response2 = $response->withStatus(700);
    }

    public function testReasonPhrase()
    {
        $response = new Response();
        $this->assertEquals('OK', $response->getReasonPhrase());
    }

    public function testStream()
    {
        $response = new Response();
        $this->assertInstanceOf(PHPOutput::class, $response->getBody());
    }
}
