<?php

use PHPUnit\Framework\TestCase;

use Framewub\Storage\BranchedObject;
use Framewub\Storage\AbstractBranchedStorage;

class Storage_BO_MockBranchedStorage extends AbstractBranchedStorage
{
    public function save(array $values)
    {
        return isset($values['id']) ? $values['id'] : 1;
    }

    public function find($where = null, $order = null)
    {
        return [];
    }

    public function findOne($idOrWhere = null)
    {
        return [];
    }

    public function insert(array $values){}
    public function update(array $values, $idOrWhere = null){}
    public function delete($idOrWhere = null){}
}

class BranchedObjectTest extends TestCase
{
    public function testAppendChild()
    {
        $storage = new Storage_BO_MockBranchedStorage();

        $obj = new BranchedObject($storage);
        $obj->name = 'root';
        $obj2 = new BranchedObject($storage);
        $obj2->name = 'sub';
        $obj3 = new BranchedObject($storage);
        $obj3->name = 'sub2';

        $obj->appendChild($obj2);
        $obj->appendChild($obj3);

        $children = $obj->getChildren();

        $this->assertEquals($obj2, $children[0]);
        $this->assertEquals($obj3, $children[1]);
    }

    public function testResetTreeLayout()
    {
        $storage = new Storage_BO_MockBranchedStorage();

        $obj = new BranchedObject($storage);
        $obj->name = 'root';
        $obj2 = new BranchedObject($storage);
        $obj2->name = 'sub1';
        $obj3 = new BranchedObject($storage);
        $obj3->name = 'sub2';
        $obj4 = new BranchedObject($storage);
        $obj4->name = 'sub3';

        $obj->appendChild($obj2);
        $obj->appendChild($obj3);
        $obj3->appendChild($obj4);

        $obj->resetTreeLayout();

        $this->assertEquals(1, $obj->left);
        $this->assertEquals(8, $obj->right);
        $this->assertEquals(2, $obj2->left);
        $this->assertEquals(3, $obj2->right);
        $this->assertEquals(4, $obj3->left);
        $this->assertEquals(7, $obj3->right);
        $this->assertEquals(5, $obj4->left);
        $this->assertEquals(6, $obj4->right);
    }

    public function testHasChildren()
    {
        $storage = new Storage_BO_MockBranchedStorage();

        $obj = new BranchedObject($storage);
        $obj2 = new BranchedObject($storage);

        $this->assertFalse($obj->hasChildren());
        $obj->appendChild($obj2);
        $this->assertTrue($obj->hasChildren());
    }
}
