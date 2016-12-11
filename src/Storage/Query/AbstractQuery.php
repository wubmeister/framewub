<?php

/**
 * Abstract query class
 *
 * @package    framewub/storage
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Storage\Query;

/**
 * Literal function or expression wrapper
 */
class AbstractQuery
{
    /**
     * The table
     *
     * @var string
     */
    protected $tableName;

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
     * The keys for the values to set
     *
     * @var array
     */
    protected $valueKeys = [];

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
     * Adds one or more columns to the query
     *
     * @param string|array $columns
     *   The column(s) to select from the table
     * @param string $table
     *   OPTIONAL. The table the columns belong to. Specify '*' if the column's don't belong to any table. This is also the default value.
     */
    protected function addColumns($columns, $table = '*')
    {
        if (!is_array($columns)) {
            $columns = [ $columns ];
        }

        foreach ($columns as $key => $column) {
            if ($column == '*') {
                $this->columns[$table][] = "*";
            } else if ($column instanceof Func) {
                $this->columns[$table][] = (string)$column . (!is_numeric($key) ? " AS `{$key}`" : "");
            } else {
                $this->columns[$table][] = "`{$column}`" . (!is_numeric($key) ? " AS `{$key}`" : "");
            }
        }
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
    public function where(array $conditions)
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
    public function orWhere(array $conditions)
    {
        if ($this->whereClause) {
            $this->whereClause .= " OR ";
        }
        $this->whereClause .= $this->whereStr($conditions, 'OR');

        return $this;
    }

    /**
     * Adds values to set
     *
     * @param array $values
     *   An associative array of values
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function values($values) {
        foreach ($values as $key => $value) {
            $this->bindParam(":{$key}", $value);
            if (!in_array($key, $this->valueKeys)) {
                $this->valueKeys[] = $key;
            }
        }
        return $this;
    }
}