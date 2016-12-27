<?php

/**
 * Base class for all RESTful request handlers
 *
 * @package    framewub/rest
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Rest;

use Framewub\Http\Message\ServerRequest;
use Framewub\Http\Message\Response\Json as JsonResponse;

abstract class AbstractController
{
    const FIND_ALL = 1;
    const FIND_BY_ID = 2;
    const ADD = 3;
    const UPDATE = 4;
    const DELETE = 5;

    /**
     * The status code of the response
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Handles a request and gives a response
     *
     * @param Framewub\Http\Message\ServerRequest $request
     *   The request
     *
     * @return Framewub\Http\Message\Response
     *   The response
     */
    public function __invoke(ServerRequest $request)
    {
        $id = $request->getAttribute('id');
        $result = [ 'success' => false ];

        switch ($request->getMethod()) {
            case 'GET':
                if ($id) {
                    if ($this->isAuthorized(static::FIND_BY_ID)) {
                        $result = $this->findById($id);
                    } else {
                        $this->statusCode = 403;
                    }
                } else {
                    if ($this->isAuthorized(static::FIND_ALL)) {
                        $result = $this->findAll();
                    } else {
                        $this->statusCode = 403;
                    }
                }
                break;

            case 'PUT':
            case 'POST':
                if ($id) {
                    if ($this->isAuthorized(static::UPDATE)) {
                        $result = $this->update($id, $request->getParsedBody());
                    } else {
                        $this->statusCode = 403;
                    }
                } else {
                    if ($this->isAuthorized(static::ADD)) {
                        $result = $this->add($request->getParsedBody());
                    } else {
                        $this->statusCode = 403;
                    }
                }
                break;

            case 'DELETE':
                if ($id) {
                    if ($this->isAuthorized(static::DELETE)) {
                        $result = $this->delete($id);
                    } else {
                        $this->statusCode = 403;
                    }
                }
                break;
        }

        if (is_object($result) && method_exists($result, 'toArray')) {
            $result = $result->toArray();
        }

        $response = new JsonResponse($result);
        if ($this->statusCode != 200) {
            $response = $response->withStatus($this->statusCode);
        }

        return $response;
    }

    /**
     * Checks if the client is authorized to perform the specified action. Each
     * overriding class should implement this in its own way, this method is
     * just a stub which always returns true.
     *
     * @param int $action
     *   One of the action constants of this class
     *
     * @return bool
     *   Returns true if the client is authorized, false if not.
     */
    protected function isAuthorized($action)
    {
        return true;
    }

    /**
     * This method MUST return all the visible objects in the collection
     *
     * @return array
     *   The objects
     */
    abstract protected function findAll();

    /**
     * This method MUST return an object identified by the given ID or null if
     * there is no object with that ID
     *
     * @param mixed $id
     *   The ID
     *
     * @return array|object|null
     *   The object
     */
    abstract protected function findById($id);

    /**
     * This method MUST create an object with the given values, if the values
     * are valid.
     *
     * @param array|object $values
     *   The values
     *
     * @return array|object|null
     *   The newly created object
     */
    abstract protected function add($values);

    /**
     * This method MUST update an existing object with the given values, if the
     * values are valid.
     *
     * @param mixed $id
     *   The object's ID
     * @param array|object $values
     *   The values
     *
     * @return array|object|null
     *   The newly created object
     */
    abstract protected function update($id, $values);

    /**
     * This method MUST delete an existing object with the given ID.
     *
     * @param mixed $id
     *   The object's ID
     *
     * @return array|object|null
     *   Status information about the deletion
     */
    abstract protected function delete($id);
}
