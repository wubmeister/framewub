<?php

use PHPUnit\Framework\TestCase;

use Framewub\Services;
use Framewub\Db\MySQL;
use Framewub\Storage\Db\StorageObject;
use Framewub\Storage\Db\Rowset;
use Framewub\Storage\Db\AbstractStorage;

class DSOTestStorage extends AbstractStorage
{
    protected $tableName = 'tests';
    protected $objectClass = StorageObject::class;

    protected $relations = [
        'testcases' => [ 'type' => self::ONE_TO_MANY, 'storage' => 'DSOTestcaseStorage', 'fkToSelf' => 'test_id' ]
    ];
}

class DSOTestcaseStorage extends AbstractStorage
{
    protected $tableName = 'testcases';
    protected $objectClass = StorageObject::class;

    protected $relations = [
        'tests' => [ 'type' => self::MANY_TO_ONE, 'storage' => 'DSOTestStorage', 'fkToOther' => 'test_id' ],
        'items' => [ 'type' => self::MANY_TO_MANY, 'storage' => 'DSOItemStorage', 'fkToSelf' => 'testcase_id', 'fkToOther' => 'item_id', 'linkTable' => 'testcase_has_items' ]
    ];
}

class DSOItemStorage extends AbstractStorage
{
    protected $tableName = 'items';
    protected $objectClass = StorageObject::class;

    protected $relations = [
        'testcases' => [ 'type' => self::MANY_TO_MANY, 'storage' => 'DSOTestcaseStorage', 'fkToSelf' => 'item_id', 'fkToOther' => 'testcase_id', 'linkTable' => 'testcase_has_items' ]
    ];
}

class StorageObjectTest extends \PHPUnit_Extensions_Database_TestCase
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
        return $this->createFlatXMLDataSet(dirname(dirname(__DIR__)).'/data/related-seed.xml');
    }

    public function testFetchRelated()
    {
        $testStorage = Services::get(DSOTestStorage::class, $this->db);
        $test = $testStorage->findOne(1);
        $this->assertInstanceOf(StorageObject::class, $test);

        $test->fetchTestcases();
        $this->assertInstanceOf(Rowset::class, $test->testcases);
        $testcase = $test->testcases->fetchOne();
        $this->assertInstanceOf(StorageObject::class, $testcase);
        $this->assertEquals('First test, first testcase', $testcase->name);
    }

    public function testAddRelated()
    {
        $testStorage = Services::get(DSOTestStorage::class, $this->db);
        $testcaseStorage = Services::get(DSOTestcaseStorage::class, $this->db);

        $test = $testStorage->findOne(1);

        $test->addTestcase(4);

        $testcase = $testcaseStorage->findOne(4);

        $this->assertInstanceOf(StorageObject::class, $testcase);
        $this->assertEquals(1, $testcase->test_id);
    }

    public function testUnlinkRelated()
    {
        $testStorage = Services::get(DSOTestStorage::class, $this->db);
        $testcaseStorage = Services::get(DSOTestcaseStorage::class, $this->db);

        $test = $testStorage->findOne(1);

        $test->unlinkTestcase(3);

        $testcase = $testcaseStorage->findOne(3);

        $this->assertInstanceOf(StorageObject::class, $testcase);
        $this->assertNull($testcase->test_id);
    }
}
