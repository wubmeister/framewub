<?php

use PHPUnit\Framework\TestCase;

use Framewub\Services;
use Framewub\Db\MySQL;
use Framewub\Storage\StorageObject;
use Framewub\Storage\Db\Rowset;
use Framewub\Storage\Db\AbstractStorage;

class ASTestObject extends StorageObject{}
class ASTestcaseObject extends StorageObject{}
class ASItemObject extends StorageObject{}

class ASTestStorage extends AbstractStorage
{
    protected $tableName = 'tests';
    protected $objectClass = ASTestObject::class;

    protected $relations = [
        'testcases' => [ 'type' => self::ONE_TO_MANY, 'storage' => 'ASTestcaseStorage', 'fkToSelf' => 'test_id' ]
    ];
}

class ASTestcaseStorage extends AbstractStorage
{
    protected $tableName = 'testcases';
    protected $objectClass = ASTestcaseObject::class;

    protected $relations = [
        'tests' => [ 'type' => self::MANY_TO_ONE, 'storage' => 'ASTestStorage', 'fkToOther' => 'test_id' ],
        'items' => [ 'type' => self::MANY_TO_MANY, 'storage' => 'ASItemStorage', 'fkToSelf' => 'testcase_id', 'fkToOther' => 'item_id', 'linkTable' => 'testcase_has_items' ]
    ];
}

class ASItemStorage extends AbstractStorage
{
    protected $tableName = 'items';
    protected $objectClass = ASItemObject::class;

