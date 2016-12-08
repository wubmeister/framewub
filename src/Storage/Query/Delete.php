<?php

/**
 * Helper to construct DELETE queries for SQL
 *
 * @package    framewub/storage
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Storage\Query;

/**
 * Delete query builder
 */
class Delete extends AbstractQuery
{
    /**
     * Sets the table for this query
     *
     * @param string|array $table
     *   Either a table name or an associative array with one element. In case of an array, the element's key should be the alias and the value the real table name.
     * @param string|array $columns
     *   OPTIONAL. The column(s) to select from the table. Defaults to '*'.
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function from($table)
    {
        $this->tableName = $this->tableStr($table, []);

        return $this;
    }

    /**
     * ToString function returns the DELETE query
     *
     * @return string
     *   The literal expression
     */
    public function __toString()
    {
        $sql = "DELETE FROM {$this->tableName}";
        if ($this->whereClause) {
            $sql .= " WHERE {$this->whereClause}";
        }

        return $sql;
    }
}