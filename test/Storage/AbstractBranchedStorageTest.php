<?php

use PHPUnit\Framework\TestCase;

use Framewub\Storage\BranchedObject;
use Framewub\Storage\StorageInterface;
use Framewub\Storage\AbstractBranchedStorage;


class Storage_MockBranchedStorage extends AbstractBranchedStorage
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

class Storage_Branched_MockStorageObject extends BranchedObject
{
    public function __construct(StorageInterface $storage, $data = [])
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }
}

class AbstractBranchedStorageTest extends TestCase
{
    public function testGetLeftRightKeys()
    {
        $storage = new Storage_MockBranchedStorage();

        $this->assertEquals('left', $storage->getLeftKey());
        $this->assertEquals('right', $storage->getRightKey());
    }

    public function testTreeFromData()
    {
        $storage = new Storage_MockBranchedStorage();

        $data = [
            new Storage_Branched_MockStorageObject($storage, [ 'left' => 1, 'right' => 9, 'name' => 'Root' ]),
            new Storage_Branched_MockStorageObject($storage, [ 'left' => 2, 'right' => 3, 'name' => 'Sub 1' ]),
            new Storage_Branched_MockStorageObject($storage, [ 'left' => 5, 'right' => 8, 'name' => 'Sub 2' ]),
            new Storage_Branched_MockStorageObject($storage, [ 'left' => 6, 'right' => 7, 'name' => 'Sub 2.1' ])
        ];

        $tree = $storage->getTreeFromData($data);
        $this->assertEquals($data[0], $tree);
        $children = $tree->getChildren();
        $this->assertEquals($data[1], $children[0]);
        $this->assertEquals($data[2], $children[1]);
        $subChildren = $children[1]->getChildren();
        $this->assertEquals($data[3], $subChildren[0]);
    }
}
