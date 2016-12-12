<?php

/**
 * Value object representing a URI.
 *
 * @package    framewub/http-message
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Http\Message;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
	/**
	 * The URI
	 *
	 * @var string
	 */
	protected $uri;

    /**
     * The user info part of the URI
     *
     * @var string
     */
    protected $userInfo = '';

    /**
     * The scheme part of the URI
     *
     * @var string
     */
    protected $scheme = '';

    /**
     * The host name part of the URI
     *
     * @var string
     */
    protected $host = '';

    /**
     * The port part of the URI
     *
     * @var int
     */
    protected $port = null;

    /**
     * The path name part of the URI
     *
     * @var string
     */
    protected $path = '';

    /**
     * The query part of the URI
     *
     * @var string
     */
    protected $query = '';

    /**
     * The fragment part of the URI
     *
     * @var string
     */
    protected $fragment = '';

	public function __construct($uri)
	{
		$this->uri = $uri;
        if (preg_match('/^(([^@]+)@)?(([^:]+):\/\/)?([^\/:]*)(:(\d+))?([^\?&#]*)(\?([^#]*))?(#(.*))?$/', $uri, $match)) {
            $this->userInfo = $match[2];
            $this->scheme = strtolower($match[4]);
            if ($match[5] && !$match[2] && !$match[4] && !$match[7] && $match[5] != 'localhost' && strpos($match[5], '.') === FALSE) {
                $this->path = $match[5] . $match[8];
            } else {
                $this->host = strtolower($match[5]);
                $this->path = strtolower($match[8]);
            }
            $this->port = $match[7] ? (int)$match[7] : null;
            if (count($match) > 10) {
                $this->query = strtolower($match[10]);
            }
            if (count($match) > 12) {
                $this->fragment = strtolower($match[12]);
            }
        }
	}

    /**
     * Retrieve the scheme component of the URI.
     *
     * @return string
     *   The URI scheme.
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * @return string
     *   The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        return ($this->userInfo ? $this->userInfo . '@' : '') . $this->host;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * @return string
     *   The URI user information, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * @return string
     *   The URI host.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Retrieve the port component of the URI.
     *
     * @return null|int
     *   The URI port.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Retrieve the path component of the URI.
     *
     * @return string
     *   The URI path.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * @return string
     *   The URI query string.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * @return string
     *   The URI fragment.
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Return an instance with the specified scheme.
     *
     * @param string $scheme
     *   The scheme to use with the new instance.
     *
     * @return static
     *   A new instance with the specified scheme.
     *
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        $uri = clone $this;
        $uri->scheme = strtolower($scheme);
        return $uri;
    }

    /**
     * Return an instance with the specified user information.
     *
     * @param string $user
     *   The user name to use for authority.
     * @param null|string $password
     *   The password associated with $user.
     * @return static
     *   A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        $uri = clone $this;
        $uri->userInfo = $user . ($password ? ':' . $password : '');
        return $uri;
    }

    /**
     * Return an instance with the specified host.
     *
     * @param string $host
     *   The hostname to use with the new instance.
     *
     * @return static
     *   A new instance with the specified host.
     */
    public function withHost($host)
    {
        $uri = clone $this;
        $uri->host = strtolower($host);
        return $uri;
    }

    /**
     * Return an instance with the specified port.
     *
     * @param null|int $port
     *   The port to use with the new instance; a null value removes the port
     *   information.
     *
     * @return static
     *   A new instance with the specified port.
     *
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        $uri = clone $this;
        $uri->port = (int)$port;
        return $uri;
    }

    /**
     * Return an instance with the specified path.
     *
     * @param string $path
     *   The path to use with the new instance.
     *
     * @return static
     *   A new instance with the specified path.
     *
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        $uri = clone $this;
        $uri->path = strtolower($path);
        return $uri;
    }

    /**
     * Return an instance with the specified query string.
     *
     * @param string $query
     *   The query string to use with the new instance.
     *
     * @return static
     *   A new instance with the specified query string.
     *
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        $uri = clone $this;
        $uri->query = preg_replace('/^\?/', '', $query);
        return $uri;
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * @param string $fragment
     *   The fragment to use with the new instance.
     *
     * @return static
     *   A new instance with the specified fragment.
     */
    public function withFragment($fragment)
    {
        $uri = clone $this;
        $uri->fragment = preg_replace('/^#/', '', $fragment);
        return $uri;
    }

    /**
     * Return the string representation as a URI reference.
     *
     * @return string
     */
    public function __toString()
    {
    	return ($this->userInfo ? $this->userInfo . '@' : '') .
            ($this->scheme ? $this->scheme . '://' : '') .
            $this->host .
            ($this->port ? ':' . $this->port : '') .
            $this->path .
            ($this->query ? '?' . $this->query : '') .
            ($this->fragment ? '#' . $this->fragment : '');
    }
}