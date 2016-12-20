<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\Transform\AbstractTransform;
use Framewub\BlockBuilder\Transform\Phtml;

class PhtmlTest extends TestCase
{
    protected $phtmlFile;

    public function setUp()
    {
        $this->phtmlFile = dirname(dirname(__DIR__)) .'/data/transform.phtml';
    }

    public function testConstruct()
    {
        $transform = new Phtml('/foo/bar.phtml');
        $this->assertInstanceOf(AbstractTransform::class, $transform);
    }

    public function testTransform()
    {
        $data = [
            'template' => 'block-template',
            'mods' => 'foo bar',
            'content' => 'Hello world'
        ];

        $transform = new Phtml($this->phtmlFile);
        $this->assertEquals('<div class="block-template foo bar">Hello world</div>', $transform->transform($data));
    }
}
