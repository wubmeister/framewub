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

abstract class StorageAction extends AbstractAction
{
    /**
     * Indicates an insert action
     */
    const INSERT = 1;

    /**
     * Indicates an update action
     */
    const UPDATE = 2;

    /**
     * The storage object to work with
     *
     * @var Framewub\Storage\Db\AbstractStorage
     */
    protected $storage;

    /**
     * These are the callback methods which can be implemented.
     *
     * @var array
     */
    protected static $callbackMethods = [
        'buildIndexQuery',
        'buildDetailQuery',
        'beforeDelete',
        'postprocessIndex',
        'postprocessDetail',
        'postprocessInsert',
        'postprocessUpdate',
        'postprocessDelete'
    ];

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
     * Checks if the undefined method which is called is a known callback. If it
     * is, this method will grecefully return true. If not, this will throw an
     * exception
     *
     * @param string $name
     *   The method name
     * @param array $args
     *   The arguments
     *
     * @return bool
     *   Returns true if the method is a known callback
     * @throws RuntimeException if the method is not a known callback
     */
    public function __call($name, $args)
    {
        if (!in_array($name, self::$callbackMethods)) {
            throw new RuntimeException("Method {$name} dosn't exist");
        }
        return true;
    }

    /**
     * Returns all the items in the collection
     *
     * @return Framewub\Storage\Db\Rowset
     */
    protected function findAll()
    {
        $query = [];
        $this->buildIndexQuery($query);
        $result = $this->storage->find($query);
        $this->postprocessIndex($result);
        return $result;
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
        $query = [];
        $this->buildDetailQuery($query);
        $obj = $this->storage->findOne($id);
        $this->postprocessDetail($obj);
        return $obj;
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
        $values = $this->filterValues($values, self::INSERT);
        $id = $this->storage->insert($values);
        $obj = $this->storage->findOne($id);
        $this->postprocessInsert($obj);
        return $obj;
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
        $values = $this->filterValues($values, self::UPDATE);
        $this->storage->update($values, $id);
        $obj = $this->storage->findOne($id);
        $this->postprocessUpdate($obj);
        return $obj;
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
        if ($this->beforeDelete()) {
            $obj = $this->storage->findOne($id);
            if ($obj && $this->storage->delete($id)) {
                $result['success'] = true;
                $this->postprocessDelete($obj);
            }
        }
        return $result;
    }
}
