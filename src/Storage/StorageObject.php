<?php

/**
 * Class to represent a single item fetched from a storage
 *
 * @package    framewub/storage
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Storage;

use PDO;

/**
 * Storage object
 */
class StorageObject
{
    /**
     * The data attributes of the object
     *
     * @var array
     */
    protected $data = [];

    /**
     * The storage this object comes from
     *
     * @var Framewub\Storage\StorageInterface
     */
    protected $storage;

    /**
     * StorageObject constructor
     *
     * @param Framewub\Storage\StorageInterface $storage
     *   The storage this object comes from
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Sets a data attribute
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Gets a data attribute
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Uses the storage object to save this object
     *
     * @return mixed
     *   A unique identifier for the object
     */
    public function save()
    {
        return $this->storage->save($this->data);
    }

    /**
     * Converts the object into an array
     *
     * @return array
     *   The array
     */
    public function toArray()
    {
        return $this->data;
    }
}