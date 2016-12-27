<?php

use PHPUnit\Framework\TestCase;

use Framewub\Auth\Db as DbAuth;
use Framewub\Db\MySQL;

class Db extends \PHPUnit_Extensions_Database_TestCase
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
        return $this->createFlatXMLDataSet(dirname(__DIR__).'/data/user-seed.xml');
    }

    public function testAuthenticate()
    {
        $auth = new DbAuth($this->db, 'users');

        $auth->authenticate([ 'username' => 'dummy', 'password' => 'dummy' ]);
        $this->assertFalse($auth->hasIdentity());
        $this->assertEquals(DbAuth::ERR_USER_NOT_FOUND, $auth->getError());

        $auth->authenticate([ 'username' => 'testuser', 'password' => 'dummy' ]);
        $this->assertFalse($auth->hasIdentity());
        $this->assertEquals(DbAuth::ERR_INCORRECT_PASSWORD, $auth->getError());

        $auth->authenticate([ 'username' => 'testuser', 'password' => 'supersecretpassword' ]);
        $this->assertTrue($auth->hasIdentity());
        $this->assertEquals(DbAuth::ERR_OK, $auth->getError());
    }
}
