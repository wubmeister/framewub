<?php

use PHPUnit\Framework\TestCase;

use Framewub\App;
use Framewub\Route\Router;

class MockCode
{
	public function __invoke()
	{
		echo 'Hello MockCode';
	}
}

class FuncTest extends TestCase
{
    public function testHandleRequest()
    {
        $app = new App();

        $router = new Router();
        $router->setFallback('MockCode');

        $app->setRouter($router);

        ob_start();
        $app->handleRequest();
        $contents =ob_get_contents();
        ob_end_clean();

        $this->assertEquals('Hello MockCode', $contents);
    }
}
