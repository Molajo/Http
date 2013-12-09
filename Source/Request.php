<?php
/**
 * Http Request Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http;

use stdClass;
use CommonApi\Http\RequestInterface;
use CommonApi\Exception\InvalidArgumentException;

/**
 * Http Request Class
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
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
    protected $port = null;

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
     * Property Array
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array = array(
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

        $this->setMethod();
        $this->setContentType();
        $this->setScheme();
        $this->setIsSecure();
        $this->setUser();
        $this->setPassword();
        $this->setUserinfo();
        $this->setHost();
        $this->setPort();
        $this->setAuthority();
        $this->setQueryParameters();
        $this->setQueryString();
        $this->setBaseUrl();
        $this->setPath();
        $this->setUrl();
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
     * @since   0.1
     */
    public function get()
    {
        $request = new stdClass();

        foreach ($this->property_array as $key) {
            $request->$key = $this->$key;
        }

        return $request;
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

        $https = strtolower($this->server_object['HTTPS']);

        if ($https == 'on' || $https == '1') {
            $this->scheme = 'https';
        }

        if (isset($this->server_object['HTTP_X_FORWARDED_PROTO'])) {

            $temp = strtolower($this->server_object['HTTP_X_FORWARDED_PROTO']);

            if ($temp == 'https') {
                $this->scheme = 'https';
            }
        }

        if ($this->scheme == 'http://' || $this->scheme == 'https://') {
        } else {
            $this->scheme = 'http://';
        }

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
     * Set the User
     *
     * @return  $this
     * @since   1.0
     */
    protected function setUser()
    {
        $this->user = '';

        if (isset($this->server_object['PHP_AUTH_USER'])) {
        } else {
            return $this;
        }

        $this->user = $this->server_object['PHP_AUTH_USER'];

        return $this;
    }

    /**
     * Set the Password
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPassword()
    {
        $this->password = '';

        if ($this->user === '') {
            return $this;
        }

        if (isset($this->server_object['PHP_AUTH_PW'])) {
        } else {
            return $this;
        }

        $this->password = $this->server_object['PHP_AUTH_PW'];

        return $this;
    }

    /**
     * Set the Userinfo Value
     *
     * @return  $this
     * @since   1.0
     */
    protected function setUserinfo()
    {
        $this->userinfo = '';

        if ($this->user === '') {
            return $this;
        }

        $this->userinfo = $this->user . ':' . $this->password;

        return $this;
    }

    /**
     * Set and Filter Host (potentially set port)
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\InvalidArgumentException
     */
    protected function setHost()
    {
        if (empty($this->server_object['HTTP_HOST'])) {

            if (empty($this->server_object['SERVER_NAME'])) {

                if (empty($this->server_object['SERVER_ADDRESS'])) {
                    $host = '';
                } else {
                    $host = $this->server_object['SERVER_ADDRESS'];
                }
            } else {
                $host = $this->server_object['SERVER_NAME'];
            }

        } else {
            $temp = explode(':', $this->server_object['HTTP_HOST']);
            $host = $temp[0];
            if (isset($temp[1])) {
                $port = (int)$temp[1];
            } else {
                $port = '';
            }
            $this->port = $port;
        }

        if (preg_match('/^\[?(?:[a-zA-Z0-9-:\]_]+\.?)+$/', $host)) {
        } else {
            throw new InvalidArgumentException
            ('Request: Host value is invalid: ' . $host);
        }

        $this->host = $host;

        return $this;
    }

    /**
     * Set Port
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPort()
    {
        if ($this->port === null) {
        } else {
            // set in setHost
            return $this;
        }

        $this->port = '';

        if (empty($this->server_object['SERVER_PORT'])) {
            return $this;
        }

        $port = $this->server_object['SERVER_PORT'];

        if ($this->scheme == 'https' && $port == '443') {
            return $this;
        }
        if ($this->scheme == 'http' && $port == '80') {
            return $this;
        }

        $this->port = $port;

        return $this;
    }

    /**
     * Set Authority
     *
     * @return  $this
     * @since   1.0
     */
    protected function setAuthority()
    {
        $this->authority = '';

        if ($this->user === '') {
        } else {
            $this->authority = $this->user;
            $this->authority .= ':';
            $this->authority .= $this->password . '@';
        }

        $this->authority .= $this->host;

        if ($this->port == '' || $this->port == 80 || $this->port == 443) {
        } else {
            $this->authority .= ':' . $this->port;
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
        $this->parameters = array();

        $query = $this->server_object['QUERY_STRING'];

        if ($query == '') {
            return $this;
        }

        $parameter_pairs = array();
        $parts           = explode("&", $query);

        if (is_array($parts) && count($parts) > 0) {
            foreach ($parts as $keyAndValue) {
                $pair                  = explode('=', $keyAndValue);
                $key                   = rawurlencode(urldecode($pair[0]));
                $value                 = rawurlencode(urldecode($pair[1]));
                $parameter_pairs[$key] = $value;
            }
        }

        if (count($parameter_pairs) > 0) {
        } else {
            return $this;
        }

        ksort($parameter_pairs);

        $this->parameters = $parameter_pairs;

        return $this;
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
        // IIS 5 and PHP as CGI
        if (isset($this->server_object['ORIG_PATH_INFO'])) {
            $uri = $this->server_object['ORIG_PATH_INFO'];

            if (isset($this->server_object['QUERY_STRING']) && $this->server_object['QUERY_STRING'] != '') {
                $uri .= '?' . $this->server_object['QUERY_STRING'];
            }

        } else {
            $uri = $this->server_object['REQUEST_URI'];
        }

        $this->path = filter_var($uri, FILTER_SANITIZE_URL);

        if (strpos($this->path, '?')) {
            $this->path = substr($this->path, 0, strpos($this->path, '?'));
        }

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