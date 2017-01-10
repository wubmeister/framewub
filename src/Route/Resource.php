<?php

/**
 * Class for RESTful routes
 *
 * @package    framewub/route
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Route;

use Framewub\Util;

class Resource extends AbstractRoute
{
    /**
     * The name of the resource
     *
     * @var string
     */
    protected $name;

    /**
     * The regular expression to use when matching a URL
     *
     * @var string
     */
    protected $regex;

    /**
     * The key to hold the ID in the param, i.e. the singular form of the
     * resource name suffixed by '_id'
     *
     * @var string
     */
    protected $idKey;

    /**
     * The constructor takes a resource name and a piece of middleware (usually a
     * class name).
     *
     * @param string $name
     *   The resource name (plural)
     * @param mixed $middleware
     *   The middleware to map to this route
     */
    public function __construct(string $name, $middleware)
    {
        $this->name = $name;
        $this->regex = "/\\/{$name}(\\/(\\d+))?/";
        $this->idKey = Util::getSingular($name) . '_id';
        parent::__construct($name, $middleware);
    }

    /**
     * Compares the specified URL to this route to see if it matches
     *
     * @param string $url
     *   The URL, starting with a slash ('/')
     *
     * @return array
     *   If the URL matches the route, it returns an array with the middleware, the
     *   params and rest of the URL. If the URL doesn't match, it returns null.
     */
    public function match($url)
    {
        if (preg_match($this->regex, $url, $match)) {
            $params = [];
            if (count($match) > 2 && $match[2]) {
                $params['id'] = $match[2];
                $params[$this->idKey] = $match[2];
            }
            $result = [ 'middleware' => $this->middleware, 'params' => $params, 'tail' => substr($url, strlen($match[0])) ];
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
        $params = count($args) ? $args[count($args)-1] : [];
        if (!is_array($params)) {
            $params = [];
        }

        $url = '/' . $this->name;
        if (isset($params[$this->idKey])) {
            $url .= '/' . $params[$this->idKey];
        } else if (isset($params['id'])) {
            $url .= '/' . $params['id'];
        }
        $url .= $this->buildChildRoutes($args);

        return $url;
    }
}
