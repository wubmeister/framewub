<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\Schema\Xml;

class XmlTest extends TestCase
{
    public function testConstruct()
    {
        $schema = new Xml(dirname(dirname(__DIR__)) . '/data/bb/schema.xml');
        $layout = $schema->toArray();

        $this->assertInternalType('array', $layout);
        $this->assertEquals('mockblock', $layout[0]['block']);
        $this->assertEquals('blue', $layout[0]['mods']['color']);
        $this->assertEquals('mockelement', $layout[0]['content'][0]['element']);
        $this->assertEquals('medium', $layout[0]['content'][0]['mods']['size']);
    }
}
