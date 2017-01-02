<?php

/**
 * Represents a pool of sources
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder;

class Pool
{
	/**
	 * The sources by key
	 *
	 * @var array
	 */
	protected $sources = [];

	/**
	 * Sets the source for a specific key, overwriting the existing source for
	 * that key if any.
	 *
	 * @param string $key
	 *   The key
	 * @param string $source
	 *   The source
	 */
	public function set(string $key, string $source)
	{
		$this->sources[$key] = $source;
	}

	/**
	 * Checks if a source has been stored under the specified key.
	 *
	 * @param string $key
	 *   The key
	 *
	 * @return true if there is a source for the key, false if not
	 */
	public function has(string $key)
	{
		return array_key_exists($key, $this->sources);
	}

	/**
	 * Returns all the sources combined into one string.
	 * Sources will be separated by new lines and a trailing new line will be
	 * added.
	 *
	 * @return string
	 */
	public function getCompiled()
	{
		return implode("\n", $this->sources) . "\n";
	}
}
