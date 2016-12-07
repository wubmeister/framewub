<?php

use PHPUnit\Framework\TestCase;

use Framewub\Storage\Query\Select;

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
        $this->assertEquals($select, $result, "Method 'from' should return the Select object for chaining");
        // Assert the resulting query
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE `id` = :bind1", (string)$select);
        // Assert bind params
        $bind = $select->getBind();
        $this->assertArrayHasKey(':bind1', $bind);
        $this->assertEquals($bind[':bind1'], 1);

        // Test greater than
        $select = new Select();
        $select->from('foo')
            ->where([ 'id' => [ '$gt' => 3 ] ]);
        $this->assertEquals("SELECT `foo`.* FROM `foo` WHERE `id` > :bind1", (string)$select);
    }
}
