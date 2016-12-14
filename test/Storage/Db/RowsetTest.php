<?php

use PHPUnit\Framework\TestCase;

use Framewub\Db\MySQL;
use Framewub\Storage\Db\Rowset;
use Framewub\Storage\StorageObject;
use Framewub\Db\Query\Select;
use Framewub\Storage\Db\AbstractStorage;

class MockStorageObject extends StorageObject
{

}

class MockStorage extends AbstractStorage
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

class RowsetTest extends \PHPUnit_Extensions_Database_TestCase
{
    private $sharedPdo;
    private $storage;
    private $db;

    protected function setUp()
    {
    }

    public function __construct()
    {
        $this->db = new MySQL([ 'dbname' => 'framewub_test' ], 'framewub', 'fr4m3wu8');
        $this->sharedPdo = $this->db->getPdo();
        $this->storage = new MockStorage($this->db);
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

    public function testSetObjectClass()
    {
        $select = new Select($this->db);
        $select->from('tests')->order('id');
        $rowset = new Rowset($select, $this->storage);
        $rowset->setObjectClass(MockStorageObject::class);

        $result = $rowset->fetchOne();
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(MockStorageObject::class, $result);
    }

    public function testFetchOne()
    {
        $select = new Select($this->db);
        $select->from('tests')->order('id');
        $rowset = new Rowset($select, $this->storage);

        $result = $rowset->fetchOne();
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(StorageObject::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('First test', $result->name);
        $this->assertEquals('2012-12-10 16:30:00', $result->created);

        $result = $rowset->fetchOne();
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(StorageObject::class, $result);
        $this->assertEquals(2, $result->id);
        $this->assertEquals('Second test', $result->name);
        $this->assertEquals('2014-12-10 16:30:00', $result->created);
    }

    public function testFetchAll()
    {
        $select = new Select($this->db);
        $select->from('tests')->order('id');
        $rowset = new Rowset($select, $this->storage);

        $result = $rowset->fetchAll();
        $this->assertInternalType('array', $result);

        $this->assertInternalType('object', $result[0]);
        $this->assertInstanceOf(StorageObject::class, $result[0]);
        $this->assertEquals(1, $result[0]->id);
        $this->assertEquals('First test', $result[0]->name);
        $this->assertEquals('2012-12-10 16:30:00', $result[0]->created);

        $this->assertInternalType('object', $result[1]);
        $this->assertInstanceOf(StorageObject::class, $result[1]);
        $this->assertEquals(2, $result[1]->id);
        $this->assertEquals('Second test', $result[1]->name);
        $this->assertEquals('2014-12-10 16:30:00', $result[1]->created);

        $this->assertInternalType('object', $result[2]);
        $this->assertInstanceOf(StorageObject::class, $result[2]);
        $this->assertEquals(3, $result[2]->id);
        $this->assertEquals('Third test', $result[2]->name);
        $this->assertEquals('2016-12-10 16:30:00', $result[2]->created);
    }

    public function testCount()
    {
        $select = new Select($this->db);
        $select->from('tests')->order('id');
        $rowset = new Rowset($select, $this->storage);

        $count = $rowset->count();
        $this->assertEquals(3, $count);

        $result = $rowset->fetchOne();
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(StorageObject::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function testIterate()
    {
        $select = new Select($this->db);
        $select->from('tests')->order('id');
        $rowset = new Rowset($select, $this->storage);

        foreach ($rowset as $i => $result) {
            $this->assertInternalType('object', $result);
            $this->assertInstanceOf(StorageObject::class, $result);

            switch ($i) {
                case 0:
                    $this->assertEquals(1, $result->id);
                    $this->assertEquals('First test', $result->name);
                    $this->assertEquals('2012-12-10 16:30:00', $result->created);
                    break;

                case 1:
                    $this->assertEquals(2, $result->id);
                    $this->assertEquals('Second test', $result->name);
                    $this->assertEquals('2014-12-10 16:30:00', $result->created);
                    break;

                case 2:
                    $this->assertEquals(3, $result->id);
                    $this->assertEquals('Third test', $result->name);
                    $this->assertEquals('2016-12-10 16:30:00', $result->created);
                    break;
            }
        }

        $this->assertEquals(2, $i);
    }
}