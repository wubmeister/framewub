<?php

use PHPUnit\Framework\TestCase;

use Framewub\Rest\StorageController;
use Framewub\Storage\Db\AbstractStorage;
use Framewub\Storage\StorageObject;
use Framewub\Db\MySQL;
use Framewub\Http\Message\ServerRequest;
use Framewub\Http\Message\Response;

class SCMockStorage extends AbstractStorage
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

class SCMockStorageObject extends StorageObject
{

}

class SCMockStorageController extends StorageController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    protected function getStorage()
    {
        $storage = new SCMockStorage($this->db);
        $storage->setTableName('tests');
        $storage->setObjectClass(SCMockStorageObject::class);
        return $storage;
    }

    public function getUsedStorage()
    {
        return $this->storage;
    }
}

class StorageControllerTest extends \PHPUnit_Extensions_Database_TestCase
{
    private $sharedPdo;
    private $db;

    public function __construct()
    {
        $_SERVER['REQUEST_URI'] = '/';
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
        return $this->createFlatXMLDataSet(dirname(__DIR__).'/data/test-seed.xml');
    }

    public function testFindAll()
    {
        $controller = new SCMockStorageController($this->db);
        ob_start();
        $response = $controller(new ServerRequest());
        ob_end_clean();
        $this->assertEquals(
            '[{"id":"1","name":"First test","created":"2012-12-10 16:30:00","modified":null,"recycled":null},{"id":"2","name":"Second test","created":"2014-12-10 16:30:00","modified":null,"recycled":null},{"id":"3","name":"Third test","created":"2016-12-10 16:30:00","modified":null,"recycled":null}]',
            $response->getBody()->getMockContents()
        );
    }

    public function testFindById()
    {
        $controller = new SCMockStorageController($this->db);
        ob_start();
        $response = $controller(new ServerRequest([ 'id' => 2 ]));
        ob_end_clean();
        $this->assertEquals(
            '{"id":"2","name":"Second test","created":"2014-12-10 16:30:00","modified":null,"recycled":null}',
            $response->getBody()->getMockContents()
        );
    }

    public function testAdd()
    {
        $controller = new SCMockStorageController($this->db);
        $_POST['name'] = 'Fourth test';
        $_POST['created'] = date('Y-m-d H:i:s');

        $request = new ServerRequest();

        ob_start();
        $response = $controller($request->withMethod('POST'));
        ob_end_clean();
        $json = $response->getBody()->getMockContents();
        $obj = json_decode($json, true);
        $this->assertEquals('Fourth test', $obj['name']);

        $result = $controller->getUsedStorage()->findOne($obj['id']);
        $this->assertInstanceOf(StorageObject::class, $result);
        $this->assertEquals('Fourth test', $result->name);
    }

    public function testUpdate()
    {
        $controller = new SCMockStorageController($this->db);
        $_POST['name'] = 'Modified test';
        $_POST['modified'] = date('Y-m-d H:i:s');

        $request = new ServerRequest([ 'id' => 3 ]);

        ob_start();
        $response = $controller($request->withMethod('PUT'));
        ob_end_clean();
        $json = $response->getBody()->getMockContents();
        $obj = json_decode($json, true);
        $this->assertEquals('Modified test', $obj['name']);

        $result = $controller->getUsedStorage()->findOne(3);
        $this->assertInstanceOf(StorageObject::class, $result);
        $this->assertEquals('Modified test', $result->name);
    }

    public function testDelete()
    {
        $controller = new SCMockStorageController($this->db);
        $request = new ServerRequest([ 'id' => 3 ]);

        ob_start();
        $response = $controller($request->withMethod('DELETE'));
        ob_end_clean();
        $json = $response->getBody()->getMockContents();
        $obj = json_decode($json, true);
        $this->assertTrue($obj['success']);

        $result = $controller->getUsedStorage()->findOne(3);
        $this->assertFalse($result);
    }
}