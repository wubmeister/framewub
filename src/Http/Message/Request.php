<?php

/**
 * Representation of an outgoing, client-side request.
 *
 * @package    framewub/http-message
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Http\Message;

/**
 * Client-side request
 */
class Request
{
	/**
	 * The protocol version
	 *
	 * @var string
	 */
	protected $protocolVersion = '1.1';

	/**
	 * The headers
	 *
	 * @var array
	 */
	protected $headers = [];

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * @return string
     *   HTTP protocol version.
     */
    public function getProtocolVersion()
    {
    	return $this->protocolVersion;
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * @param string $version
     *   HTTP protocol version
     *
     * @return static
     */
    public function withProtocolVersion($version)
    {
    	$newRequest = clone $this;
    	$newRequest->protocolVersion = $version;
    	return $newRequest;
    }

    /**
     * Retrieves all message header values.
     *
     * @return string[][]
     *  Returns an associative array of the message's headers. Each
     *  key MUST be a header name, and each value MUST be an array of strings
     *  for that header.
     */
    public function getHeaders()
    {
    	return $this->headers;
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * @return string[][]
     *  Returns an associative array of the message's headers. Each
     *  key MUST be a header name, and each value MUST be an array of strings
     *  for that header.
     */
    public function getHeader()
    {
    	return $this->headers;
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value)
    {
    	$newRequest = clone $this;
    	$newRequest->headers[$name] = $value;
    	return $newRequest;
    }
}