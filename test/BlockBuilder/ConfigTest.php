<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\Config;

class BlovckBuilder_ConfigTest extends TestCase
{
    public function testConfig()
    {
    	Config::setDirs('themes/global', 'themes/mytheme', 'themes/projects/myproject');

    	$this->assertEquals('themes/global', Config::$globalDir);
    	$this->assertEquals('themes/mytheme', Config::$themeDir);
    	$this->assertEquals('themes/projects/myproject', Config::$specificsDir);
    }
}
