<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\Config as BlockBuilderConfig;
use Framewub\BlockBuilder\Entity\AbstractEntity;
use Framewub\BlockBuilder\Entity\Element;
use Framewub\BlockBuilder\Entity\Block;

class BlockBuilder_Entity_MockElement extends Element
{
    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        return $this->path;
    }
}

class ElementTest extends TestCase
{
    public function setUp()
    {
        $basedir = dirname(dirname(__DIR__)) . '/data/bb/';
        BlockBuilderConfig::setDirs(
            $basedir . 'global',
            $basedir . 'theme',
            $basedir . 'specifics'
        );
    }

    public function testConstruct()
    {
        $element = new BlockBuilder_Entity_MockElement([
            'element' => 'mockelement'
        ]);
        $this->assertInstanceOf(AbstractEntity::class, $element);
        $this->assertEquals('mockelement', $element->getName());
        $this->assertEquals('mockelement', $element->getPath());
    }

    public function testWithBlock()
    {
        $block = new Block([ 'block' => 'mockblock' ]);
        $element = new BlockBuilder_Entity_MockElement([
            'element' => 'mockelement'
        ]);
        $element->setParent($block);
        $this->assertInstanceOf(AbstractEntity::class, $element);
        $this->assertEquals('mockelement', $element->getName());
        $this->assertEquals('mockblock/mockelement', $element->getPath());
    }

    public function testGetPhtml()
    {
        $block = new Block([ 'block' => 'mockblock' ]);
        $element = new BlockBuilder_Entity_MockElement([
            'element' => 'mockelement',
            'mods' => [ 'size' => 'medium' ],
            'content' => 'Lorem ipsum'
        ]);
        $element->setParent($block);
        $phtml = $element->getPhtml();
        $this->assertEquals("<div class=\"specificsitemwrap\">\n    <div class=\"themeitemwrap\">\n    <div class=\"mockelement size-medium\">\n    Lorem ipsum\n</div>\n</div>\n</div>", $phtml);
    }
}
