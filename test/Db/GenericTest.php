<?php

use PHPUnit\Framework\TestCase;

use Framewub\Db\Generic;

class GenericTest extends TestCase
{
    protected $db;

    protected function setUp()
    {
        $this->db = new Generic([ 'dbname' => 'framewub_test' ], 'framewub', 'fr4m3wu8');
    }

    public function testQuoteIdentifier()
    {
        $this->assertEquals('"test"', $this->db->quoteIdentifier('test'));
        $this->assertEquals('"foo"."bar"', $this->db->quoteIdentifier('foo.bar'));
        $this->assertEquals('"foo".*', $this->db->quoteIdentifier('foo.*'));
    }
}
