<?php

use PHPUnit\Framework\TestCase;

use Framewub\Template\AbstractTemplate;

class Template_MockTemplate extends AbstractTemplate
{
    public function render($data = null)
    {
        $data = $data ? array_merge($this->data, $data) : $this->data;
        $target = $data && isset($data['target']) ? $data['target'] : 'world';
        $this->content = "Hello " . $target;
    }
}

class AbstractTemplateTest extends TestCase
{
    public function testRender()
    {
        $template = new Template_MockTemplate(dirname(__DIR__) . '/data/mocktemplate');
        $template->render();
        $this->assertEquals('Hello world', $template->getContent());
    }

    public function testRenderWithData()
    {
        $template = new Template_MockTemplate(dirname(__DIR__) . '/data/mocktemplate');
        $template->render([ 'target' => 'John Doe' ]);
        $this->assertEquals('Hello John Doe', $template->getContent());

        $template = new Template_MockTemplate(dirname(__DIR__) . '/data/mocktemplate');
        $template->target = 'John Doe';
        $template->render();
        $this->assertEquals('Hello John Doe', $template->getContent());
    }
}
