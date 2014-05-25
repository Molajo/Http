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
    public function __construct(
        $server_object = null
    ) {
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
        $this->setMethod();
        $this->setContentType();
        $this->setScheme();
        $this->setIsSecure();

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
     * Get Request Method - 'GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS', 'PATCH'
     *
     * @return  $this
     * @since   1.0
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
     * @since   1.0
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
     * @since   1.0
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
     * @since   1.0
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
     * @since   1.0
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
     * @since   1.0
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
     * @since   1.0
     */
    protected function setIsSecure()
    {
        $this->secure = 0;

        if ($this->scheme == 'https://') {
            $this->secure = 1;
        }

        return $this;
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
