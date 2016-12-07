<?php

use PHPUnit\Framework\TestCase;

use Framewub\Storage\Query\Select;
use Framewub\Storage\Query\Func;

class SelectTest extends TestCase
{
    public function testFrom()
    {
        // Create object
        $select = new Select();

        // Add 'from' clause
        $result = $select->from('foo');

        // Assert chaining
        $this->assertEquals($select, $result, "Method 'from' should return the Select object for chaining");
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.* FROM `foo`", (string)$select);

        // Test alias
        $select = new Select();
        // Add 'from' clause
        $select->from([ 'f' => 'foo' ]);
        // Assert the resulting query
        $this->assertEquals("SELECT `f`.* FROM `foo` AS `f`", (string)$select);
    }

    public function testJoin()
    {
        // Create object
        $select = new Select();

        // Add 'from' clause
        $result = $select->from('foo')
            ->join('bar', 'bar.id = foo.bar_id');

        // Assert chaining
        $this->assertEquals($select, $result, "Method 'from' should return the Select object for chaining");
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.*, `bar`.* FROM `foo` JOIN `bar` ON `bar`.`id` = `foo`.`bar_id`", (string)$select);

        // Test join with alias
        $select = new Select();
        $result = $select->from('foo')
            ->join([ 'b' => 'bar' ], 'b.id = foo.bar_id');
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.*, `b`.* FROM `foo` JOIN `bar` AS `b` ON `b`.`id` = `foo`.`bar_id`", (string)$select);

        // Test left join
        $select = new Select();
        $result = $select->from('foo')
            ->joinLeft('bar', 'bar.id = foo.bar_id');
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.*, `bar`.* FROM `foo` LEFT JOIN `bar` ON `bar`.`id` = `foo`.`bar_id`", (string)$select);

        // Test left join with alias
        $select = new Select();
        $result = $select->from('foo')
            ->joinLeft([ 'b' => 'bar' ], 'b.id = foo.bar_id');
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.*, `b`.* FROM `foo` LEFT JOIN `bar` AS `b` ON `b`.`id` = `foo`.`bar_id`", (string)$select);

        // Test right join
        $select = new Select();
        $result = $select->from('foo')
            ->joinRight('bar', 'bar.id = foo.bar_id');
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.*, `bar`.* FROM `foo` RIGHT JOIN `bar` ON `bar`.`id` = `foo`.`bar_id`", (string)$select);

        // Test right join with alias
        $select = new Select();
        $result = $select->from('foo')
            ->joinRight([ 'b' => 'bar' ], 'b.id = foo.bar_id');
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.*, `b`.* FROM `foo` RIGHT JOIN `bar` AS `b` ON `b`.`id` = `foo`.`bar_id`", (string)$select);
    }

