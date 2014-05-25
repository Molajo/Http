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
    protected $property_array
        = array(
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
     * Server
     *
     * @var    object
     * @since  1.0
     */
    protected $server;

    /**
     * Construct
     *
     * @param   object $server_object
     *
     * @since   1.0
     */
    public function __construct(
        $server_object,
        $user = null,
        $password = null
    ) {
        $this->server_object = $server_object;

        $this->user     = $user;
        $this->password = $password;
        $this->setServer();

        $this->server = new stdClass();
        foreach ($this->property_array as $key) {
            $this->server->$key = $this->$key;
        }
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
        return $this->server;
    }

    /**
     * Set Class Server Variables for the class property
     *
     * @return  $this
     * @since   1.0
     */
    protected function setServer()
    {
        $this->setStandardProperty('DOCUMENT_ROOT', 'document_root', '');
        $this->setStandardProperty('SCRIPT_FILENAME', 'entry_point', '');
        $this->setStandardProperty('REMOTE_ADDR', 'remote_addr', '');
        $this->setStandardProperty('SERVER_SIGNATURE', 'server_signature', '');
        $this->setStandardProperty('SERVER_SOFTWARE', 'server_software', '');
        $this->setStandardProperty('SERVER_NAME', 'server_name', '');
        $this->setStandardProperty('SERVER_ADDR', 'server_addr', '');
        $this->setStandardProperty('SERVER_PORT', 'server_port', '');
        $this->setStandardProperty('SERVER_ADMIN', 'server_admin', '');

        return $this;
    }

    /**
     * Set Headers Language
     *
     * @param string $server_object_property
     * @param string $property
     * @param string $default
     *
     * @return  Server
     * @since   1.0
     */
    protected function setStandardProperty($server_object_property, $property, $default = '')
    {
        if (empty($this->server_object[$server_object_property])) {
            $this->$property = $default;
        } else {
            $this->$property = $this->server_object[$server_object_property];
        }

        return $this;
    }
}
