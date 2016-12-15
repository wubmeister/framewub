<?php

use PHPUnit\Framework\TestCase;

use Framewub\Http\Message\Request;

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
		$this->assertEquals('application/json', $headers['Content-Type']);
		// $this->assertEquals('application/json', $request->getHeader('Content-Type'));
		// $this->assertEquals('application/json', $request->getHeader('content-type'));
	}
}