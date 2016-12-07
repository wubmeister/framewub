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
    protected $tableName;

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
     * The 'where' clause
     *
     * @var string
     */
    protected $whereClause = '';

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
     * Generates an qualified (aliased) table name and adds the columns to the query if needed
     *
     * @param string|array $table
     *   Either a table name or an associative array with one element. In case of an array, the element's key should be the alias and the value the real table name.
     * @param string|array $columns
     *   OPTIONAL. The column(s) to select from the table. Defaults to '*'.
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    protected function tableStr($table, $columns = '*')
    {
        if (is_array($table)) {
            foreach ($table as $alias => $name) {
                $qualifiedName = "`{$name}` AS `{$alias}`";
                break;
            }
        } else {
            $qualifiedName = "`{$table}`";
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

        return $qualifiedName;
    }

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
     * Generates a bind parameter name and binds the specified value to it
     *
     * @param mixed $value
     *   The value to bind
     *
     * @return string
     *   The generated parameter name (beginning with a colon)
     */
    protected function bindValue($value) {
        if ($value instanceof Func) {
            return (string)$value;
        }

        $this->bindIndex++;
        $key = ":bind{$this->bindIndex}";
        $this->bind[$key] = $value;

        return $key;
    }

    /**
     * Binds a value to a specified parameter. Overwrites a previously bound value to that parameter if any
     *
     * @param string $param
     *   The parameter name
     * @param mixed $value
     *   The value to bind
     */
    protected function bindParam($param, $value) {
        $this->bind[$param] = $value;
    }

    /**
     * Converts conditions into a part of a 'where' clause
     *
     * @param array $conditions
     *   The conditions
     * @param string $glue
     *   OPTIONAL. Either 'AND' or 'OR'. Defaults to 'OR'
     *
     * @return string
     *   The part of the 'where' clause
     */
    protected function whereStr(&$conditions, $glue = 'AND')
    {
        $str = '';
        foreach ($conditions as $key => $value) {
            if ($key == '$or' || $key == '$and') {
                if ($str) {
                    $str .= $key == '$or' ? ' OR ' : ' AND ';
                }
                $str .= "(" . $this->whereStr($value) . ")";
            } else {
                if ($str) {
                    $str .= " {$glue} ";
                }

                $operator = $value === null ? 'IS' : '=';
                $operand = null;

                if (is_array($value) && !array_key_exists(0, $value)) {
                    $k = array_keys($value)[0];
                    $bindValue = $value[$k];

                    switch ($k) {
                        case '$gt':
                            $operator = '>';
                            break;
                        case '$gte':
                            $operator = '>=';
                            break;
                        case '$lt':
                            $operator = '<';
                            break;
                        case '$lte':
                            $operator = '<=';
                            break;
                        case '$ne':
                            $operator = $bindValue === null ? 'IS NOT' : '<>';
                            break;
                        case '$between':
                            $operator = 'BETWEEN';
                            $operand = $this->bindValue($bindValue[0]) . ' AND ' . $this->bindValue($bindValue[1]);
                            break;
                    }
                } else {
                    $bindValue = $value;
                }

                if (!$operand) {
                    $operand = $bindValue === null ? 'NULL' : $this->bindValue($bindValue);
                }

                $str .= "(`" . str_replace('.', '`.`', $key) . "` {$operator} {$operand})";
            }
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
     * Adds one or more conditions to the 'where' clause, appended with 'OR'
     *
     * @param array $conditions
     *   The conditions to add
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function orWhere($conditions)
    {
        if ($this->whereClause) {
            $this->whereClause .= " OR ";
        }
        $this->whereClause .= $this->whereStr($conditions, 'OR');

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
