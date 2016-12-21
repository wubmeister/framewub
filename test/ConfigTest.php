<?php

use PHPUnit\Framework\TestCase;

use Framewub\Config;

class ConfigTest extends TestCase
{
    public function testFromArray()
    {
    	$config = new Config([ 'foo' => 'bar', 'lorem' => [ 'ipsum' => 'doler', 'sit' => 'amet' ]]);

    	$this->assertEquals('bar', $config->foo);
    	$this->assertEquals(null, $config->dingen);
    	$this->assertInstanceOf(Config::class, $config->lorem);
    	$this->assertEquals('doler', $config->lorem->ipsum);
    	$this->assertEquals('amet', $config->lorem->sit);
    }

    public function testSerialize()
    {
    	$config = new Config([ 'foo' => 'bar', 'lorem' => [ 'ipsum' => 'doler', 'sit' => 'amet' ]]);
    	$this->assertEquals('O:15:"Framewub\Config":2:{s:3:"foo";s:3:"bar";s:5:"lorem";O:15:"Framewub\Config":2:{s:5:"ipsum";s:5:"doler";s:3:"sit";s:4:"amet";}}', serialize($config));
    }

    public function testFromFile()
    {
    	$filename = __DIR__ . '/data/config.php';
    	$config = new Config(include $filename);

    	$this->assertEquals('bar', $config->foo);
    	$this->assertEquals(null, $config->dingen);
    	$this->assertInstanceOf(Config::class, $config->lorem);
    	$this->assertEquals('doler', $config->lorem->ipsum);
    	$this->assertEquals('amet', $config->lorem->sit);
    }
}
