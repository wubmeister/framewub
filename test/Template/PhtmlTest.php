<?php

use PHPUnit\Framework\TestCase;

use Framewub\Template\AbstractTemplate;
use Framewub\Template\Phtml;

class PhtmlTest extends TestCase
{
    public function testInherit()
    {
        $template = new Phtml(dirname(__DIR__) . '/data/template.phtml');
        $this->assertInstanceOf(AbstractTemplate::class, $template);
    }

    public function testRender()
    {
        $template = new Phtml(dirname(__DIR__) . '/data/template.phtml');
        $template->render();
        $this->assertEquals('Hello world', $template->getContent());
    }

    public function testRenderWithData()
    {
        $template = new Phtml(dirname(__DIR__) . '/data/template.phtml');
        $template->render([ 'target' => 'John Doe' ]);
        $this->assertEquals('Hello John Doe', $template->getContent());

        $template = new Phtml(dirname(__DIR__) . '/data/template.phtml');
        $template->target = 'John Doe';
        $template->render();
        $this->assertEquals('Hello John Doe', $template->getContent());
    }
}
