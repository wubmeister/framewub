<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\Precomp\AbstractPrecomp;

class MockPrecomp extends AbstractPrecomp
{
    public function getFilename($index)
    {
        return $this->files[$index];
    }

    public function getCompiled()
    {
    	return 'body { margin: 0; }';
    }
}

class AbstractPrecompTest extends TestCase
{
    public function testFilename()
    {
        $precomp = new MockPrecomp('/foo/bar.css');
        $this->assertEquals('/foo/bar.css', $precomp->getFilename(0));
    }

    public function testAppend()
    {
        $precomp = new MockPrecomp('/foo/bar.css');
        $precomp->append('/foo/loremipsum.css');
        $this->assertEquals('/foo/bar.css', $precomp->getFilename(0));
        $this->assertEquals('/foo/loremipsum.css', $precomp->getFilename(1));
    }

    public function testGetCompiled()
    {
    	$precomp = new MockPrecomp('/foo/bar.css');
    	$this->assertEquals('body { margin: 0; }', $precomp->getCompiled());
    }
}