    protected $relations = [
        'testcases' => [ 'type' => self::MANY_TO_MANY, 'storage' => 'ASTestcaseStorage', 'fkToSelf' => 'item_id', 'fkToOther' => 'testcase_id', 'linkTable' => 'testcase_has_items' ]
    ];
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
        return $this->createFlatXMLDataSet(dirname(dirname(__DIR__)).'/data/related-seed.xml');
    }

    public function testConstruct()
    {
        $storage = new ASTestStorage($this->db);
    }

    public function testFind()
    {
        $storage = new ASTestStorage($this->db);

        // Find all
        $rowset = $storage->find();
        $this->assertInstanceOf(Rowset::class, $rowset);
        $this->assertEquals(3, $rowset->count());
        $obj = $rowset->fetchOne();
        $this->assertInstanceOf(ASTestObject::class, $obj);
        $this->assertEquals(1, $obj->id);
        $this->assertEquals('First test', $obj->name);

        // Find with where
        $rowset = $storage->find([ 'created' => [ '$lt' => '2016-01-01 00:00:00' ] ]);
        $this->assertInstanceOf(Rowset::class, $rowset);
        $this->assertEquals(2, $rowset->count());
        $obj = $rowset->fetchOne();
        $this->assertInstanceOf(ASTestObject::class, $obj);
        $this->assertEquals(1, $obj->id);
        $this->assertEquals('First test', $obj->name);

        // Find with order
        $rowset = $storage->find(null, 'id DESC');
        $this->assertInstanceOf(Rowset::class, $rowset);
        $this->assertEquals(3, $rowset->count());
        $obj = $rowset->fetchOne();
        $this->assertInstanceOf(ASTestObject::class, $obj);
        $this->assertEquals(3, $obj->id);
        $this->assertEquals('Third test', $obj->name);
    }

    public function testFindOne()
    {
        $storage = new ASTestStorage($this->db);

        // Find one by ID
        $obj = $storage->findOne(1);
        $this->assertInstanceOf(ASTestObject::class, $obj);
        $this->assertEquals(1, $obj->id);
        $this->assertEquals('First test', $obj->name);

        // Find one by conditions
        $obj = $storage->findOne([ 'name' => 'First test' ]);
        $this->assertInstanceOf(ASTestObject::class, $obj);
        $this->assertEquals(1, $obj->id);
        $this->assertEquals('First test', $obj->name);
    }

    public function testInsert()
    {
        $storage = new ASTestStorage($this->db);

        $now = date('Y-m-d H:i:s');

        $id = $storage->insert([ 'id' => 4, 'name' => 'Fourth test', 'created' => $now ]);
        $this->assertEquals(4, $id);

        // Find one by ID
        $obj = $storage->findOne($id);
        $this->assertInstanceOf(ASTestObject::class, $obj);
        $this->assertEquals(4, $obj->id);
        $this->assertEquals('Fourth test', $obj->name);
        $this->assertEquals($now, $obj->created);
    }

    public function testUpdate()
    {
        $storage = new ASTestStorage($this->db);

        $now = date('Y-m-d H:i:s');
        $id = $storage->insert([ 'name' => 'Next test', 'created' => $now ]);

        // Update by ID
        $now = date('Y-m-d H:i:s');
        $result = $storage->update([ 'name' => 'New test', 'modified' => $now ], $id);
        $this->assertEquals(1, $result);

        // Find one by ID
        $obj = $storage->findOne($id);
        $this->assertInstanceOf(ASTestObject::class, $obj);
        $this->assertEquals('New test', $obj->name);
        $this->assertEquals($now, $obj->modified);

        // Update by conditions
        $now = date('Y-m-d H:i:s');
        $result = $storage->update([ 'name' => 'Modified test', 'modified' => $now ], [ 'name' => 'New test' ]);
        $this->assertEquals(1, $result);

        // Find one by ID
        $obj = $storage->findOne($id);
        $this->assertInstanceOf(ASTestObject::class, $obj);
        $this->assertEquals('Modified test', $obj->name);
        $this->assertEquals($now, $obj->modified);
    }

    public function testDelete()
    {
        $storage = new ASTestStorage($this->db);

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
        $storage = new ASTestStorage($this->db);

        $now = date('Y-m-d H:i:s');

        // Save a new object
        $id = $storage->save([ 'name' => 'New test', 'created' => $now ]);
        $this->assertGreaterThan(3, $id);

        $obj = $storage->findOne($id);
        $this->assertInstanceOf(ASTestObject::class, $obj);
        $this->assertEquals('New test', $obj->name);
        $this->assertEquals($now, $obj->created);

        // Update existing
        $id = $storage->save([ 'id' => $id, 'name' => 'Modified test', 'modified' => $now ]);

        $obj = $storage->findOne($id);
        $this->assertInstanceOf(ASTestObject::class, $obj);
        $this->assertEquals('Modified test', $obj->name);
        $this->assertEquals($now, $obj->modified);
    }

    public function testFindByRelated()
    {
        $storage = Services::get(ASTestcaseStorage::class, $this->db);
        $testcases = $storage->findByTest(1);

        $this->assertInstanceOf(Rowset::class, $testcases);

        $tc = $testcases->fetchOne();
        $this->assertInstanceOf(ASTestcaseObject::class, $tc);
        $this->assertEquals('First test, first testcase', $tc->name);

        $storage = Services::get(ASTestStorage::class, $this->db);
        $tests = $storage->findByTestcase(1);

        $this->assertInstanceOf(Rowset::class, $tests);

        $test = $tests->fetchOne();
        $this->assertInstanceOf(ASTestObject::class, $test);
        $this->assertEquals('First test', $test->name);

        $storage = Services::get(ASItemStorage::class, $this->db);
        $items = $storage->findByTestcase(3);

        $this->assertInstanceOf(Rowset::class, $items);

        $item = $items->fetchOne();
        $this->assertInstanceOf(ASItemObject::class, $item);
        $this->assertEquals('Second item', $item->name);
    }

    public function testFindRelated()
    {
        $storage = Services::get(ASTestStorage::class, $this->db);
        $testcases = $storage->findTestcases(1);

        $this->assertInstanceOf(Rowset::class, $testcases);

        $tc = $testcases->fetchOne();
        $this->assertInstanceOf(ASTestcaseObject::class, $tc);
        $this->assertEquals('First test, first testcase', $tc->name);

        $storage = Services::get(ASTestcaseStorage::class, $this->db);
        $tests = $storage->findTest(1);

        $this->assertInstanceOf(Rowset::class, $tests);

        $test = $tests->fetchOne();
        $this->assertInstanceOf(ASTestObject::class, $test);
        $this->assertEquals('First test', $test->name);

        $items = $storage->findItems(3);

        $this->assertInstanceOf(Rowset::class, $items);

        $item = $items->fetchOne();
        $this->assertInstanceOf(ASItemObject::class, $item);
        $this->assertEquals('Second item', $item->name);
    }

    public function testAddRelated()
    {
        $testStorage = Services::get(ASTestStorage::class, $this->db);
        $testcaseStorage = Services::get(ASTestcaseStorage::class, $this->db);

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
        $testStorage = Services::get(ASTestStorage::class, $this->db);
        $testcaseStorage = Services::get(ASTestcaseStorage::class, $this->db);

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