<?php

/**
 * Interface for all storage classes
 *
 * @package    framewub/storage
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Storage;

/**
 * Storage interface
 */
interface StorageInterface
{

    /**
     * Inserts or updates a set of values
     *
     * @param array $values
     *   The values to save
     *
     * @return int|string
     *   A unique identifier for the saved element
     */
	public function save(array $values);
}