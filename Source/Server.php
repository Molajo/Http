<?php
/**
 * Http Server Object
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http;

use stdClass;
use CommonApi\Http\ServerInterface;

/**
 * Http Server Object
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Server implements ServerInterface
{
    /**
     * $server_object
     *
     * Injected copy of $_SERVER
     *
     * @var    object
     * @since  1.0
     */
    protected $server_object = null;

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
     * Construct
     *
     * @param   object $server_object
     *
     * @since   1.0
     */
    public function __construct(
        $server_object = null
    ) {
        $this->server_object = $server_object;

        $this->setUser();
        $this->setPassword();
        $this->setDocumentRoot();
        $this->setEntryPoint();
        $this->setServer();
    }

    /**
     * Get the server object, including the following elements:
     *
     * - user, password, document_root, entry_point, remote_addr, server_signature, server_software,
     *  server_name, server_addr, server_port, server_admin
     *
     * @link    http://tools.ietf.org/html/rfc3986
     * @return  stdClass
     * @since   1.0
     */
    public function get()
    {
        $server = new stdClass();

        foreach ($this->property_array as $key) {
            $server->$key = $this->$key;
        }

        return $server;
    }

    /**
     * Set User running script on the Server for this Class
     *
     * @return  string
     * @since   1.0
     */
    protected function setUser()
    {
        if (empty($this->server_object['PHP_AUTH_USER'])) {
            $user = '';
        } else {
            $user = $this->server_object['PHP_AUTH_USER'];
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
        if (empty($this->server_object['PHP_AUTH_PW'])) {
            $password = '';
        } else {
            $password = $this->server_object['PHP_AUTH_PW'];
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
        if (empty($this->server_object['DOCUMENT_ROOT'])) {
            $document_root = '';
        } else {
            $document_root = $this->server_object['DOCUMENT_ROOT'];
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
        if (empty($this->server_object['SCRIPT_FILENAME'])) {
            $entry_point = '';
        } else {
            $entry_point = $this->server_object['SCRIPT_FILENAME'];
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
        $this->remote_addr      = $this->server_object['REMOTE_ADDR'];
        $this->server_signature = $this->server_object['SERVER_SIGNATURE'];
        $this->server_software  = $this->server_object['SERVER_SOFTWARE'];
        $this->server_name      = $this->server_object['SERVER_NAME'];
        $this->server_addr      = $this->server_object['SERVER_ADDR'];
        $this->server_port      = $this->server_object['SERVER_PORT'];
        $this->server_admin     = $this->server_object['SERVER_ADMIN'];

        return $this;
    }
}
