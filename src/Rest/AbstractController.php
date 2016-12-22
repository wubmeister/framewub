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
                    $result = $this->findById($id);
                } else {
                    $result = $this->findAll();
                }
                break;

            case 'PUT':
            case 'POST':
                if ($id) {
                    $result = $this->update($id, $request->getParsedBody());
                } else {
                    $result = $this->add($request->getParsedBody());
                }
                break;

            case 'DELETE':
                if ($id) {
                    $result = $this->delete($id);
                }
                break;
        }

        if (is_object($result) && method_exists($result, 'toArray')) {
            $result = $result->toArray();
        }

        return new JsonResponse($result);
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
