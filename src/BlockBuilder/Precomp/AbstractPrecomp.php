<?php

/**
 * Abstract base class for all precompilers (CSS, Javascript, etc.)
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder\Precomp;

abstract class AbstractPrecomp
{
    /**
     * All the files that need to be precompiled in order
     *
     * @var array
     */
    protected $files = [];

    /**
     * Constructs the precompiler with a base file name
     *
     * @param string $filename
     *   The file name
     */
    public function __construct(string $filename)
    {
        $this->files[] = $filename;
    }

    /**
     * Appends a filename to the list of source files
     *
     * @param string $filename
     */
    public function append(string $filename)
    {
        $this->files[] = $filename;
    }

    /**
     * This method should return the compiled sources
     *
     * @return string
     */
    abstract public function getCompiled();
}
