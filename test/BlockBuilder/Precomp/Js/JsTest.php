<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\Precomp\AbstractPrecomp;
use Framewub\BlockBuilder\Precomp\Js\Js;

class JsTest extends TestCase
{
    public function testExtends()
    {
        $precomp = new Js(dirname(dirname(dirname(__DIR__))) . '/data/blockbuilder1.js');
        $this->assertInstanceOf(AbstractPrecomp::class, $precomp);
    }

    public function testGetCss()
    {
        $precomp = new Js(dirname(dirname(dirname(__DIR__))) . '/data/blockbuilder1.js');
        $precomp->append(dirname(dirname(dirname(__DIR__))) . '/data/blockbuilder2.js');
        $this->assertEquals("(function(){\nwindow.location = '/foo/bar';\n})();\n(function(){\ndocument.querySelector('.lorem').innerHTML = 'ipsum';\n})();\n", $precomp->getCompiled());
    }
}
