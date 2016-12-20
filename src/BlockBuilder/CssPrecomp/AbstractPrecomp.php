<?php

/**
 * Abstract base class for all CSS precompilers
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder\CssPrecomp;

class AbstractPrecomp
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
    public function __construct($filename)
    {
        $this->files[] = $filename;
    }
}
