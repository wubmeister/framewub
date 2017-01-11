<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\Precomp\AbstractPrecomp;
use Framewub\BlockBuilder\Precomp\Css\Less;

class LessTest extends TestCase
{
    public function testExtends()
    {
        $precomp = new Less(dirname(dirname(dirname(__DIR__))) . '/data/bb/correct.less');
        $this->assertInstanceOf(AbstractPrecomp::class, $precomp);
    }

    public function testCompile()
    {
        $precomp = new Less(dirname(dirname(dirname(__DIR__))) . '/data/bb/correct.less');
        $this->assertEquals(
            "@mode extend;\n.correct {\n  color: green;\n}\n.correct span {\n  text-decoration: underline;\n}\n.correct.link {\n  cursor: pointer;\n}\n",
            $precomp->getCompiled()
        );
    }

    public function testCompileErrors()
    {
        $precomp = new Less(dirname(dirname(dirname(__DIR__))) . '/data/bb/incorrect.less');
        $this->assertEquals(
            "",
            $precomp->getCompiled()
        );

        $errors = $precomp->getCompilerErrors();
        $this->assertInternalType('array', $errors);
        $this->assertEquals(1, count($errors));
        $this->assertEquals('NameError: variable @mainFont is undefined', substr($errors[0], 0, 42));
    }
}
