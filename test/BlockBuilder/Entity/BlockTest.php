<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\Entity\AbstractEntity;
use Framewub\BlockBuilder\Entity\Block;
use Framewub\BlockBuilder\Config as BlockBuilderConfig;

class BlockBuilder_Entity_MockBlock extends Block
{
    public function getName()
    {
        return $this->name;
    }
}

class BlockTest extends TestCase
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
        $block = new BlockBuilder_Entity_MockBlock([
            'block' => 'mockblock'
        ]);
        $this->assertInstanceOf(AbstractEntity::class, $block);
        $this->assertEquals('mockblock', $block->getName());
    }

    public function testGetPhtml()
    {
        $block = new BlockBuilder_Entity_MockBlock([
            'block' => 'mockblock',
            'mods' => [ 'color' => 'blue' ],
            'content' => 'Dingen en zaken'
        ]);
        $phtml = $block->getPhtml();
        $this->assertEquals("<div class=\"specificswrap\">\n    <div class=\"themewrap\">\n    <div class=\"mockblock color-blue\">\n    Dingen en zaken\n</div>\n</div>\n</div>", $phtml);
    }

    public function testGetCss()
    {
        $block = new BlockBuilder_Entity_MockBlock([
            'block' => 'mockblock',
            'mods' => [ 'color' => 'blue' ],
            'content' => 'Dingen en zaken'
        ]);
        $css = $block->getCss();
        $this->assertEquals(".mockblock {\n    color: red;\n}\n.mockblock {\n    background-color: grey;\n}\n", $css);
    }

    public function testWithElement()
    {
        $block = new BlockBuilder_Entity_MockBlock([
            'block' => 'mockblock',
            'mods' => [ 'color' => 'blue' ],
            'content' => [
                [
                    'element' => 'mockelement',
                    'mods' => [ 'size' => 'medium' ],
                    'content' => 'Lorem ipsum'
                ]
            ]
        ]);

        $phtml = $block->getPhtml();
        $this->assertEquals("<div class=\"specificswrap\">\n    <div class=\"themewrap\">\n    <div class=\"mockblock color-blue\">\n    <div class=\"specificsitemwrap\">\n    <div class=\"themeitemwrap\">\n    <div class=\"mockelement size-medium\">\n    Lorem ipsum\n</div>\n</div>\n</div>\n</div>\n</div>\n</div>", $phtml);
    }
}
