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
class Select extends AbstractQuery
{
    /**
     * The joins
     *
     * @var array
     */
    protected $joins = [];

    /**
     * The columns
     *
     * @var array
     */
    protected $columns = [];

    /**
     * The 'group by' clause
     *
     * @var string
     */
    protected $groupBy = '';

    /**
     * The 'order by' clause
     *
     * @var string
     */
    protected $orderBy = '';

    /**
     * The limit's offset
     *
     * @var int
     */
    protected $offsetBy = 0;

    /**
     * The limit
     *
     * @var int
     */
    protected $limitBy = 0;

    /**
     * The 'having' clause
     *
     * @var string
     */
    protected $havingClause = '';

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
        $this->tableName = $this->tableStr($table, $columns);

        return $this;
    }

    /**
     * Adds a join to this query
     *
     * @param string|array $table
     *   Either a table name or an associative array with one element. In case of an array, the element's key should be the alias and the value the real table name.
     * @param string $condition
     *   The condition on which to join on
     * @param string|array $columns
     *   OPTIONAL. The column(s) to select from the table. Defaults to '*'.
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function join($table, $condition, $columns = '*')
    {
        $condition = "`" . preg_replace('/\s*=\s*/', '` = `', str_replace('.', '`.`', $condition)) . "`";
        $join = "JOIN " . $this->tableStr($table, $columns) . " ON {$condition}";
        $this->joins[] = $join;

        return $this;
    }

    /**
     * Adds a left join to this query
     *
     * @param string|array $table
     *   Either a table name or an associative array with one element. In case of an array, the element's key should be the alias and the value the real table name.
     * @param string $condition
     *   The condition on which to join on
     * @param string|array $columns
     *   OPTIONAL. The column(s) to select from the table. Defaults to '*'.
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function joinLeft($table, $condition, $columns = '*')
    {
        $condition = "`" . preg_replace('/\s*=\s*/', '` = `', str_replace('.', '`.`', $condition)) . "`";
        $join = "LEFT JOIN " . $this->tableStr($table, $columns) . " ON {$condition}";
        $this->joins[] = $join;

        return $this;
    }

    /**
     * Adds a right join to this query
     *
     * @param string|array $table
     *   Either a table name or an associative array with one element. In case of an array, the element's key should be the alias and the value the real table name.
     * @param string $condition
     *   The condition on which to join on
     * @param string|array $columns
     *   OPTIONAL. The column(s) to select from the table. Defaults to '*'.
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function joinRight($table, $condition, $columns = '*')
    {
        $condition = "`" . preg_replace('/\s*=\s*/', '` = `', str_replace('.', '`.`', $condition)) . "`";
        $join = "RIGHT JOIN " . $this->tableStr($table, $columns) . " ON {$condition}";
        $this->joins[] = $join;

        return $this;
    }

    /**
     * Adds a 'group by' clause to the query
     *
     * @param string|array $group
     *   One or multiple columns to group by
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function group($group) {
        $this->groupBy = is_array($group) ? "`" . implode("`, `", $group) . "`" : "`{$group}`";

        return $this;
    }

    /**
     * Adds a 'order by' clause to the query
     *
     * @param string|array $order
     *   One or multiple columns to order by
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function order($order) {
        $this->orderBy = is_array($order) ? "`" . implode("`, `", $order) . "`" : "`{$order}`";

        return $this;
    }

    /**
     * Adds an offset to the query
     *
     * @param int $offset
     *   The offset
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function offset($offset) {
        $this->offsetBy = (int)$offset;

        return $this;
    }

    /**
     * Adds a limit to the query
     *
     * @param int $limit
     *   The limit
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function limit($limit) {
        $this->limitBy = (int)$limit;

        return $this;
    }

    /**
     * Adds one or more conditions to the 'having' clause
     *
     * @param array $conditions
     *   The conditions to add
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function having($conditions)
    {
        if ($this->havingClause) {
            $this->havingClause .= " AND ";
        }
        $this->havingClause .= $this->whereStr($conditions);

        return $this;
    }

    /**
     * Adds one or more conditions to the 'having' clause, appended with 'OR'
     *
     * @param array $conditions
     *   The conditions to add
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function orHaving($conditions)
    {
        if ($this->havingClause) {
            $this->havingClause .= " OR ";
        }
        $this->havingClause .= $this->whereStr($conditions, 'OR');

        return $this;
    }

    /**
     * ToString function returns the SELECT query
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

        $sql = "SELECT " . implode(', ', $columns) . " FROM {$this->tableName}";
        if (count($this->joins)) {
            $sql .= " " . implode(" ", $this->joins);
        }
        if ($this->whereClause) {
            $sql .= " WHERE {$this->whereClause}";
        }
        if ($this->groupBy) {
            $sql .= " GROUP BY {$this->groupBy}";
        }
        if ($this->havingClause) {
            $sql .= " HAVING {$this->havingClause}";
        }
        if ($this->orderBy) {
            $sql .= " ORDER BY {$this->orderBy}";
        }
        if ($this->offsetBy || $this->limitBy) {
            if ($this->offsetBy) {
                $sql .= " LIMIT {$this->offsetBy}, " . ($this->limitBy ? $this->limitBy : "18446744073709551615");
            } else {
                $sql .= " LIMIT {$this->limitBy}";
            }
        }

        return $sql;
    }
}
