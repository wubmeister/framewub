<?php

/**
 * Class for segment routes
 *
 * @package    framewub/route
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Route;

use Framewub\Util\VarExp;

class Segment extends AbstractRoute
{
    /**
     * The variable expression representing the pattern
     *
     * @var Framewub\Util\VarExp
     */
    protected $varExp;

    /**
     * The constructor should take a route descriptor and a piece of middleware
     * (usually a class name).
     *
     * @param string $descriptor
     *   The route pattern
     * @param mixed $middleware
     *   The middleware to map to this route
     */
    public function __construct(string $descriptor, $middleware)
    {
        parent::__construct($descriptor, $middleware);
        $this->varExp = new VarExp($descriptor, true);
    }

    /**
     * Compares the specified URL to this route to see if it matches
     *
     * @param string $url
     *   The URL, starting with a slash ('/')
     *
     * @return array
     *   If the URL matches the route, it returns an array with the middleware,
     *   the params and rest of the URL. If the URL doesn't match, it returns
     *   null.
     */
    public function match($url)
    {
        $match = $this->varExp->match($url);
        if ($match) {
            $result = [ 'middleware' => $this->middleware, 'params' => $match, 'tail' => substr($url, strlen($match['*'])) ];
            $this->matchChildRoutes($result['tail'], $result);
            return $result;
        }
        return null;
    }

    /**
     * Builds a URL based on this route
     *
     * @param array $params
     *   OPTIONAL. The parameters to use. Defaults to an empty array
     *
     * @return string
     *   The built URL
     */
    public function build()
    {
        $args = func_get_args();
        $params = count($args) > 0 ? $args[count($args) - 1] : [];
        if (!is_array($params)) {
            $params = [];
        }
        $url = $this->varExp->build($params) . $this->buildChildRoutes($args);
        return $url;
    }
}
