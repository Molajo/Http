<?php
/**
 * Server Service
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http;

use stdClass;
use Exception\Http\ServerException;
use CommonApi\Http\ServerInterface;

/**
 * Server
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Server implements ServerInterface
{
    /**
     * $_SERVER['PHP_AUTH_USER']
     *
     * @var    bool
     * @since  1.0
     */
    protected $user = null;

    /**
     * $_SERVER['PHP_AUTH_PW']
     *
     * @var    string
     * @since  1.0
     */
    protected $password = null;

    /**
     * $_SERVER['DOCUMENT_ROOT']
     *
     * @var    string
     * @since  1.0
     */
    protected $document_root = null;

    /**
     * $_SERVER['SCRIPT_FILENAME']
     *
     * @var    string
     * @since  1.0
     */
    protected $entry_point = null;

    /**
     * $_SERVER['REMOTE_ADDR']
     *
     * @var    string
     * @since  1.0
     */
    protected $remote_addr = null;

    /**
     * $_SERVER['SERVER_SIGNATURE']
     *
     * @var    string
     * @since  1.0
     */
    protected $server_signature = null;

    /**
     * $_SERVER['SERVER_SOFTWARE']
     *
     * @var    string
     * @since  1.0
     */
    protected $server_software = null;

    /**
     * $_SERVER['SERVER_NAME']
     *
     * @var    string
     * @since  1.0
     */
    protected $server_name = null;

    /**
     * $_SERVER['SERVER_ADDR']
     *
     * @var    string
     * @since  1.0
     */
    protected $server_addr = null;

    /**
     * $_SERVER['SERVER_PORT']
     *
     * @var    string
     * @since  1.0
     */
    protected $server_port = null;

    /**
     * $_SERVER['SERVER_ADMIN']
     *
     * @var    string
     * @since  1.0
     */
    protected $server_admin = null;

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
        'entry_point',
        'remote_addr',
        'server_signature',
        'server_software',
        'server_name',
        'server_addr',
        'server_port',
        'server_admin'
    );

    /**
     * __construct
     *
     * @since   1.0
     */
    public function __construct()
    {
        $this->setUser();
        $this->setPassword();
        $this->setDocumentRoot();
        $this->setEntryPoint();
        $this->setServer();
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

        if ((string)$key === '*' || (string)$key === '') {

            $results = new stdClass();

            foreach ($this->property_array as $key) {
                $results->$key = $this->$key;
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
     * Set User running script on the Server for this Class
     *
     * @return  string
     * @since   1.0
     */
    protected function setUser()
    {
        if (empty($_SERVER['PHP_AUTH_USER'])) {
            $user = '';
        } else {
            $user = $_SERVER['PHP_AUTH_USER'];
        }

        $this->user = $user;

        return $user;
    }

    /**
     * Set Password for User for the class property
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPassword()
    {
        if (empty($_SERVER['PHP_AUTH_PW'])) {
            $password = '';
        } else {
            $password = $_SERVER['PHP_AUTH_PW'];
        }

        $this->password = $password;

        return $this;
    }

    /**
     * Set the Document Root for the Server for the class property
     *
     * @return  $this
     * @since   1.0
     */
    protected function setDocumentRoot()
    {
        if (empty($_SERVER['DOCUMENT_ROOT'])) {
            $document_root = '';
        } else {
            $document_root = $_SERVER['DOCUMENT_ROOT'];
        }

        $this->document_root = $document_root;

        return $this;
    }

    /**
     * Set Class Entry Point for the script
     *
     * @return  $this
     * @since   1.0
     */
    protected function setEntryPoint()
    {
        if (empty($_SERVER['SCRIPT_FILENAME'])) {
            $entry_point = '';
        } else {
            $entry_point = $_SERVER['SCRIPT_FILENAME'];
        }

        $this->entry_point = $entry_point;

        return $this;
    }

    /**
     * Set Class Server Variables for the class property
     *
     * @return  $this
     * @since   1.0
     */
    protected function setServer()
    {
        $this->remote_addr      = $_SERVER['REMOTE_ADDR'];
        $this->server_signature = $_SERVER['SERVER_SIGNATURE'];
        $this->server_software  = $_SERVER['SERVER_SOFTWARE'];
        $this->server_name      = $_SERVER['SERVER_NAME'];
        $this->server_addr      = $_SERVER['SERVER_ADDR'];
        $this->server_port      = $_SERVER['SERVER_PORT'];
        $this->server_admin     = $_SERVER['SERVER_ADMIN'];

        return $this;
    }
}
