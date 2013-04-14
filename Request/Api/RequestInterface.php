<?php
/**
 * Request Interface
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Request\Api;

defined('MOLAJO') or die;

use Molajo\Http\Request\Exception\RequestException;

/**
 * Request Interface
 *
 * @author    Amy Stephen
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
Interface RequestInterface
{
    /** HTTP Methods */
    const
        GET     = 'GET',
        POST    = 'POST',
        PUT     = 'PUT',
        DELETE  = 'DELETE',
        HEAD    = 'HEAD',
        OPTIONS = 'OPTIONS',
        PATCH   = 'PATCH';

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   null   $key
     * @param   null   $default
     * @param   string $filter
     * @param   array  $filter_options
     *
     * @return  mixed
     * @since   1.0
     * @throws  RequestException
     */
    public function get($key = null, $default = null, $filter = 'Alphanumeric', $filter_options = array());

    /**
     * Set the value of a specified key
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  RequestException
     */
    public function set($key, $value = null);

    /**
     * Get Request Method - 'GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS', 'PATCH'
     *
     * @return  mixed|string|array
     * @since   1.0
     * @throws  RequestException
     */
    public function getMethod();

    /**
     * Get Uri
     *
     * @return  string
     * @since   1.0
     * @throws  RequestException
     */
    public function getUri();

    /**
     * Returns the Scheme - HTTP or HTTPS
     *
     * @return  string
     * @since   1.0
     */
    public function getScheme();

    /**
     * Get the User
     *
     * @return  string
     * @since   1.0
     */
    public function getUser();

    /**
     * Get the Password
     *
     * @return  string
     * @since   1.0
     */
    public function getPassword();

    /**
     * Host
     *
     * @return  string
     * @since   1.0
     * @throws  RequestException
     */
    public function getHost();

    /**
     * Port
     *
     * @return  string
     * @since   1.0
     */
    public function getPort();

    /**
     * Authority
     *
     * @var     string
     * @since   1.0
     */
    public function getAuthority();

    /**
     * Returns Path
     *
     * @var     string
     * @since   1.0
     */
    public function getPath();

    /**
     * Builds normalized query string with alphabetized key/value pairs
     *
     * @return  string
     * @since   1.0
     */
    public function getQueryString();

    /**
     * Content Type
     *
     * @var     string
     * @since   1.0
     */
    public function getContentType();
}
