<?php

/**
 * Abstract class for RESTful controllers acting on a storage
 *
 * @package    framewub/rest
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Rest;

use Framewub\Http\Message\ServerRequest;

abstract class StorageController extends AbstractController
{
	/**
	 * The storage object to work with
	 *
	 * @var Framewub\Storage\Db\AbstractStorage
	 */
	protected $storage;

	/**
	 * This method should return a new storage object, which te controller can
	 * work with
	 *
	 * @return Framewub\Storage\Db\AbstractStorage
	 *   The storage
	 */
	abstract protected function getStorage();

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
    	$this->storage = $this->getStorage();
    	return parent::__invoke($request);
    }

    /**
     * Returns all the items in the collection
     *
     * @return Framewub\Storage\Db\Rowset
     */
    protected function findAll()
    {
        return $this->storage->find();
    }

    /**
     * Returns a single item from the collection
     *
     * @param mixed $id
     *   The ID
     *
     * @return Framewub\Storage\StorageObject
     */
    protected function findById($id)
    {
        return $this->storage->findOne($id);
    }

    /**
     * Adds an object to the collection
     *
     * @param array $values
     *   The values for the new object
     *
     * @return Framewub\Storage\StorageObject
     *   The newly created object
     */
    protected function add($values)
    {
        $id = $this->storage->insert($values);
        return $this->storage->findOne($id);
    }

    /**
     * Updates an object in the collection with the specified ID, using the
     * specfied values
     *
     * @param mixed $id
     *   The ID
     * @param array $values
     *   The new values for the object
     *
     * @return Framewub\Storage\StorageObject
     *   The modified version of the object
     */
    protected function update($id, $values)
    {
        $this->storage->update($values, $id);
        return $this->storage->findOne($id);
    }

    /**
     * Deletes an object with the specified ID from the collection
     *
     * @param mixed $id
     *   The ID
     *
     * @return array
     *   The result of the delete action. Contains at least the keys 'success'
     *   (bool) and 'id', the latter being the ID of the deleted object.
     */
    protected function delete($id)
    {
    	$result = [ 'success' => false, 'id' => $id ];
    	if ($this->storage->delete($id)) {
    		$result['success'] = true;
    	}
        return $result;
    }
}
