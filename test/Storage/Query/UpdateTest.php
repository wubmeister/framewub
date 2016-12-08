<?php

use PHPUnit\Framework\TestCase;

use Framewub\Storage\Query\Update;
use Framewub\Storage\Query\Func;

class UpdateTest extends TestCase
{
    public function testTable()
    {
        // Create object
        $update = new Update();

        // Add 'table' clause
        $result = $update->table('foo');

        // Assert chaining
        $this->assertEquals($update, $result, "Method 'table' should return the Update object for chaining");
        // Assert the resulting query
        $this->assertEquals("UPDATE `foo`", (string)$update);
    }

    public function testValues()
    {
        // Create object
        $update = new Update();

        // Add 'table' clause
        $result = $update->table('foo')
            ->where([ 'id' => 42 ])
            ->values([ 'lorem' => 'ipsum', 'dingen' => 'zaken' ]);

        // Assert chaining
        $this->assertEquals($update, $result, "Method 'values' should return the Update object for chaining");
        // Assert the resulting query
        $this->assertEquals("UPDATE `foo` SET `lorem` = :lorem, `dingen` = :dingen WHERE (`id` = :bind1)", (string)$update);
        // Assert bind params
        $bind = $update->getBind();
        $this->assertArrayHasKey(':bind1', $bind);
        $this->assertArrayHasKey(':lorem', $bind);
        $this->assertArrayHasKey(':dingen', $bind);
        $this->assertEquals($bind[':bind1'], 42);
        $this->assertEquals($bind[':lorem'], 'ipsum');
        $this->assertEquals($bind[':dingen'], 'zaken');
    }

    public function testWhere()
    {
        // Create object
        $update = new Update();

        // Add 'from' clause
        $update->table('foo');

        // Test simple comparison
        $result = $update->where([ 'id' => 1 ]);
        // Assert chaining
        $this->assertEquals($update, $result, "Method 'where' should return the Update object for chaining");
        // Assert the resulting query
        $this->assertEquals("UPDATE `foo` WHERE (`id` = :bind1)", (string)$update);
        // Assert bind params
        $bind = $update->getBind();
        $this->assertArrayHasKey(':bind1', $bind);
        $this->assertEquals($bind[':bind1'], 1);

        // Test 'OR'
        $update = new Update();
        $update->table('foo')
            ->where([ 'id' => 1 ])
            ->orWhere([ 'id' => 3, 'foo' => 'bar' ]);
        $this->assertEquals("UPDATE `foo` WHERE (`id` = :bind1) OR (`id` = :bind2) OR (`foo` = :bind3)", (string)$update);

        // Test greater than
        $update = new Update();
        $update->table('foo')
            ->where([ 'id' => [ '$gt' => 3 ] ]);
        $this->assertEquals("UPDATE `foo` WHERE (`id` > :bind1)", (string)$update);

        // Test greater than or equal
        $update = new Update();
        $update->table('foo')
            ->where([ 'id' => [ '$gte' => 3 ] ]);
        $this->assertEquals("UPDATE `foo` WHERE (`id` >= :bind1)", (string)$update);

        // Test less than
        $update = new Update();
        $update->table('foo')
            ->where([ 'id' => [ '$lt' => 3 ] ]);
        $this->assertEquals("UPDATE `foo` WHERE (`id` < :bind1)", (string)$update);

        // Test less than or equal
        $update = new Update();
        $update->table('foo')
            ->where([ 'id' => [ '$lte' => 3 ] ]);
        $this->assertEquals("UPDATE `foo` WHERE (`id` <= :bind1)", (string)$update);

        // Test less not equal
        $update = new Update();
        $update->table('foo')
            ->where([ 'id' => [ '$ne' => 3 ] ]);
        $this->assertEquals("UPDATE `foo` WHERE (`id` <> :bind1)", (string)$update);

        // Test IS NULL
        $update = new Update();
        $update->table('foo')
            ->where([ 'id' => null ]);
        $this->assertEquals("UPDATE `foo` WHERE (`id` IS NULL)", (string)$update);

        // Test IS NOT NULL
        $update = new Update();
        $update->table('foo')
            ->where([ 'id' => [ '$ne' => null ] ]);
        $this->assertEquals("UPDATE `foo` WHERE (`id` IS NOT NULL)", (string)$update);

        // Test literal expression
        $update = new Update();
        $update->table('foo')
            ->where([ 'created' => new Func("NOW()") ]);
        $this->assertEquals("UPDATE `foo` WHERE (`created` = NOW())", (string)$update);

        // Test between
        $update = new Update();
        $update->table('foo')
            ->where([ 'id' => [ '$between' => [ 3, 10 ] ] ]);
        $this->assertEquals("UPDATE `foo` WHERE (`id` BETWEEN :bind1 AND :bind2)", (string)$update);
        // Assert bind params
        $bind = $update->getBind();
        $this->assertArrayHasKey(':bind1', $bind);
        $this->assertArrayHasKey(':bind2', $bind);
        $this->assertEquals($bind[':bind1'], 3);
        $this->assertEquals($bind[':bind2'], 10);
    }

    public function testNestedWhere()
    {
        // Create object
        $update = new Update();

        // Add 'from' clause
        $update->table('foo');

        // Test simple comparison
        $result = $update->where([ 'id' => 1, '$or' => [ 'foo' => 'bar', 'dingen' => 'zaken' ] ]);
        // Assert the resulting query
        $this->assertEquals("UPDATE `foo` WHERE (`id` = :bind1) OR ((`foo` = :bind2) AND (`dingen` = :bind3))", (string)$update);
        // Assert bind params
        $bind = $update->getBind();
        $this->assertArrayHasKey(':bind1', $bind);
        $this->assertArrayHasKey(':bind2', $bind);
        $this->assertArrayHasKey(':bind3', $bind);
        $this->assertEquals($bind[':bind1'], 1);
        $this->assertEquals($bind[':bind2'], 'bar');
        $this->assertEquals($bind[':bind3'], 'zaken');
    }
}