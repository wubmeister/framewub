<?php

use PHPUnit\Framework\TestCase;

// use Framewub\Http\Message\Uri;

class ServerRequestTest extends TestCase
{
	public function setUp()
	{
		$_POST['foo'] = 'bar';
		$_POST['lorem'] = 'ipsum';
		$_GET['dingen'] = 'zaken';
	}

	public function testConstruct() {
		var_dump($_POST);
	}
}