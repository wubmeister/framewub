<?php

/**
 * Abstract base class for all transformations
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder\Transform;

abstract class AbstractTransform
{
    /**
     * The file name
     *
     * @var string
     */
    protected $filename;

    /**
     * Constructs a transform with the given file name
     *
     * @param string $filename
     *   The file name
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * This method should transform scheme data to PHTML
     *
     * @param array $data
     *   The data
     */
    abstract public function transform(array $data);
}
