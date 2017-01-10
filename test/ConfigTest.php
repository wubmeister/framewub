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

    public function testExplicitGet()
    {
        $config = new Config([ 'foo' => 'bar', 'lorem' => [ 'ipsum' => 'doler', 'sit' => 'amet' ]]);

        $this->assertEquals('bar', $config->get('foo'));
        $this->assertEquals(null, $config->get('dingen'));
        $this->assertInstanceOf(Config::class, $config->get('lorem'));
        $this->assertEquals('doler', $config->get('lorem')->get('ipsum'));
        $this->assertEquals('amet', $config->get('lorem')->get('sit'));
    }

    public function testHas()
    {
        $config = new Config([ 'foo' => 'bar', 'lorem' => [ 'ipsum' => 'doler', 'sit' => 'amet' ]]);

        $this->assertFalse($config->has('bar'));
        $this->assertTrue($config->has('foo'));
        $this->assertTrue($config->has('lorem'));
    }

    public function testMerge()
    {
        $config = new Config([ 'foo' => 'bar', 'lorem' => [ 'ipsum' => 'doler', 'sit' => 'amet' ]]);
        $config2 = new Config([ 'bar' => 'barbar', 'lorem' => [ 'ipsum' => 'doler sit amet', 'amet' => 'trens roxnas' ]]);

        $config->mergeWith($config2);

        $this->assertEquals('bar', $config->foo);
        $this->assertEquals('barbar', $config->bar);
        $this->assertEquals('doler sit amet', $config->lorem->ipsum);
        $this->assertEquals('amet', $config->lorem->sit);
        $this->assertEquals('trens roxnas', $config->lorem->amet);
    }

    // public function testSerialize()
    // {
    //  $config = new Config([ 'foo' => 'bar', 'lorem' => [ 'ipsum' => 'doler', 'sit' => 'amet' ]]);
    //  $this->assertEquals('O:15:"Framewub\Config":2:{s:3:"foo";s:3:"bar";s:5:"lorem";O:15:"Framewub\Config":2:{s:5:"ipsum";s:5:"doler";s:3:"sit";s:4:"amet";}}', serialize($config));
    // }

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

    public function testIterate()
    {
        $configArray = [ 'foo' => 'bar', 'lorem' => 'ipsum' ];
        $config = new Config($configArray);

        foreach ($config as $key => $value)
        {
            $this->assertEquals($configArray[$key], $value);
        }
    }
}
