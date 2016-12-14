<?php

use PHPUnit\Framework\TestCase;

use Framewub\Db\MySQL;
use Framewub\Db\Query\Insert;
use Framewub\Db\Query\Func;

class UpdateTest extends TestCase
{
    protected $db;

    protected function setUp()
    {
        $this->db = new MySQL([ 'dbname' => 'framewub_test' ], 'framewub', 'fr4m3wu8');
    }

    public function testTable()
    {
        // Create object
        $insert = new Insert($this->db);

        // Add 'table' clause
        $result = $insert->into('foo');

        // Assert chaining
        $this->assertEquals($insert, $result, "Method 'into' should return the Insert object for chaining");
        // Assert the resulting query
        $this->assertEquals("INSERT INTO `foo`", (string)$insert);
    }

    public function testValues()
    {
        // Create object
        $insert = new Insert($this->db);

        // Add 'table' clause
        $result = $insert->into('foo')
            ->values([ 'lorem' => 'ipsum', 'dingen' => 'zaken' ]);

        // Assert chaining
        $this->assertEquals($insert, $result, "Method 'values' should return the Insert object for chaining");
        // Assert the resulting query
        $this->assertEquals("INSERT INTO `foo` (`lorem`, `dingen`) VALUES (:lorem, :dingen)", (string)$insert);
        // Assert bind params
        $bind = $insert->getBind();
        $this->assertArrayHasKey(':lorem', $bind);
        $this->assertArrayHasKey(':dingen', $bind);
        $this->assertEquals($bind[':lorem'], 'ipsum');
        $this->assertEquals($bind[':dingen'], 'zaken');
    }

    public function testIgnore()
    {
        // Create object
        $insert = new Insert($this->db);

        // Add 'table' clause
        $result = $insert->into('foo')
            ->values([ 'lorem' => 'ipsum', 'dingen' => 'zaken' ])
            ->ignore();

        // Assert chaining
        $this->assertEquals($insert, $result, "Method 'ignore' should return the Insert object for chaining");
        // Assert the resulting query
        $this->assertEquals("INSERT IGNORE INTO `foo` (`lorem`, `dingen`) VALUES (:lorem, :dingen)", (string)$insert);

        // Cancel ignoring
        $insert->ignore(false);
        // Assert the resulting query
        $this->assertEquals("INSERT INTO `foo` (`lorem`, `dingen`) VALUES (:lorem, :dingen)", (string)$insert);


        // Alternative method to set the ignore flag
        $result = $insert->into('foo')
            ->values([ 'lorem' => 'ipsum', 'dingen' => 'zaken' ])
            ->onDuplicateKey(Insert::IGNORE);
        // Assert the resulting query
        $this->assertEquals("INSERT IGNORE INTO `foo` (`lorem`, `dingen`) VALUES (:lorem, :dingen)", (string)$insert);
    }

    public function testOnDuplicateKeyUpdate()
    {
        // Create object
        $insert = new Insert($this->db);

        // Add 'table' clause
        $result = $insert->into('foo')
            ->values([ 'lorem' => 'ipsum', 'dingen' => 'zaken' ])
            ->onDuplicateKey(Insert::UPDATE);
        // Assert chaining
        $this->assertEquals($insert, $result, "Method 'onDuplicateKey' should return the Insert object for chaining");
        // Assert the resulting query
        $this->assertEquals("INSERT INTO `foo` (`lorem`, `dingen`) VALUES (:lorem, :dingen) ON DUPLICATE KEY UPDATE `lorem` = :lorem, `dingen` = :dingen", (string)$insert);

        // Add 'table' clause
        $result = $insert->into('foo')
            ->values([ 'lorem' => 'ipsum', 'dingen' => 'zaken' ])
            ->onDuplicateKey(Insert::UPDATE, [ 'dingen' ]);
        // Assert chaining
        $this->assertEquals($insert, $result, "Method 'onDuplicateKey' should return the Insert object for chaining");
        // Assert the resulting query
        $this->assertEquals("INSERT INTO `foo` (`lorem`, `dingen`) VALUES (:lorem, :dingen) ON DUPLICATE KEY UPDATE `dingen` = :dingen", (string)$insert);
    }
}