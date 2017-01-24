<?php

use PHPUnit\Framework\TestCase;

use Framewub\Route\AbstractRoute;
use Framewub\Route\Segment;
use Framewub\Route\Literal;

class SegmentTest extends TestCase
{
    public function testConstruct()
    {
        $route = new Segment('segment', 'SegmentAction');
        $this->assertInstanceOf(AbstractRoute::class, $route);
    }

    public function testMatch()
    {
        $route = new Segment('/foo/{id}', 'SegmentAction');

        // Should not match
        $result = $route->match('/lorem/ipsum');
        $this->assertEquals(null, $result);

        // Should match
        $result = $route->match('/foo/bar');
        $this->assertInternalType('array', $result);
        $this->assertEquals('SegmentAction', $result['middleware']);
        $this->assertInternalType('array', $result['params']);
        $this->assertEquals('bar', $result['params']['id']);

        // Should match
        $result = $route->match('/foo/123/lorem/ipsum');
        $this->assertInternalType('array', $result);
        $this->assertEquals('SegmentAction', $result['middleware']);
        $this->assertEquals('123', $result['params']['id']);
        $this->assertEquals('/lorem/ipsum', $result['tail']);
    }

    public function testBuild()
    {
        $route = new Segment('/foo/{id}', 'SegmentAction');
        $route->addChildRoute('lorem', new Literal('/lorem/ipsum', 'LoremIpsum'));

        // Should not match
        $result = $route->build([ 'id' => 123 ]);
        $this->assertEquals('/foo/123', $result);

        // Should not match
        $result = $route->build('lorem', [ 'id' => 123 ]);
        $this->assertEquals('/foo/123/lorem/ipsum', $result);
    }
}
