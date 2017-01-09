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
     * List of errors that occurred during compilation
     *
     * @var array
     */
    protected $compilerErrors = [];

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
     * Gets errors which occured during compilation, if any
     *
     * @return array
     *   An array of strings, expressing the errors
     */
    public function getCompilerErrors()
    {
        return $this->compilerErrors;
    }

    /**
     * This method should return the compiled sources
     *
     * @return string
     */
    abstract public function getCompiled();
}
