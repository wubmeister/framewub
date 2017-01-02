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
        $this->assertEquals("<div class=\"mockblock color-blue\">\n    Dingen en zaken\n</div>", $phtml);
    }
}
