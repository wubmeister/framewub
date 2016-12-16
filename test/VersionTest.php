<?php

use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    public function testVersion()
    {
    	echo PHP_EOL . "PHP Version: " . phpversion() . PHP_EOL;
    }
}
