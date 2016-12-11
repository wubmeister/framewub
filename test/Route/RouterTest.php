<?php

use PHPUnit\Framework\TestCase;

use Framewub\Route\AbstractRoute;
use Framewub\Route\Router;
use Framewub\Route\Literal;

class RouterTest extends TestCase
{
    public function testMatch()
    {
        $router = new Router();
        $this->assertInstanceOf(AbstractRoute::class, $router);

        $router->addChildRoute('foobar', new Literal('/foo/bar', 'FooBar'));
        $router->addChildRoute('loremipsum', new Literal('/lorem/ipsum', 'LoremIpsum'));

        // Should not match
        $result = $router->match('/lorem/ipsum');
        $this->assertInternalType('array', $result);
        $this->assertEquals('LoremIpsum', $result['code']);

        // Should not match
        $result = $router->match('/foo/bar');
        $this->assertInternalType('array', $result);
        $this->assertEquals('FooBar', $result['code']);
    }
}
