<?php

/**
 * A literal function or expression for SQL queries. This is a wrapper for strings which
 * should not be encapsulated in quotes.
 *
 * @package    framewub/storage
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    http://mit-license.org/
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Storage\Query;

/**
 * Literal function or expression wrapper
 */
class Func
{
    /**
     * The expression
     *
     * @var string
     */
    protected $expression;

    /**
     * Func constructor
     *
     * @param string $expression
     *   The literal expression
     */
    public function __construct($expression)
    {
        $this->expression = $expression
    }

    /**
     * ToString function just returns the expression as-is
     *
     * @return string
     *   The literal expression
     */
    public function __toString()
    {
        return $this->expression;
    }
}
