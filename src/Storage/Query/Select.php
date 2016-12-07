<?php

/**
 * Helper to construct SELECT queries for SQL
 *
 * @package    framewub/storage
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    http://mit-license.org/
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Storage\Query;

/**
 * Select query builder
 */
class Select
{
    /**
     * The table
     *
     * @var string
     */
    protected $table;

    /**
     * The columns
     *
     * @var array
     */
    protected $columns = [];

    /**
     * The 'where' clause
     *
     * @var string
     */
    protected $whereClause = '';

    /**
     * The current bind paramter index. Will be increased by one each time a named bind paramter is added
     *
     * @var int
     */
    protected $bindIndex = 0;

    /**
     * The bind paramters
     *
     * @var array
     */
    protected $bind = [];

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
    public function from($table, $columns = '*')
    {
        if (is_array($table)) {
            foreach ($table as $alias => $name) {
                $this->table = "`{$name}` AS `{$alias}`";
                break;
            }
        } else {
            $this->table = "`{$table}`";
            $alias = $table;
        }

        if ($columns) {
            if (!is_array($columns)) {
                $columns = [ $columns ];
            }

            $this->columns[$alias] = [];

            foreach ($columns as $key => $column) {
                if ($column == '*') {
                    $this->columns[$alias][] = "`{$alias}`.*";
                } else {
                    $this->columns[$alias][] = "`{$alias}`.`{$column}`" . (!is_numeric($key) ? " AS `{$key}`" : "");
                }
            }
        }

        return $this;
    }

    protected function whereStr(&$conditions, $glue = 'AND')
    {
        $str = '';
        foreach ($conditions as $key => $value) {
            if ($str) {
                $str .= " {$glue} ";
            }

            $this->bindIndex++;
            $operator = '=';
            $operand = ':bind' . $this->bindIndex;

            if (is_array($value) && !array_key_exists(0, $value)) {
                $k = array_keys($value)[0];
                $bindValue = $value[$k];

                switch ($k) {
                    case '$gt':
                        $operator = '>';
                        break;
                }
            } else {
                $bindValue = $value;
            }

            $str .= '`' . str_replace('.', '`.`', $key) . "` {$operator} {$operand}";
            $this->bind[':bind'.$this->bindIndex] = $bindValue;
        }

        return $str;
    }

    /**
     * Adds one or more conditions to the 'where' clause
     *
     * @param array $conditions
     *   The conditions to add
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function where($conditions)
    {
        if ($this->whereClause) {
            $this->whereClause .= " AND ";
        }
        $this->whereClause .= $this->whereStr($conditions);

        return $this;
    }

    /**
     * Returns the bind parameters
     *
     * @return array
     *   The bind parameters ([ ':bindX' => $value ])
     */
    public function getBind()
    {
        return $this->bind;
    }

    /**
     * ToString function just returns the expression as-is
     *
     * @return string
     *   The literal expression
     */
    public function __toString()
    {
        $columns = [];
        foreach ($this->columns as $cols) {
            foreach ($cols as $col) {
                $columns[] = $col;
            }
        }

        $sql = "SELECT " . implode(', ', $columns) . " FROM {$this->table}";
        if ($this->whereClause) {
            $sql .= " WHERE {$this->whereClause}";
        }

        return $sql;
    }
}
