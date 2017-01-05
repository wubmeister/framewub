<?php

use PHPUnit\Framework\TestCase;

use Framewub\Rest\StorageAction;
use Framewub\Storage\Db\AbstractStorage;
use Framewub\Storage\StorageObject;
use Framewub\Db\MySQL;
use Framewub\Http\Message\ServerRequest;
use Framewub\Http\Message\Response;

class Rest_MockStorage extends AbstractStorage
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

class Rest_MockStorageObject extends StorageObject
{

}

class Rest_MockStorageAction extends StorageAction
{
    private $db;
    private $executedMethods = [];

    public function __construct($db)
    {
        $this->db = $db;
    }

    protected function getStorage()
    {
        $storage = new Rest_MockStorage($this->db);
        $storage->setTableName('tests');
        $storage->setObjectClass(Rest_MockStorageObject::class);
        return $storage;
    }

    public function getUsedStorage()
    {
        return $this->storage;
    }

    public function isExecuted($method)
    {
        return array_key_exists($method, $this->executedMethods);
    }

    public function getMethodArgs($method)
    {
        return array_key_exists($method, $this->executedMethods) ? $this->executedMethods[$method] : null;
    }

    public function __call($name, $args)
    {
        $this->executedMethods[$name] = $args;
        return true;
    }

    public function filterValues($values, $reason)
    {
        $this->executedMethods['filterValues'] = [ $values, $reason ];
        return $values;
    }
}

class StorageActionTest extends \PHPUnit_Extensions_Database_TestCase
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
        $action = new Rest_MockStorageAction($this->db);
        ob_start();
        $response = $action(new ServerRequest());
        ob_end_clean();
        $this->assertEquals(
            '[{"id":"1","name":"First test","created":"2012-12-10 16:30:00","modified":null,"recycled":null},{"id":"2","name":"Second test","created":"2014-12-10 16:30:00","modified":null,"recycled":null},{"id":"3","name":"Third test","created":"2016-12-10 16:30:00","modified":null,"recycled":null}]',
            $response->getBody()->getMockContents()
        );

        $this->assertTrue($action->isExecuted('buildIndexQuery'), 'The action should have called "buildIndexQuery", but it didn\'t');
        $this->assertTrue($action->isExecuted('postprocessIndex'), 'The action should have called "postprocessIndex", but it didn\'t');
    }

    public function testFindById()
    {
        $action = new Rest_MockStorageAction($this->db);
        ob_start();
        $response = $action(new ServerRequest([ 'id' => 2 ]));
        ob_end_clean();
        $this->assertEquals(
            '{"id":"2","name":"Second test","created":"2014-12-10 16:30:00","modified":null,"recycled":null}',
            $response->getBody()->getMockContents()
        );

        $this->assertTrue($action->isExecuted('buildDetailQuery'), 'The action should have called "buildDetailQuery", but it didn\'t');
        $this->assertTrue($action->isExecuted('postprocessDetail'), 'The action should have called "postprocessDetail", but it didn\'t');
    }

    public function testAdd()
    {
        $action = new Rest_MockStorageAction($this->db);
        $_POST['name'] = 'Fourth test';
        $_POST['created'] = date('Y-m-d H:i:s');

        $request = new ServerRequest();

        ob_start();
        $response = $action($request->withMethod('POST'));
        ob_end_clean();
        $json = $response->getBody()->getMockContents();
        $obj = json_decode($json, true);
        $this->assertEquals('Fourth test', $obj['name']);

        $result = $action->getUsedStorage()->findOne($obj['id']);
        $this->assertInstanceOf(StorageObject::class, $result);
        $this->assertEquals('Fourth test', $result->name);


        $this->assertTrue($action->isExecuted('filterValues'), 'The action should have called "filterValues", but it didn\'t');
        $this->assertEquals(StorageAction::INSERT, $action->getMethodArgs('filterValues')[1]);
        $this->assertTrue($action->isExecuted('postprocessInsert'), 'The action should have called "postprocessInsert", but it didn\'t');
    }

    public function testUpdate()
    {
        $action = new Rest_MockStorageAction($this->db);
        $_POST['name'] = 'Modified test';
        $_POST['modified'] = date('Y-m-d H:i:s');

        $request = new ServerRequest([ 'id' => 3 ]);

        ob_start();
        $response = $action($request->withMethod('PUT'));
        ob_end_clean();
        $json = $response->getBody()->getMockContents();
        $obj = json_decode($json, true);
        $this->assertEquals('Modified test', $obj['name']);

        $result = $action->getUsedStorage()->findOne(3);
        $this->assertInstanceOf(StorageObject::class, $result);
        $this->assertEquals('Modified test', $result->name);


        $this->assertTrue($action->isExecuted('filterValues'), 'The action should have called "filterValues", but it didn\'t');
        $this->assertEquals(StorageAction::UPDATE, $action->getMethodArgs('filterValues')[1]);
        $this->assertTrue($action->isExecuted('postprocessUpdate'), 'The action should have called "postprocessUpdate", but it didn\'t');
    }

    public function testDelete()
    {
        $action = new Rest_MockStorageAction($this->db);
        $request = new ServerRequest([ 'id' => 3 ]);

        ob_start();
        $response = $action($request->withMethod('DELETE'));
        ob_end_clean();
        $json = $response->getBody()->getMockContents();
        $obj = json_decode($json, true);
        $this->assertTrue($obj['success']);

        $result = $action->getUsedStorage()->findOne(3);
        $this->assertFalse($result);


        $this->assertTrue($action->isExecuted('beforeDelete'), 'The action should have called "beforeDelete", but it didn\'t');
        $this->assertTrue($action->isExecuted('postprocessDelete'), 'The action should have called "postprocessDelete", but it didn\'t');
    }
}