<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\Transform\AbstractTransform;

class BlockBuilder_Transform_MockTransform extends AbstractTransform
{
    public function getFilename()
    {
        return $this->filename;
    }

    public function transform(array $data)
    {
        $phtml = "<div class=\"{$data['template']} {$data['mods']}\">{$data['content']}</div>";
        return $phtml;
    }
}

class AbstractTransformTest extends TestCase
{
    public function testFilename()
    {
        $transform = new BlockBuilder_Transform_MockTransform('/foo/bar.xlst');
        $this->assertEquals('/foo/bar.xlst', $transform->getFilename());
    }

    public function testTransform()
    {
        $data = [
            'template' => 'block-template',
            'mods' => 'foo bar',
            'content' => 'Hello world'
        ];

        $transform = new BlockBuilder_Transform_MockTransform('/foo/bar.xlst');
        $this->assertEquals('<div class="block-template foo bar">Hello world</div>', $transform->transform($data));
    }
}
