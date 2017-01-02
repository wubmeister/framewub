<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\Precomp\AbstractPrecomp;
use Framewub\BlockBuilder\Precomp\Css\Css;

class CssTest extends TestCase
{
    public function testExtends()
    {
        $precomp = new Css(dirname(dirname(dirname(__DIR__))) . '/data/blockbuilder1.css');
        $this->assertInstanceOf(AbstractPrecomp::class, $precomp);
    }

    public function testGetCss()
    {
        $precomp = new Css(dirname(dirname(dirname(__DIR__))) . '/data/blockbuilder1.css');
        $precomp->append(dirname(dirname(dirname(__DIR__))) . '/data/blockbuilder2.css');
        $this->assertEquals("body {\n    margin: 0;\n    padding: 0;\n}\n.container {\n    width: 100%;\n    max-width: 960px;\n    margin: 0 auto;\n}\n", $precomp->getCompiled());
    }
}
