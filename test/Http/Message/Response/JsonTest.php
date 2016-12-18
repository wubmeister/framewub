<?php

use PHPUnit\Framework\TestCase;

use Framewub\Http\Message\Response\Json as JsonResponse;

class JsonTest extends TestCase
{
    public function testJson()
    {
        $response = new JsonResponse();
        ob_start();
        $response->getBody()->write([ 'foo' => 'bar', 'lorem' => 'ipsum' ]);
        ob_end_clean();
        $this->assertEquals('{"foo":"bar","lorem":"ipsum"}', $response->getBody()->getMockContents());
    }
}
