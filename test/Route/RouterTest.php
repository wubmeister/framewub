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

        // Should match
        $result = $router->match('/lorem/ipsum');
        $this->assertInternalType('array', $result);
        $this->assertEquals('LoremIpsum', $result['code']);

        // Should match
        $result = $router->match('/foo/bar');
        $this->assertInternalType('array', $result);
        $this->assertEquals('FooBar', $result['code']);
    }

    public function testBuild()
    {
        $router = new Router();
        $this->assertInstanceOf(AbstractRoute::class, $router);
        $lipsum = new Literal('/lorem/ipsum', 'LoremIpsum');
        $lipsum->addChildRoute('dingen', new Literal('/dingen', 'Dingen'));

        $router->addChildRoute('foobar', new Literal('/foo/bar', 'FooBar'));
        $router->addChildRoute('loremipsum', $lipsum);

        $result = $router->build('foobar');
        $this->assertEquals('/foo/bar', $result);

        $result = $router->build('loremipsum');
        $this->assertEquals('/lorem/ipsum', $result);

        $result = $router->build('loremipsum', 'dingen');
        $this->assertEquals('/lorem/ipsum/dingen', $result);
    }

    public function testFallback()
    {
        $router = new Router();
        $this->assertInstanceOf(AbstractRoute::class, $router);

        $router->addChildRoute('foobar', new Literal('/foo/bar', 'FooBar'));
        $router->addChildRoute('loremipsum', new Literal('/lorem/ipsum', 'LoremIpsum'));
        $router->setFallback('MyFallback');

        // Should go to fallback
        $result = $router->match('/dingen/zaken');
        $this->assertInternalType('array', $result);
        $this->assertEquals('MyFallback', $result['code']);
    }
}