    public function testGroup()
    {
        // Create object
        $select = new Select();

        // Add 'from' clause
        $result = $select->from('foo')
            ->group('bar');
        // Assert chaining
        $this->assertEquals($select, $result, "Method 'group' should return the Select object for chaining");
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.* FROM `foo` GROUP BY `bar`", (string)$select);

        // Test multiple groups
        $select = new Select();
        $select->from('foo')
            ->group([ 'bar', 'lorem' ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` GROUP BY `bar`, `lorem`", (string)$select);
    }

    public function testOrder()
    {
        // Create object
        $select = new Select();

        // Add 'from' clause
        $result = $select->from('foo')
            ->order('bar');
        // Assert chaining
        $this->assertEquals($select, $result, "Method 'order' should return the Select object for chaining");
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.* FROM `foo` ORDER BY `bar`", (string)$select);

        // Test multiple groups
        $select = new Select();
        $select->from('foo')
            ->order([ 'bar', 'lorem' ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` ORDER BY `bar`, `lorem`", (string)$select);
    }

    public function testOffsetAndLimit()
    {
        // Create object
        $select = new Select();
        // Add 'offset' clause
        $result = $select->from('foo')
            ->offset(10);
        // Assert chaining
        $this->assertEquals($select, $result, "Method 'offset' should return the Select object for chaining");
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.* FROM `foo` LIMIT 10, 18446744073709551615", (string)$select);

        // Create object
        $select = new Select();
        // Add 'limit' clause
        $result = $select->from('foo')
            ->limit(10);
        // Assert chaining
        $this->assertEquals($select, $result, "Method 'limit' should return the Select object for chaining");
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.* FROM `foo` LIMIT 10", (string)$select);

        // Create object
        $select = new Select();
        // Add 'limit' clause
        $select->from('foo')
            ->limit(42)->offset(10);
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.* FROM `foo` LIMIT 10, 42", (string)$select);
    }

    public function testFromWithColumns()
    {
        // Create object
        $select = new Select();

        // Test with single column
        $result = $select->from('foo', 'column');
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.`column` FROM `foo`", (string)$select);

        // Test with multiple columns
        $result = $select->from('foo', [ 'column1', 'column2' ]);
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.`column1`, `foo`.`column2` FROM `foo`", (string)$select);

        // Test with multiple columns, of which one has an alias
        $result = $select->from('foo', [ 'column1', 'c2' => 'column2' ]);
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.`column1`, `foo`.`column2` AS `c2` FROM `foo`", (string)$select);
    }

    public function testWhere()
    {
        // Create object
        $select = new Select();

        // Add 'from' clause
        $select->from('foo');

        // Test simple comparison
        $result = $select->where([ 'id' => 1 ]);
        // Assert chaining
        $this->assertEquals($select, $result, "Method 'where' should return the Select object for chaining");
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`id` = :bind1)", (string)$select);
        // Assert bind params
        $bind = $select->getBind();
        $this->assertArrayHasKey(':bind1', $bind);
        $this->assertEquals($bind[':bind1'], 1);

        // Test 'OR'
        $select = new Select();
        $select->from('foo')
            ->where([ 'id' => 1 ])
            ->orWhere([ 'id' => 3, 'foo' => 'bar' ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`id` = :bind1) OR (`id` = :bind2) OR (`foo` = :bind3)", (string)$select);

        // Test greater than
        $select = new Select();
        $select->from('foo')
            ->where([ 'id' => [ '$gt' => 3 ] ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`id` > :bind1)", (string)$select);

        // Test greater than or equal
        $select = new Select();
        $select->from('foo')
            ->where([ 'id' => [ '$gte' => 3 ] ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`id` >= :bind1)", (string)$select);

        // Test less than
        $select = new Select();
        $select->from('foo')
            ->where([ 'id' => [ '$lt' => 3 ] ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`id` < :bind1)", (string)$select);

        // Test less than or equal
        $select = new Select();
        $select->from('foo')
            ->where([ 'id' => [ '$lte' => 3 ] ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`id` <= :bind1)", (string)$select);

        // Test less not equal
        $select = new Select();
        $select->from('foo')
            ->where([ 'id' => [ '$ne' => 3 ] ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`id` <> :bind1)", (string)$select);

        // Test IS NULL
        $select = new Select();
        $select->from('foo')
            ->where([ 'id' => null ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`id` IS NULL)", (string)$select);

        // Test IS NOT NULL
        $select = new Select();
        $select->from('foo')
            ->where([ 'id' => [ '$ne' => null ] ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`id` IS NOT NULL)", (string)$select);

        // Test literal expression
        $select = new Select();
        $select->from('foo')
            ->where([ 'created' => new Func("NOW()") ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`created` = NOW())", (string)$select);

        // Test between
        $select = new Select();
        $select->from('foo')
            ->where([ 'id' => [ '$between' => [ 3, 10 ] ] ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`id` BETWEEN :bind1 AND :bind2)", (string)$select);
        // Assert bind params
        $bind = $select->getBind();
        $this->assertArrayHasKey(':bind1', $bind);
        $this->assertArrayHasKey(':bind2', $bind);
        $this->assertEquals($bind[':bind1'], 3);
        $this->assertEquals($bind[':bind2'], 10);
    }

    public function testNestedWhere()
    {
        // Create object
        $select = new Select();

        // Add 'from' clause
        $select->from('foo');

        // Test simple comparison
        $result = $select->where([ 'id' => 1, '$or' => [ 'foo' => 'bar', 'dingen' => 'zaken' ] ]);
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`id` = :bind1) OR ((`foo` = :bind2) AND (`dingen` = :bind3))", (string)$select);
        // Assert bind params
        $bind = $select->getBind();
        $this->assertArrayHasKey(':bind1', $bind);
        $this->assertArrayHasKey(':bind2', $bind);
        $this->assertArrayHasKey(':bind3', $bind);
        $this->assertEquals($bind[':bind1'], 1);
        $this->assertEquals($bind[':bind2'], 'bar');
        $this->assertEquals($bind[':bind3'], 'zaken');
    }

    public function testHaving()
    {
        // Create object
        $select = new Select();

        // Add 'from' clause
        $select->from('foo')->where([ 'lorem' => 'ipsum' ]);

        // Test simple comparison
        $result = $select->having([ 'id' => 1 ]);
        // Assert chaining
        $this->assertEquals($select, $result, "Method 'having' should return the Select object for chaining");
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`lorem` = :bind1) HAVING (`id` = :bind2)", (string)$select);
        // Assert bind params
        $bind = $select->getBind();
        $this->assertArrayHasKey(':bind2', $bind);
        $this->assertEquals($bind[':bind2'], 1);

        // Test 'OR'
        $select = new Select();
        $select->from('foo')->where([ 'lorem' => 'ipsum' ])
            ->having([ 'id' => 1 ])
            ->orHaving([ 'id' => 3, 'foo' => 'bar' ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`lorem` = :bind1) HAVING (`id` = :bind2) OR (`id` = :bind3) OR (`foo` = :bind4)", (string)$select);

        // Test greater than
        $select = new Select();
        $select->from('foo')->where([ 'lorem' => 'ipsum' ])
            ->having([ 'id' => [ '$gt' => 3 ] ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`lorem` = :bind1) HAVING (`id` > :bind2)", (string)$select);

        // Test greater than or equal
        $select = new Select();
        $select->from('foo')->where([ 'lorem' => 'ipsum' ])
            ->having([ 'id' => [ '$gte' => 3 ] ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`lorem` = :bind1) HAVING (`id` >= :bind2)", (string)$select);

        // Test less than
        $select = new Select();
        $select->from('foo')->where([ 'lorem' => 'ipsum' ])
            ->having([ 'id' => [ '$lt' => 3 ] ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`lorem` = :bind1) HAVING (`id` < :bind2)", (string)$select);

        // Test less than or equal
        $select = new Select();
        $select->from('foo')->where([ 'lorem' => 'ipsum' ])
            ->having([ 'id' => [ '$lte' => 3 ] ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`lorem` = :bind1) HAVING (`id` <= :bind2)", (string)$select);

        // Test less not equal
        $select = new Select();
        $select->from('foo')->where([ 'lorem' => 'ipsum' ])
            ->having([ 'id' => [ '$ne' => 3 ] ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`lorem` = :bind1) HAVING (`id` <> :bind2)", (string)$select);

        // Test IS NULL
        $select = new Select();
        $select->from('foo')->where([ 'lorem' => 'ipsum' ])
            ->having([ 'id' => null ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`lorem` = :bind1) HAVING (`id` IS NULL)", (string)$select);

        // Test between
        $select = new Select();
        $select->from('foo')->where([ 'lorem' => 'ipsum' ])
            ->having([ 'id' => [ '$between' => [ 3, 10 ] ] ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE (`lorem` = :bind1) HAVING (`id` BETWEEN :bind2 AND :bind3)", (string)$select);
        // Assert bind params
        $bind = $select->getBind();
        $this->assertArrayHasKey(':bind2', $bind);
        $this->assertArrayHasKey(':bind3', $bind);
        $this->assertEquals($bind[':bind2'], 3);
        $this->assertEquals($bind[':bind3'], 10);
    }
}
