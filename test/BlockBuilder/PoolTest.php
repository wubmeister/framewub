<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\Pool;

class PoolTest extends TestCase
{
    public function testSet()
    {
        $pool = new Pool();
        $pool->set('key1', 'Source for key 1');
    }

    public function testHasKey()
    {
        $pool = new Pool();
        $this->assertFalse($pool->has('key1'));
        $pool->set('key1', 'Source for key 1');
        $this->assertTrue($pool->has('key1'));
    }

    public function testCompile()
    {
        $pool = new Pool();
        $pool->set('key1', 'Source for key 1');
        $pool->set('key2', 'Source for key 2');
        $this->assertEquals("Source for key 1\nSource for key 2\n", $pool->getCompiled());
    }
}
