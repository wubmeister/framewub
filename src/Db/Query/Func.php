<?php

/**
 * A literal function or expression for SQL queries. This is a wrapper for
 * strings which should not be encapsulated in quotes.
 *
 * @package    framewub/db
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Db\Query;

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
        $this->expression = $expression;
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
