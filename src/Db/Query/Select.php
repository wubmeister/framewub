<?php

/**
 * Helper to construct SELECT queries for SQL
 *
 * @package    framewub/db
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Db\Query;

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
     *   Either a table name or an associative array with one element. In case
     *   of an array, the element's key should be the alias and the value the
     *   real table name.
     * @param string|array $columns
     *   OPTIONAL. The column(s) to select from the table. Defaults to '*'.
     *
     * @return static
     *   Provides method chaining
     */
    public function from($table, $columns = '*')
    {
        $this->tableName = $this->tableStr($table, $columns);

        return $this;
    }

    /**
     * Adds or replaces columns for the query
     *
     * @param string|array $columns
     *   The column(s) to select from the table
     * @param bool $replaceAll
     *   OPTIONAL. If true, this will replace all existing columns in the query
     *   with the specified ones. Defaults to false.
     *
     * @return static
     *   Provides method chaining
     */
    public function columns($columns, $replaceAll = false)
    {
        if ($replaceAll) {
            $this->columns = [ '*' => [] ];
        } else if (!isset($this->columns['*'])) {
            $this->columns['*'] = [];
        }

        $this->addColumns($columns);

        return $this;
    }

    /**
     * Adds a join to this query
     *
     * @param string|array $table
     *   Either a table name or an associative array with one element. In case
     *   of an array, the element's key should be the alias and the value the
     *   real table name.
     * @param string $condition
     *   The condition on which to join on
     * @param string|array $columns
     *   OPTIONAL. The column(s) to select from the table. Defaults to '*'.
     * @param string|null $method
     *   OPTIONAL. The join method, e.g. 'LEFT', 'INNER', 'RIGHT'
     *
     * @return static
     *   Provides method chaining
     */
    public function join($table, string $condition, $columns = '*', $method = null)
    {
        $pair = preg_split('/\s*=\s*/', $condition);
        $condition = $this->db->quoteIdentifier($pair[0]) . ' = ' . $this->db->quoteIdentifier($pair[1]);
        $join = ($method ? $method . " " : "") . "JOIN " . $this->tableStr($table, $columns) . " ON {$condition}";
        $this->joins[] = $join;

        return $this;
    }

    /**
     * Adds a left join to this query
     *
     * @param string|array $table
     *   Either a table name or an associative array with one element. In case
     *   of an array, the element's key should be the alias and the value the
     *   real table name.
     * @param string $condition
     *   The condition on which to join on
     * @param string|array $columns
     *   OPTIONAL. The column(s) to select from the table. Defaults to '*'.
     *
     * @return static
     *   Provides method chaining
     */
    public function joinLeft($table, string $condition, $columns = '*')
    {
        return $this->join($table, $condition, $columns, "LEFT");
    }

    /**
     * Adds a right join to this query
     *
     * @param string|array $table
     *   Either a table name or an associative array with one element. In case
     *   of an array, the element's key should be the alias and the value the
     *   real table name.
     * @param string $condition
     *   The condition on which to join on
     * @param string|array $columns
     *   OPTIONAL. The column(s) to select from the table. Defaults to '*'.
     *
     * @return static
     *   Provides method chaining
     */
    public function joinRight($table, string $condition, $columns = '*')
    {
        return $this->join($table, $condition, $columns, "RIGHT");
    }

    /**
     * Adds a 'group by' clause to the query
     *
     * @param string|array $group
     *   One or multiple columns to group by
     *
     * @return static
     *   Provides method chaining
     */
    public function group($group) {
        if (!is_array($group)) {
            $group = [ $group ];
        }
        $group = array_map([ $this->db, 'quoteIdentifier' ], $group);
        $this->groupBy = implode(", ", $group);

        return $this;
    }

    /**
     * Adds a 'order by' clause to the query
     *
     * @param string|array $order
     *   One or multiple columns to order by
     *
     * @return static
     *   Provides method chaining
     */
    public function order($order) {
        if (!is_array($order)) {
            $order = [ $order ];
        }
        $ord = [];
        foreach ($order as $o) {
            $p = explode(' ', $o, 2);
            $ord[] = $this->db->quoteIdentifier($p[0]) . (count($p) == 2 ? " {$p[1]}" : "");
        }
        $this->orderBy = implode(", ", $ord);

        return $this;
    }

    /**
     * Adds an offset to the query
     *
     * @param int $offset
     *   The offset
     *
     * @return static
     *   Provides method chaining
     */
    public function offset(int $offset) {
        $this->offsetBy = (int)$offset;

        return $this;
    }

    /**
     * Adds a limit to the query
     *
     * @param int $limit
     *   The limit
     *
     * @return static
     *   Provides method chaining
     */
    public function limit(int $limit) {
        $this->limitBy = (int)$limit;

        return $this;
    }

    /**
     * Adds one or more conditions to the 'having' clause
     *
     * @param array $conditions
     *   The conditions to add
     *
     * @return static
     *   Provides method chaining
     */
    public function having(array $conditions)
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
     * @return static
     *   Provides method chaining
     */
    public function orHaving(array $conditions)
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
