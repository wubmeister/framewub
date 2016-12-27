<?php

/**
 * Authentication of identities stored in a databse table
 *
 * @package    framewub/auth
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Auth;

use Framewub\Db\Generic;
use Framewub\Db\Query\Select;

class Db extends AbstractAuth
{
    /**
     * Error code 'OK'
     */
    const ERR_OK = 0;

    /**
     * Error code 'No such user'
     */
    const ERR_USER_NOT_FOUND = 1;

    /**
     * Error code 'Incorrect password'
     */
    const ERR_INCORRECT_PASSWORD = 2;

    /**
     * Error code 'Database fail' (meaning querying the database failed)
     */
    const ERR_DB_FAIL = 3;

    /**
     * The database adapter to use to fetch identities
     *
     * @var Framewub\Db\Generic
     */
    protected $db;

    /**
     * The table in which the identities are stored
     *
     * @var string
     */
    protected $table;

    /**
     * The error code as result of the 'authenticate' method
     *
     * @var int
     */
    protected $error;

    /**
     * Constructor
     *
     * @param Framewub\Db\Generic $db
     *   The database adapter to use
     * @param string $table
     *   The table in which the identities are stored
     */
    public function __construct(Generic $db, $table)
    {
        $this->db = $db;
        $this->table = $table;
        $this->error = static::ERR_OK;
    }

    /**
     * Hashes the password
     *
     * @param string $password
     * @param string $salt
     *
     * @return string
     *   The hashed password
     */
    protected function hashPassword($password, $salt)
    {
        return hash('sha256', $password.$salt);
    }

    /**
     * Authenticates an identity with the given credentials.
     * The credentials array MUST contain the keys 'username' and 'password'
     *
     * @param array $credentials
     *   The credentials
     */
    public function authenticate(array $credentials)
    {
        $select = new Select($this->db);
        $select->from($this->table)->where([ 'username' => $credentials['username'] ]);
        $stmt = $this->db->execute($select, $select->getBind());

        if ($stmt) {
            $identity = $stmt->fetch(\PDO::FETCH_OBJ);

            if ($identity) {
                $hash = $this->hashPassword($credentials['password'], $identity->salt);

                if ($hash == $identity->password) {
                    unset($identity->password, $identity->salt);
                    $this->setIdentity([ 'id' => $identity->id ]);
                    $this->identity = $identity;

                    $this->error = static::ERR_OK;
                } else {
                    $this->error = static::ERR_INCORRECT_PASSWORD;
                }
            } else {
                $this->error = static::ERR_USER_NOT_FOUND;
            }
        } else {
            $this->error = static::ERR_DB_FAIL;
        }
    }

    /**
     * Retrieves the error, indicating why the 'authenticate' method
     * failed. If 'authenticate' succeeded, this methos returns an empty string.
     *
     * @return $string
     */
    public function getError()
    {
        return $this->error;
    }
}
