<?php

use PHPUnit\Framework\TestCase;

use Framewub\Storage\Db\AbstractStorage;
use Framewub\Storage\Db\AbstractRelated;
use Framewub\Storage\Db\Rowset;
use Framewub\Storage\StorageObject;
use Framewub\Db\MySQL;
use Framewub\Services;

class ARTestStorage extends AbstractRelated
{
    protected $tableName = 'tests';

    protected $relations = [
        'testcases' => [ 'type' => self::ONE_TO_MANY, 'storage' => 'ARTestcaseStorage', 'fkToSelf' => 'test_id' ]
    ];
}

class ARTestcaseStorage extends AbstractRelated
{
    protected $tableName = 'testcases';

    protected $relations = [
        'tests' => [ 'type' => self::MANY_TO_ONE, 'storage' => 'ARTestStorage', 'fkToOther' => 'test_id' ],
        'items' => [ 'type' => self::MANY_TO_MANY, 'storage' => 'ARItemStorage', 'fkToSelf' => 'testcase_id', 'fkToOther' => 'item_id', 'linkTable' => 'testcase_has_items' ]
    ];
}

class ARItemStorage extends AbstractRelated
{
    protected $tableName = 'items';

    protected $relations = [
        'testcases' => [ 'type' => self::MANY_TO_MANY, 'storage' => 'ARTestcaseStorage', 'fkToSelf' => 'item_id', 'fkToOther' => 'testcase_id', 'linkTable' => 'testcase_has_items' ]
    ];
}

class AbstractRelatedTest extends \PHPUnit_Extensions_Database_TestCase
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

    public function testFindByRelated()
    {
        $storage = Services::get(ARTestcaseStorage::class, $this->db);
        $testcases = $storage->findByTest(1);

        $this->assertInstanceOf(Rowset::class, $testcases);

        $tc = $testcases->fetchOne();
        $this->assertInstanceOf(StorageObject::class, $tc);
        $this->assertEquals('First test, first testcase', $tc->name);

        $storage = Services::get(ARTestStorage::class, $this->db);
        $tests = $storage->findByTestcase(1);

        $this->assertInstanceOf(Rowset::class, $tests);

        $test = $tests->fetchOne();
        $this->assertInstanceOf(StorageObject::class, $test);
        $this->assertEquals('First test', $test->name);

        $storage = Services::get(ARItemStorage::class, $this->db);
        $items = $storage->findByTestcase(3);

        $this->assertInstanceOf(Rowset::class, $items);

        $item = $items->fetchOne();
        $this->assertInstanceOf(StorageObject::class, $item);
        $this->assertEquals('Second item', $item->name);
    }

    public function testFindRelated()
    {
        $storage = Services::get(ARTestStorage::class, $this->db);
        $testcases = $storage->findTestcases(1);

        $this->assertInstanceOf(Rowset::class, $testcases);

        $tc = $testcases->fetchOne();
        $this->assertInstanceOf(StorageObject::class, $tc);
        $this->assertEquals('First test, first testcase', $tc->name);

        $storage = Services::get(ARTestcaseStorage::class, $this->db);
        $tests = $storage->findTest(1);

        $this->assertInstanceOf(Rowset::class, $tests);

        $test = $tests->fetchOne();
        $this->assertInstanceOf(StorageObject::class, $test);
        $this->assertEquals('First test', $test->name);

        $items = $storage->findItems(3);

        $this->assertInstanceOf(Rowset::class, $items);

        $item = $items->fetchOne();
        $this->assertInstanceOf(StorageObject::class, $item);
        $this->assertEquals('Second item', $item->name);
    }

    public function testAddRelated()
    {
        $testStorage = Services::get(ARTestStorage::class, $this->db);
        $testcaseStorage = Services::get(ARTestcaseStorage::class, $this->db);

        $testStorage->addTestcase(1, 9);
        $testcase = $testcaseStorage->findOne([ 'id' => 9 ]);
        $this->assertEquals(1, $testcase->test_id);

        $testcaseStorage->addTest(8, 1);
        $testcase = $testcaseStorage->findOne([ 'id' => 8 ]);
        $this->assertEquals(1, $testcase->test_id);

        $testcaseStorage->addItem(1, 2);
        $sql = "SELECT * FROM testcase_has_items WHERE testcase_id = 1 AND item_id = 2";
        $stmt = $this->db->execute($sql);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertInternalType('array', $result);
        $this->assertEquals(1, $result['testcase_id']);
        $this->assertEquals(2, $result['item_id']);
    }

    public function testUnlinkRelated()
    {
        $testStorage = Services::get(ARTestStorage::class, $this->db);
        $testcaseStorage = Services::get(ARTestcaseStorage::class, $this->db);

        $testStorage->unlinkTestcase(1, 2);
        $testcase = $testcaseStorage->findOne([ 'id' => 2 ]);
        $this->assertNull($testcase->test_id);

        $testcaseStorage->unlinkTest(3, 1);
        $testcase = $testcaseStorage->findOne([ 'id' => 3 ]);
        $this->assertNull($testcase->test_id);

        $testcaseStorage->unlinkItem(2, 1);
        $sql = "SELECT * FROM testcase_has_items WHERE testcase_id = 2 AND item_id = 1";
        $stmt = $this->db->execute($sql);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertFalse($result);
    }
}
