<?php

use PHPUnit\Framework\TestCase;

use Framewub\Storage\StorageInterface;
use Framewub\Storage\StorageObject;

class Storage_MockStorage implements StorageInterface
{
	public function save(array $values)
	{
		return isset($values['id']) ? $values['id'] : 1;
	}
}

class StorageObjectTest extends TestCase
{
    public function testConstruct()
    {
    	$storage = new Storage_MockStorage();

        $obj = new StorageObject($storage);
        $obj->foo = 'bar';
        $obj->lorem = 'ipsum';

        $this->assertEquals('bar', $obj->foo);
        $this->assertEquals('ipsum', $obj->lorem);
    }

    public function testSave()
    {
    	$storage = new Storage_MockStorage();

        $obj = new StorageObject($storage);
        $obj->foo = 'bar';
        $obj->lorem = 'ipsum';
        $id = $obj->save();

        $this->assertEquals(1, $id);
    }
}
