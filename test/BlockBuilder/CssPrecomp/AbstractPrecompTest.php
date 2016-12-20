<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\CssPrecomp\AbstractPrecomp;

class MockPrecomp extends AbstractPrecomp
{
    public function getFilename($index)
    {
        return $this->files[$index];
    }
}

class AbstractPrecompTest extends TestCase
{
    public function testFilename()
    {
        $precomp = new MockPrecomp('/foo/bar.css');
        $this->assertEquals('/foo/bar.css', $precomp->getFilename(0));
    }
}
