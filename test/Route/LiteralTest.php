<?php

use PHPUnit\Framework\TestCase;

use Framewub\Route\AbstractRoute;
use Framewub\Route\Literal;

class LiteralTest extends TestCase
{
    public function testMatch()
    {
        $route = new Literal('/foo/bar', 'FooBar');
        $this->assertInstanceOf(AbstractRoute::class, $route);

        // Should not match
        $result = $route->match('/lorem/ipsum');
        $this->assertEquals(null, $result);

        // Should match
        $result = $route->match('/foo/bar');
        $this->assertInternalType('array', $result);
        $this->assertEquals('FooBar', $result['middleware']);

        // Should match
        $result = $route->match('/foo/bar/lorem/ipsum');
        $this->assertInternalType('array', $result);
        $this->assertEquals('FooBar', $result['middleware']);
    }

    public function testBuild()
    {
        $route = new Literal('/foo/bar', 'FooBar');
        $route->addChildRoute('lorem', new Literal('/lorem/ipsum', 'LoremIpsum'));

        // Should not match
        $result = $route->build();
        $this->assertEquals('/foo/bar', $result);

        // Should not match
        $result = $route->build('lorem');
        $this->assertEquals('/foo/bar/lorem/ipsum', $result);
    }

    public function testChildRoutes()
    {
        $route = new Literal('/foo', 'Foo');
        $route->addChildRoute('bar', new Literal('/bar', 'Bar'));
        $route->addChildRoute('lorem', new Literal('/lorem', 'Lorem'));

        // Should match
        $result = $route->match('/foo/ipsum');
        $this->assertInternalType('array', $result);
        $this->assertEquals('Foo', $result['middleware']);

        // Should match
        $result = $route->match('/foo/bar');
        $this->assertInternalType('array', $result);
        $this->assertEquals('Bar', $result['middleware']);

        // Should match
        $result = $route->match('/foo/lorem');
        $this->assertInternalType('array', $result);
        $this->assertEquals('Lorem', $result['middleware']);
    }
}
