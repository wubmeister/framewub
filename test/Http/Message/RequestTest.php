<?php

use PHPUnit\Framework\TestCase;

use Framewub\Http\Message\Request;
use Framewub\Http\Message\Stream\AbstractStream;
use Framewub\Http\Message\Uri;
use Psr\Http\Message\StreamInterface;

class RequestTest extends TestCase
{
	// public function setUp()
	// {
	// 	$_POST['foo'] = 'bar';
	// 	$_POST['lorem'] = 'ipsum';
	// 	$_GET['dingen'] = 'zaken';
	// }

	public function testConstruct() {
		$request = new Request();
	}

	public function testGetProtocolVersion() {
		$request = new Request();
		$this->assertEquals('1.1', $request->getProtocolVersion());

		$request2 = $request->withProtocolVersion('1.0');
		$this->assertEquals('1.0', $request2->getProtocolVersion());
	}

	public function testHeaders() {
		$request = new Request();

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
		$request = new Request();

		$body = $request->getBody();
		$this->assertInstanceOf(StreamInterface::class, $body);

		$stream = new AbstractStream();
		$request2 = $request->withBody($stream);
		$this->assertEquals($stream, $request->getBody());
	}

	public function testTarget()
	{
		$request = new Request();

		$this->assertEquals('/', $request->getRequestTarget());

		$request2 = $request->withRequestTarget('/foo/bar');
		$this->assertEquals('/foo/bar', $request2->getRequestTarget());
	}

	public function testMethod()
	{
		$request = new Request();

		$this->assertEquals('GET', $request->getMethod());

		$request2 = $request->withMethod('POST');
		$this->assertEquals('POST', $request2->getMethod());
	}

	public function testUri()
	{
		// $uri = new Uri('/foo/bar');

		$request = new Request();
		$uri = $request->getUri();
		$this->assertInstanceOf(Uri::class, $uri);
		$this->assertEquals('/', (string)$uri);

		$request2 = $request->withUri(new Uri('/foo/bar'));
		$this->assertEquals('/foo/bar', (string)$request2->getUri());
	}
}