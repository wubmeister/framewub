<?php

use PHPUnit\Framework\TestCase;

use Framewub\App;
use Framewub\Route\Literal;
use Framewub\Route\Router;
use Framewub\Http\Message\ServerRequest;
use Framewub\Http\Message\Response;

class MockCode
{
    public function __invoke(ServerRequest $request)
    {
        $response = new Response();
        $response->getBody()->write('Hello MockCode');
        return $response;
    }
}

class MockCodePlain
{
    public function __invoke(ServerRequest $request)
    {
        return 'Hello MockCodePlain';
    }
}

class AppTest extends TestCase
{
    public function setUp()
    {
        $_SERVER['REQUEST_URI'] = '/';
    }

    public function testHandleRequest()
    {
        $app = new App();
        $request = new ServerRequest();

        $router = new Router();
        $router->addChildRoute('mockplain', new Literal('/plain', 'MockCodePlain'));
        $router->setFallback('MockCode');

        $app->setRouter($router);

        ob_start();
        $response = $app->handleRequest($request);
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);

        $this->assertEquals('Hello MockCode', $response->getBody()->getMockContents());

        ob_start();
        $response = $app->handleRequest($request->withRequestTarget('/plain'));
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);

        $this->assertEquals('Hello MockCodePlain', $response->getBody()->getMockContents());
    }
}
