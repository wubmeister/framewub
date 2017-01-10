<?php

/**
 * Class to contain configuration variables
 *
 * @package    framewub
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub;

use Iterator;

class Config implements Iterator
{
    /**
     * Internal data storage
     *
     * @var array
     */
    protected $data = [];

    /**
     * Internal iterator pointer
     *
     * @var int
     */
    protected $it = 0;

    /**
     * Internal iterator keys
     *
     * @var array
     */
    protected $itKeys;

    /**
     * Constructor
     *
     * @param array $data
     *   The data
     */
    public function __construct(array $data = [])
    {
        foreach (array_keys($data) as $key) {
            if (is_array($data[$key]) && !array_key_exists(0, $data[$key])) {
                $this->data[$key] = new static($data[$key]);
            } else {
                $this->data[$key] = $data[$key];
            }
        }
    }

    /**
     * Implicit getter
     *
     * @param string $name
     *   The property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Implicit setter
     *
     * @param string $name
     *   The property name
     * @param mixed $value
     *   The value
     */
    public function __set($name, $value)
    {
        return $this->data[$name] = $value;
    }

    /**
     * Explicit getter
     *
     * @param string $name
     *   The property name
     *
     * @return mixed
     */
    public function get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Checks if a value for the specified name is set
     *
     * @param string $name
     *   The property name
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * Injects another configuration into this configuration. This configuration
     * will be the merged version of both
     *
     * @param Config $other
     *   The other configuration
     */
    public function mergeWith(Config $other)
    {
        foreach ($other->data as $key => $value) {
            if (is_object($this->{$key}) && is_object($value)) {
                $this->data[$key]->mergeWith($value);
            } else {
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * Return the current element
     *
     * @return mixed
     */
    public function current()
    {
        $key = $this->itKeys[$this->it];
        return $this->data[$key];
    }

    /**
     * Return the key of the current element
     *
     * @return string
     */
    public function key()
    {
        return $this->itKeys[$this->it];
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        $this->it++;
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind()
    {
        $this->it = 0;
        $this->itKeys = array_keys($this->data);
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return $this->it < count($this->itKeys);
    }
}
