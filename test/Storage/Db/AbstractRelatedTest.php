<?php

use PHPUnit\Framework\TestCase;

use Framewub\Storage\Db\AbstractStorage;
use Framewub\Storage\Db\AbstractRelated;
use Framewub\Storage\Db\Rowset;
use Framewub\Storage\StorageObject;
use Framewub\Db\MySQL;

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
        'tests' => [ 'type' => self::MANY_TO_ONE, 'storage' => 'ARTestStorage', 'fkToOther' => 'test_id' ]
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
        $storage = new ARTestcaseStorage($this->db);
        $testcases = $storage->findByTest(1);

        $this->assertInstanceOf(Rowset::class, $testcases);

        $tc = $testcases->fetchOne();
        $this->assertInstanceOf(StorageObject::class, $tc);
        $this->assertEquals('First test, first testcase', $tc->name);

        $storage = new ARTestStorage($this->db);
        $tests = $storage->findByTestcase(1);

        $this->assertInstanceOf(Rowset::class, $tests);

        $test = $tests->fetchOne();
        $this->assertInstanceOf(StorageObject::class, $test);
        $this->assertEquals('First test', $test->name);
    }

    public function testFindRelated()
    {
        $storage = new ARTestStorage($this->db);
        $testcases = $storage->findTestcases(1);

        $this->assertInstanceOf(Rowset::class, $testcases);

        $tc = $testcases->fetchOne();
        $this->assertInstanceOf(StorageObject::class, $tc);
        $this->assertEquals('First test, first testcase', $tc->name);

        $storage = new ARTestcaseStorage($this->db);
        $tests = $storage->findTest(1);

        $this->assertInstanceOf(Rowset::class, $tests);

        $test = $tests->fetchOne();
        $this->assertInstanceOf(StorageObject::class, $test);
        $this->assertEquals('First test', $test->name);
    }
}
