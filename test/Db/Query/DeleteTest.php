<?php

use PHPUnit\Framework\TestCase;

use Framewub\Db\MySQL;
use Framewub\Db\Query\Delete;
use Framewub\Db\Query\Func;

class DeleteTest extends TestCase
{
    protected $db;

    protected function setUp()
    {
        $this->db = new MySQL([ 'dbname' => 'framewub_test' ], 'framewub', 'fr4m3wu8');
    }

    public function testTable()
    {
        // Create object
        $delete = new Delete($this->db);

        // Add 'table' clause
        $result = $delete->from('foo');

        // Assert chaining
        $this->assertEquals($delete, $result, "Method 'from' should return the Delete object for chaining");
        // Assert the resulting query
        $this->assertEquals("DELETE FROM `foo`", (string)$delete);
    }

    public function testWhere()
    {
        // Create object
        $delete = new Delete($this->db);

        // Add 'from' clause
        $delete->from('foo');

        // Test simple comparison
        $result = $delete->where([ 'id' => 1 ]);
        // Assert chaining
        $this->assertEquals($delete, $result, "Method 'where' should return the Delete object for chaining");
        // Assert the resulting query
        $this->assertEquals("DELETE FROM `foo` WHERE (`id` = :bind1)", (string)$delete);
        // Assert bind params
        $bind = $delete->getBind();
        $this->assertArrayHasKey(':bind1', $bind);
        $this->assertEquals($bind[':bind1'], 1);

        // Test 'OR'
        $delete = new Delete($this->db);
        $delete->from('foo')
            ->where([ 'id' => 1 ])
            ->orWhere([ 'id' => 3, 'foo' => 'bar' ]);
        $this->assertEquals("DELETE FROM `foo` WHERE (`id` = :bind1) OR (`id` = :bind2) OR (`foo` = :bind3)", (string)$delete);

        // Test greater than
        $delete = new Delete($this->db);
        $delete->from('foo')
            ->where([ 'id' => [ '$gt' => 3 ] ]);
        $this->assertEquals("DELETE FROM `foo` WHERE (`id` > :bind1)", (string)$delete);

        // Test greater than or equal
        $delete = new Delete($this->db);
        $delete->from('foo')
            ->where([ 'id' => [ '$gte' => 3 ] ]);
        $this->assertEquals("DELETE FROM `foo` WHERE (`id` >= :bind1)", (string)$delete);

        // Test less than
        $delete = new Delete($this->db);
        $delete->from('foo')
            ->where([ 'id' => [ '$lt' => 3 ] ]);
        $this->assertEquals("DELETE FROM `foo` WHERE (`id` < :bind1)", (string)$delete);

        // Test less than or equal
        $delete = new Delete($this->db);
        $delete->from('foo')
            ->where([ 'id' => [ '$lte' => 3 ] ]);
        $this->assertEquals("DELETE FROM `foo` WHERE (`id` <= :bind1)", (string)$delete);

        // Test less not equal
        $delete = new Delete($this->db);
        $delete->from('foo')
            ->where([ 'id' => [ '$ne' => 3 ] ]);
        $this->assertEquals("DELETE FROM `foo` WHERE (`id` <> :bind1)", (string)$delete);

        // Test IS NULL
        $delete = new Delete($this->db);
        $delete->from('foo')
            ->where([ 'id' => null ]);
        $this->assertEquals("DELETE FROM `foo` WHERE (`id` IS NULL)", (string)$delete);

        // Test IS NOT NULL
        $delete = new Delete($this->db);
        $delete->from('foo')
            ->where([ 'id' => [ '$ne' => null ] ]);
        $this->assertEquals("DELETE FROM `foo` WHERE (`id` IS NOT NULL)", (string)$delete);

        // Test literal expression
        $delete = new Delete($this->db);
        $delete->from('foo')
            ->where([ 'created' => new Func("NOW()") ]);
        $this->assertEquals("DELETE FROM `foo` WHERE (`created` = NOW())", (string)$delete);

        // Test between
        $delete = new Delete($this->db);
        $delete->from('foo')
            ->where([ 'id' => [ '$between' => [ 3, 10 ] ] ]);
        $this->assertEquals("DELETE FROM `foo` WHERE (`id` BETWEEN :bind1 AND :bind2)", (string)$delete);
        // Assert bind params
        $bind = $delete->getBind();
        $this->assertArrayHasKey(':bind1', $bind);
        $this->assertArrayHasKey(':bind2', $bind);
        $this->assertEquals($bind[':bind1'], 3);
        $this->assertEquals($bind[':bind2'], 10);
    }

    public function testNestedWhere()
    {
        // Create object
        $delete = new Delete($this->db);

        // Add 'from' clause
        $delete->from('foo');

        // Test simple comparison
        $result = $delete->where([ 'id' => 1, '$or' => [ 'foo' => 'bar', 'dingen' => 'zaken' ] ]);
        // Assert the resulting query
        $this->assertEquals("DELETE FROM `foo` WHERE (`id` = :bind1) OR ((`foo` = :bind2) AND (`dingen` = :bind3))", (string)$delete);
        // Assert bind params
        $bind = $delete->getBind();
        $this->assertArrayHasKey(':bind1', $bind);
        $this->assertArrayHasKey(':bind2', $bind);
        $this->assertArrayHasKey(':bind3', $bind);
        $this->assertEquals($bind[':bind1'], 1);
        $this->assertEquals($bind[':bind2'], 'bar');
        $this->assertEquals($bind[':bind3'], 'zaken');
    }
}