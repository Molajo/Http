<?php
/**
 * Server Service
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Server;

defined('MOLAJO') or die;

use Molajo\Http\Server\Exception\ServerException;

use Molajo\Http\Server\Api\ServerInterface;

/**
 * Server
 *
 * @author    Amy Stephen
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
class Server implements ServerInterface
{
    /**
     * User running script on server
     *
     * @var    bool
     * @since  1.0
     */
    protected $user = null;

    /**
     * Password for User running server script
     *
     * @var    string
     * @since  1.0
     */
    protected $password = null;

    /**
     * Document Root
     *
     * @var    string
     * @since  1.0
     */
    protected $document_root = null;

    /**
     * Entry Point
     *
     * @var    string
     * @since  1.0
     */
    protected $entry_point = null;

    /**
     * List of Properties
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array = array(
        'user',
        'password',
        'document_root',
        'entry_point'
    );

    /**
     * __construct
     *
     * @since   1.0
     */
    public function __construct()
    {
        $this->getUser();
        $this->getPassword();
        $this->getDocumentRoot();
        $this->getEntryPoint();

        return $this;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  ServerException
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if ((string)$key === '') {

            $results = array();

            foreach ($this->property_array as $key) {
                $results[$key] = $this->$key;
            }

            return $results;
        }

        if (in_array($key, $this->property_array)) {
        } else {
            throw new ServerException('Server: Get for unknown key: ' . $key);
        }

        if ($this->$key === null) {
            $this->$key = $default;
        }

        return $this->$key;
    }

    /**
     * Set the value of a specified key
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  ServerException
     */
    protected function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new ServerException('Server: Set for unknown key: ' . $key);
        }

        $this->$key = $value;

        return $this->$key;
    }

    /**
     * Get User running script on the Server
     *
     * @return  string
     * @since   1.0
     */
    protected function getUser()
    {
        if (empty($_SERVER['PHP_AUTH_USER'])) {
            $user = '';

        } else {
            $user = $_SERVER['PHP_AUTH_USER'];
        }

        $this->set('user', $user);

        return $user;
    }

    /**
     * Get Password for User
     *
     * @return  string
     * @since   1.0
     */
    protected function getPassword()
    {
        if (empty($_SERVER['PHP_AUTH_PW'])) {
            $password = '';

        } else {
            $password = $_SERVER['PHP_AUTH_PW'];
        }

        $this->set('password', $password);

        return $password;
    }

    /**
     * Get the Document Root for the Server
     *
     * @return  string
     * @since   1.0
     */
    protected function getDocumentRoot()
    {
        if (empty($_SERVER['DOCUMENT_ROOT'])) {
            $document_root = '';

        } else {
            $document_root = $_SERVER['DOCUMENT_ROOT'];
        }

        $this->set('document_root', $document_root);

        return $document_root;
    }

    /**
     * Get the Entry Point for the script
     *
     * @return  string
     * @since   1.0
     */
    protected function getEntryPoint()
    {
        if (empty($_SERVER['SCRIPT_FILENAME'])) {
            $entry_point = '';

        } else {
            $entry_point = $_SERVER['SCRIPT_FILENAME'];
        }

        $this->set('entry_point', $entry_point);

        return $entry_point;
    }
}
