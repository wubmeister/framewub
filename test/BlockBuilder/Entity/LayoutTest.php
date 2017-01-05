<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\Config as BlockBuilderConfig;
use Framewub\BlockBuilder\Entity\AbstractEntity;
use Framewub\BlockBuilder\Entity\Layout;

class LayoutTest extends TestCase
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
        $layout = new Layout([
            [ 'block' => 'mockblock' ]
        ]);
        $this->assertInstanceOf(AbstractEntity::class, $layout);
    }

    public function testWithBlockAndElement()
    {
        $layout = new Layout([
            [
                'block' => 'mockblock',
                'mods' => [ 'color' => 'blue' ],
                'content' => [
                    [
                        'element' => 'mockelement',
                        'mods' => [ 'size' => 'medium' ],
                        'content' => 'Lorem ipsum'
                    ]
                ]
            ]
        ]);

        $phtml = $layout->getPhtml();
        $this->assertEquals("<div class=\"specificswrap\">\n    <div class=\"themewrap\">\n    <div class=\"mockblock color-blue\">\n    <div class=\"specificsitemwrap\">\n    <div class=\"themeitemwrap\">\n    <div class=\"mockelement size-medium\">\n    Lorem ipsum\n</div>\n</div>\n</div>\n</div>\n</div>\n</div>", $phtml);
    }
}
