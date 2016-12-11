<?php

use PHPUnit\Framework\TestCase;

use Framewub\Util;

class UtilTest extends TestCase
{
    public function testGetSingular()
    {
        $singular = Util::getSingular('tests');
        $this->assertEquals('test', $singular);

        $singular = Util::getSingular('criteria');
        $this->assertEquals('criterium', $singular);

        $singular = Util::getSingular('branches');
        $this->assertEquals('branch', $singular);
    }
}
