<?php
/**
 * Http Request Scheme Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Request;

use stdClass;

/**
 * Http Request Scheme Class
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Scheme
{
    /**
     * $server_object
     *
     * Injected copy of $_SERVER
     *
     * @var    object
     * @since  1.0.0
     */
    protected $server_object = null;

    /**
     * Method - GET, HEAD, POST, DELETE, PUT, PATCH
     *
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
     *
     * @var    string
     * @since  1.0.0
     */
    protected $method = null;

    /**
     * Content Type
     *
     * @var    array
     * @since  1.0.0
     */
    protected $content_type = null;

    /**
     * Scheme - HTTP or HTTPS
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3.1
     * @example  http
     * @var      string
     * @since    1.0
     */
    protected $scheme = null;

    /**
     * Secure Protocol
     *
     * @var    bool
     * @since  1.0.0
     */
    protected $secure = false;

    /**
     * Property Array
     *
     * @var    array
     * @since  1.0.0
     */
    protected $property_array
        = array(
            'method',
            'content_type',
            'scheme',
            'secure'
        );

    /**
     * Property Object
     *
     * @var    object
     * @since  1.0.0
     */
    protected $properties;

    /**
     * Construct
     *
     * @param   object $server_object
     *
     * @since   1.0.0
     */
    public function __construct(
        $server_object
    ) {
        $this->server_object = $server_object;
        $this->properties    = new stdClass();
    }

    /**
     * Process Request
     *
     * @return  stdClass
     * @since   1.0.0
     */
    public function set()
    {
        $this->setMethod();
        $this->setContentType();
        $this->setScheme();
        $this->setIsSecure();

        foreach ($this->property_array as $key) {
            $this->properties->$key = $this->$key;
        }

        return $this->properties;
    }

    /**
     * Get Request Method - 'GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS', 'PATCH'
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setMethod()
    {
        $this->method = $this->server_object['REQUEST_METHOD'];

        $method_array = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS', 'PATCH');

        if (in_array($this->method, $method_array)) {
        } else {
            $this->method = 'GET';
        }

        return $this;
    }

    /**
     * Set Content Type
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setContentType()
    {
        $content_type_array = preg_split('/\s*[;,]\s*/', $this->server_object['HTTP_ACCEPT']);

        if (isset($content_type_array[0])) {
        } else {
            $this->content_type = 'text/html';
        }

        $this->content_type = strtolower($content_type_array[0]);

        return $this;
    }

    /**
     * Set the scheme and secure values
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setScheme()
    {
        $this->scheme = 'http://';

        if (empty($this->server_object['HTTPS'])) {
            return $this;
        }

        $this->setSchemeServerObjectHttps();
        $this->setSchemeServerObjectHttpForwarded();
        $this->setSchemeServerObjectServerPort();

        return $this;
    }

    /**
     * Check Server Object HTTPS
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setSchemeServerObjectHttps()
    {
        $https = strtolower($this->server_object['HTTPS']);

        if ($https == 'on' || $https == '1') {
            $this->scheme = 'https';
        }

        return $this;
    }

    /**
     * Check Server Object HTTP_X_FORWARDED_PROTO
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setSchemeServerObjectHttpForwarded()
    {
        if (isset($this->server_object['HTTP_X_FORWARDED_PROTO'])) {
            if (strtolower($this->server_object['HTTP_X_FORWARDED_PROTO']) === 'https') {
                $this->scheme = 'https';
            }
        }

        return $this;
    }

    /**
     * Check Server Object SERVER_PORT
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setSchemeServerObjectServerPort()
    {
        if (isset($this->server_object['SERVER_PORT']) && $this->server_object['SERVER_PORT'] == '443') {
            $this->scheme = 'https://';
        }

        return $this;
    }

    /**
     * Set value indicating if request is a secure protocol
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setIsSecure()
    {
        $this->secure = 0;

        if ($this->scheme == 'https://') {
            $this->secure = 1;
        }

        return $this;
    }
}
