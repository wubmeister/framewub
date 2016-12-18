<?php

use PHPUnit\Framework\TestCase;

use Framewub\Db\MySQL;
use Framewub\Storage\StorageObject;
use Framewub\Storage\Db\Rowset;
use Framewub\Storage\Db\AbstractStorage;

class ATSMockStorage extends AbstractStorage
{
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function setObjectClass($class)
    {
        $this->objectClass = $class;
    }
}

class ASTMockStorageObject extends StorageObject
{

}

class AbstractStorageTest extends \PHPUnit_Extensions_Database_TestCase
{
    private $sharedPdo;
    private $db;

    public function __construct()
    {
        $this->db = new MySQL([ 'dbname' => 'framewub_test' ], 'framewub', 'fr4m3wu8');
        $this->sharedPdo = $this->db->getPdo();
    }

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
        return $this->createDefaultDBConnection($this->sharedPdo, 'framewub_test');
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(dirname(__DIR__)).'/data/test-seed.xml');
    }

    public function testConstruct()
    {
        $storage = new ATSMockStorage($this->db);
        $storage->setTableName('tests');
    }

    public function testFind()
    {
        $storage = new ATSMockStorage($this->db);
        $storage->setTableName('tests');

        // Find all
        $rowset = $storage->find();
        $this->assertInstanceOf(Rowset::class, $rowset);
        $this->assertEquals(3, $rowset->count());
        $obj = $rowset->fetchOne();
        $this->assertInstanceOf(StorageObject::class, $obj);
        $this->assertEquals(1, $obj->id);
        $this->assertEquals('First test', $obj->name);

        // Find with where
        $rowset = $storage->find([ 'created' => [ '$lt' => '2016-01-01 00:00:00' ] ]);
        $this->assertInstanceOf(Rowset::class, $rowset);
        $this->assertEquals(2, $rowset->count());
        $obj = $rowset->fetchOne();
        $this->assertInstanceOf(StorageObject::class, $obj);
        $this->assertEquals(1, $obj->id);
        $this->assertEquals('First test', $obj->name);

        // Find with order
        $rowset = $storage->find(null, 'id DESC');
        $this->assertInstanceOf(Rowset::class, $rowset);
        $this->assertEquals(3, $rowset->count());
        $obj = $rowset->fetchOne();
        $this->assertInstanceOf(StorageObject::class, $obj);
        $this->assertEquals(3, $obj->id);
        $this->assertEquals('Third test', $obj->name);
    }

    public function testFindOne()
    {
        $storage = new ATSMockStorage($this->db);
        $storage->setTableName('tests');

        // Find one by ID
        $obj = $storage->findOne(1);
        $this->assertInstanceOf(StorageObject::class, $obj);
        $this->assertEquals(1, $obj->id);
        $this->assertEquals('First test', $obj->name);

        // Find one by conditions
        $obj = $storage->findOne([ 'name' => 'First test' ]);
        $this->assertInstanceOf(StorageObject::class, $obj);
        $this->assertEquals(1, $obj->id);
        $this->assertEquals('First test', $obj->name);
    }

    public function testInsert()
    {
        $storage = new ATSMockStorage($this->db);
        $storage->setTableName('tests');

        $now = date('Y-m-d H:i:s');

        $id = $storage->insert([ 'id' => 4, 'name' => 'Fourth test', 'created' => $now ]);
        $this->assertEquals(4, $id);

        // Find one by ID
        $obj = $storage->findOne($id);
        $this->assertInstanceOf(StorageObject::class, $obj);
        $this->assertEquals(4, $obj->id);
        $this->assertEquals('Fourth test', $obj->name);
        $this->assertEquals($now, $obj->created);
    }

    public function testUpdate()
    {
        $storage = new ATSMockStorage($this->db);
        $storage->setTableName('tests');

        $now = date('Y-m-d H:i:s');
        $id = $storage->insert([ 'name' => 'Next test', 'created' => $now ]);

        // Update by ID
        $now = date('Y-m-d H:i:s');
        $result = $storage->update([ 'name' => 'New test', 'modified' => $now ], $id);
        $this->assertEquals(1, $result);

        // Find one by ID
        $obj = $storage->findOne($id);
        $this->assertInstanceOf(StorageObject::class, $obj);
        $this->assertEquals('New test', $obj->name);
        $this->assertEquals($now, $obj->modified);

        // Update by conditions
        $now = date('Y-m-d H:i:s');
        $result = $storage->update([ 'name' => 'Modified test', 'modified' => $now ], [ 'name' => 'New test' ]);
        $this->assertEquals(1, $result);

        // Find one by ID
        $obj = $storage->findOne($id);
        $this->assertInstanceOf(StorageObject::class, $obj);
        $this->assertEquals('Modified test', $obj->name);
        $this->assertEquals($now, $obj->modified);
    }

    public function testDelete()
    {
        $storage = new ATSMockStorage($this->db);
        $storage->setTableName('tests');

        // Delete by ID
        $now = date('Y-m-d H:i:s');
        $id = $storage->insert([ 'name' => 'Test to delete', 'created' => $now ]);
        $result = $storage->delete($id);
        $this->assertEquals(1, $result);

        $obj = $storage->findOne($id);
        $this->assertEquals(null, $obj);

        // Delete by conditions
        $now = date('Y-m-d H:i:s');
        $id = $storage->insert([ 'name' => 'Test to delete', 'created' => $now ]);
        $result = $storage->delete([ 'name' => 'Test to delete' ]);
        $this->assertEquals(1, $result);

        $obj = $storage->findOne($id);
        $this->assertEquals(null, $obj);
    }

    public function testSave()
    {
        $storage = new ATSMockStorage($this->db);
        $storage->setTableName('tests');

        $now = date('Y-m-d H:i:s');

        // Save a new object
        $id = $storage->save([ 'name' => 'New test', 'created' => $now ]);
        $this->assertGreaterThan(3, $id);

        $obj = $storage->findOne($id);
        $this->assertInstanceOf(StorageObject::class, $obj);
        $this->assertEquals('New test', $obj->name);
        $this->assertEquals($now, $obj->created);

        // Update existing
        $id = $storage->save([ 'id' => $id, 'name' => 'Modified test', 'modified' => $now ]);

        $obj = $storage->findOne($id);
        $this->assertInstanceOf(StorageObject::class, $obj);
        $this->assertEquals('Modified test', $obj->name);
        $this->assertEquals($now, $obj->modified);
    }

    public function testSetObjectClass()
    {
        $storage = new ATSMockStorage($this->db);
        $storage->setTableName('tests');
        $storage->setObjectClass(ASTMockStorageObject::class);

        $obj = $storage->findOne(2);
        $this->assertInternalType('object', $obj);
        $this->assertInstanceOf(ASTMockStorageObject::class, $obj);
    }
}