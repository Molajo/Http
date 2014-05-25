<?php
/**
 * Http Request Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http;

use stdClass;
use CommonApi\Http\RequestInterface;
use CommonApi\Exception\InvalidArgumentException;
use Molajo\Http\Request\Authority;
use Molajo\Http\Request\Scheme;

/**
 * Http Request Class
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 *
 * URI Syntax (RFC 3986) http://tools.ietf.org/html/rfc3986
 */
class Request implements RequestInterface
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
     * Method - GET, HEAD, POST, DELETE, PUT, PATCH
     *
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
     *
     * @var    string
     * @since  1.0
     */
    protected $method = null;

    /**
     * Content Type
     *
     * @var    array
     * @since  1.0
     */
    protected $content_type = null;

    /**
     * URL
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3
     * @example  http://username:secret@example.com:8042/over/there/index.php?type=animal&name=narwhal#nose
     * @var      string
     * @since    1.0
     */
    protected $url = null;

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
     * @since  1.0
     */
    protected $secure = false;

    /**
     * User
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3.2.1
     * @example  username
     * @var      string
     * @since    1.0
     */
    protected $user = null;

    /**
     * Password
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3.2.1
     * @example  secret
     * @var      string
     * @since    1.0
     */
    protected $password = null;

    /**
     * Userinfo
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3.2.1
     * @example  username:secret
     * @var      string
     * @since    1.0
     */
    protected $userinfo = null;

    /**
     * Host
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @example  example.com
     * @var      string
     * @since    1.0
     */
    protected $host = null;

    /**
     * Port
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3.2.3
     * @example  8042
     * @var      string
     * @since    1.0
     */
    protected $port = '';

    /**
     * Authority
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3.2
     * @example  user:password@example.com:8042
     * @var      string
     * @since    1.0
     */
    protected $authority = null;

    /**
     * Base URL
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3.2
     * @example  http://user:password@example.com:8042
     * @var      string
     * @since    1.0
     */
    protected $base_url = null;

    /**
     * Path
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3.3
     * @example  /over/there/index.php
     * @var      string
     * @since    1.0
     */
    protected $path = null;

    /**
     * Query String
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3.4
     * @example  type=animal&name=narwhal
     * @var      string
     * @since    1.0
     */
    protected $query = null;

    /**
     * Query Parameters
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3.4
     * @example  array('type' => 'animal', 'name' => 'narwhal')
     * @var      array
     * @since    1.0
     */
    protected $parameters = array();

    /**
     * Request Object
     *
     * @var      object
     * @since    1.0
     */
    protected $request;

    /**
     * Property Array
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array
        = array(
            'method',
            'content_type',
            'base_url',
            'url',
            'scheme',
            'secure',
            'user',
            'password',
            'userinfo',
            'host',
            'port',
            'authority',
            'path',
            'query',
            'parameters'
        );

    /**
     * Construct
     *
     * @param   object $server_object
     *
     * @since   1.0
     */
    public function __construct($server_object)
    {
        $this->server_object = $server_object;

        $this->request = new stdClass();
        $this->setRequest();
    }

    /**
     * Process Request
     *
     * @return  $this
     * @since   1.0
     */
    public function setRequest()
    {
        $scheme = new Scheme($this->server_object);
        $results = $scheme->set();
        foreach ($results as $key => $value) {
            $this->$key = $value;
        }

        $authority = new Authority($this->server_object, $this->scheme);
        $results = $authority->set();
        foreach ($results as $key => $value) {
            $this->$key = $value;
        }

        $this->setQueryParameters();
        $this->setQueryString();
        $this->setBaseUrl();
        $this->setPath();
        $this->setUrl();

        foreach ($this->property_array as $key) {
            $this->request->$key = $this->$key;
        }
    }


    /**
     * Get the request object, as defined by URI Syntax RFC 3986
     *
     * scheme://user:password@example.com:8042/over/there/index.php?type=animal&name=narwhal#nose
     *          \___________/ \_________/ \__/ \__________________/\______________________/ \__/
     *             |               |       |          |                      |                |
     *          userinfo         host     port       path                  query           fragment
     *         \____________________________/                      \__/ \____/ \__/ \____/
     *                     |                                         |    |      |     |
     *                authority                                      key  value  key   value
     *
     * - Returns method, content_type, base_url, url, scheme, secure (boolean), user, password,
     *   userinfo, host, port, authority, path, query, parameters (named pair query array)
     *
     * @link    http://tools.ietf.org/html/rfc3986
     * @return  object
     * @since   1.0
     */
    public function get()
    {
        return $this->request;
    }

    /**
     * Builds query parameters array with sorted key/value pairs
     *
     * @return  $this
     * @since   1.0
     */
    protected function setQueryParameters()
    {
        $query = $this->server_object['QUERY_STRING'];
        if ($query == '') {
            return $this;
        }

        $parameter_pairs = $this->extractQueryParameterPairs($query);
        if (count($parameter_pairs) > 0) {
        } else {
            return $this;
        }

        ksort($parameter_pairs);

        $this->parameters = $parameter_pairs;

        return $this;
    }

    /**
     * Extract the Parameter Pairs
     *
     * @param   string $query
     *
     * @return  array
     * @since   1.0
     */
    protected function extractQueryParameterPairs($query)
    {
        $parameter_pairs = array();

        $parts = explode("&", $query);

        if (is_array($parts) && count($parts) > 0) {
            foreach ($parts as $keyAndValue) {
                $pair                  = explode('=', $keyAndValue);
                $key                   = rawurlencode(urldecode($pair[0]));
                $value                 = rawurlencode(urldecode($pair[1]));
                $parameter_pairs[$key] = $value;
            }
        }

        return $parameter_pairs;
    }

    /**
     * Set normalized query string with sorted key/value pairs
     *
     * @return  $this
     * @since   1.0
     */
    protected function setQueryString()
    {
        $this->query = '';

        if (count($this->parameters) === 0) {
            return $this;
        }

        foreach ($this->parameters as $key => $value) {
            if ($this->query === '') {
            } else {
                $this->query .= '&';
            }
            $this->query .= $key . '=' . $value;
        }

        return $this;
    }

    /**
     * Sets Base Url Value
     *
     * @return  $this
     * @since   1.0
     */
    protected function setBaseUrl()
    {
        $this->base_url = $this->scheme;
        $this->base_url .= $this->authority;

        return $this;
    }

    /**
     * Returns Path
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPath()
    {
        if (isset($this->server_object['ORIG_PATH_INFO'])) {
            $uri = $this->setPathOrigPathInfo();

        } else {
            $uri = $this->server_object['REQUEST_URI'];
        }

        $this->path = filter_var($uri, FILTER_SANITIZE_URL);

        $this->setPathCleanup();

        return $this;
    }

    /**
     * Set Path using server object ORIG_PATH_INFO (IIS 5 and PHP as CGI)
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPathOrigPathInfo()
    {
        $uri = $this->server_object['ORIG_PATH_INFO'];

        if (isset($this->server_object['QUERY_STRING']) && $this->server_object['QUERY_STRING'] != '') {
            $uri .= '?' . $this->server_object['QUERY_STRING'];
        }

        return $uri;
    }

    /**
     * Set Path using server object ORIG_PATH_INFO
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPathCleanup()
    {
        if (strpos($this->path, '?')) {
            $this->path = substr($this->path, 0, strpos($this->path, '?'));
        }

        $this->path = ltrim($this->path, '/');

        $this->path = rtrim($this->path, '/');

        return $this;
    }

    /**
     * Sets Url Value
     *
     * @return  $this
     * @since   1.0
     */
    protected function setUrl()
    {
        $this->url = $this->base_url;
        $this->url .= $this->path;

        if ($this->query === '') {
        } else {
            $this->url .= '?' . $this->query;
        }

        return $this;
    }
}
