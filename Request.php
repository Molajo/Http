<?php
/**
 * Request
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http;

use stdClass;
use CommonApi\Http\RequestInterface;
use CommonApi\Model\FieldhandlerInterface;
use CommonApi\Exception\InvalidArgumentException;

/**
 * Request Class
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 *
 * URI Syntax (RFC 3986)
 *
 * http://tools.ietf.org/html/rfc3986
 *
 * method          GET, POST, PUT, DELETE, HEAD, OPTIONS, PATCH
 *
 * url             http://molajo:crocodile/molajo.org:80/base/path/index.php?name=value#fragment
 * base_url        http://molajo:crocodile/molajo.org:80
 *
 * uri             base/path/index.php?name=value#fragment
 *
 * scheme          http://
 * user            molajo:
 * password        crocodile
 * host            molajo.org
 * port            :80
 * base path       base/path/index.php?name=value#fragment
 *
 * authority       molajo:crocodile@molajo.org:80
 * path            base/path/
 * query_string    name=value
 * fragment        #fragment
 *
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
     * Request Uri
     *
     * base/path/index.php?name=value#fragment
     *
     * @var    string
     * @since  1.0
     */
    protected $uri = null;

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
     * Scheme - HTTP or HTTPS
     *
     * http://www.iana.org/assignments/uri-schemes.html
     *
     * @var    string
     * @since  1.0
     */
    protected $scheme = null;

    /**
     * Secure Protocol
     *
     * @var    bool
     * @since  1.0
     */
    protected $is_secure = false;

    /**
     * User
     *
     * @var    string
     * @since  1.0
     */
    protected $user = null;

    /**
     * Password
     *
     * @var    string
     * @since  1.0
     */
    protected $password = null;

    /**
     * Host
     *
     * http://localhost:99/molajo/index.php returns http:://localhost:99 (non-standard port)
     *
     * @var    string
     * @since  1.0
     */
    protected $host = null;

    /**
     * Port
     *
     * http://localhost/molajo/index.php returns 80
     *
     * @var    string
     * @since  1.0
     */
    protected $port = null;

    /**
     * Authority
     *
     * molajo:crocodile@molajo.org:80
     *
     * @var    string
     * @since  1.0
     */
    protected $authority = null;

    /**
     * Query String
     *
     * ex name=value
     *
     * @var    string
     * @since  1.0
     */
    protected $query_string = null;

    /**
     * Query Parameters
     *
     * ex array('name' => value)
     *
     * @var    array
     *
     * @since  1.0
     */
    protected $query_parameters = array();

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
     * http://molajo:crocodile/molajo.org:80/base/path/index.php?name=value#fragment
     *
     * @var    string
     * @since  1.0
     */
    protected $url = null;

    /**
     * Base Url
     *
     * http://molajo:crocodile/molajo.org:80/
     *
     * @var    string
     * @since  1.0
     */
    protected $base_url = null;

    /**
     * Path
     *
     * ex base/path/
     *
     * @var    string
     * @since  1.0
     */
    protected $path = null;

    /**
     * Property Array
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array = array(
        'uri',
        'method',
        'scheme',
        'is_secure',
        'user',
        'password',
        'host',
        'port',
        'authority',
        'path',
        'query_string',
        'query_parameters',
        'content_type',
        'url',
        'base_url'
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
        $this->setUri();
        $this->setScheme();
        $this->setIsSecure();
        $this->setUser();
        $this->setPassword();
        $this->setHost();
        $this->setPort();
        $this->setQueryParameters();
        $this->setQueryString();
        $this->setAuthority();
        $this->setContentType();
        $this->setBaseUrl();
        $this->setPath();
        $this->setUrl();
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\InvalidArgumentException
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
     * @throws  \CommonApi\Exception\InvalidArgumentException
     */
    protected function setMethod()
    {
        if ($this->method === null) {
        } else {
            return $this->method;
        }

        $this->method = $this->server_object['REQUEST_METHOD'];

        $method_array = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS', 'PATCH');

        if (in_array($this->method, $method_array)) {
        } else {
            throw new InvalidArgumentException
            ('Request: Invalid $_SERVER[REQUEST_METHOD] value: ' . $this->server_object['REQUEST_METHOD']);
        }

        return $this;
    }

    /**
     * Get Uri
     *
     * @return  string
     * @since   1.0
     */
    protected function setUri()
    {
        // Apache and IIS 6
        if (isset($this->server_object['REQUEST_URI'])) {
            $uri = $this->server_object['REQUEST_URI'];

            // IIS 5 and PHP as CGI
        } elseif (isset($this->server_object['ORIG_PATH_INFO'])) {
            $uri = $this->server_object['ORIG_PATH_INFO'];

            if (isset($this->server_object['QUERY_STRING']) && $this->server_object['QUERY_STRING'] != '') {
                $uri .= '?' . $this->server_object['QUERY_STRING'];
            }

        } else {
            $uri = '';
        }

        $uri = filter_var($uri, FILTER_SANITIZE_URL);

        $this->uri = $uri;

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
        $scheme = 'http';

        if (empty($this->server_object['HTTPS'])) {
        } else {
            $https = strtolower($this->server_object['HTTPS']);

            if ($https == 'on' || $https == '1') {
                $scheme = 'https';
            }
        }

        if (isset($this->server_object['HTTP_X_FORWARDED_PROTO'])) {

            $temp = strtolower($this->server_object['HTTP_X_FORWARDED_PROTO']);

            if ($temp == 'https') {
                $scheme = 'https';
            }
        }

        if (isset($this->server_object['SERVER_PORT']) && $this->server_object['SERVER_PORT'] == '443') {
            $scheme = 'https';
        }
        $scheme .= '://';

        $this->scheme = $scheme;

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
        if ($this->scheme == 'https://') {
            $this->secure = 1;
        } else {
            $this->secure = 0;
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
        $user = '';

        if (isset($this->server_object['PHP_AUTH_USER'])) {
            $user = $this->server_object['PHP_AUTH_USER'];
        }

        $this->user = $user;

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
        $password = '';

        if (isset($this->server_object['PHP_AUTH_PW'])) {
            $password = $this->server_object['PHP_AUTH_PW'];
        }

        $this->password = $password;

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

        if (empty($this->server_object['SERVER_PORT'])) {
            $port = '';
        } else {
            $port = $this->server_object['SERVER_PORT'];
        }

        if ($this->scheme == 'https' && $port == '443') {
            $port = '';
        }
        if ($this->scheme == 'http' && $port == '80') {
            $port = '';
        }

        $this->port = $port;

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
        $query_string = $this->server_object['QUERY_STRING'];

        if ($query_string == '') {
            $this->query_parameters = array();

            return $this;
        }

        $parameter_pairs = array();
        $parts           = explode("&", $this->server_object['QUERY_STRING']);

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
            $this->query_parameters = array();
            return $this;
        }

        ksort($parameter_pairs);

        $this->query_parameters = $parameter_pairs;

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
        $this->query_string = '';

        if (count($this->query_parameters) === 0) {
            return $this;
        }

        foreach ($this->query_parameters as $key => $value) {
            if ($this->query_string === '') {
            } else {
                $this->query_string .= '&';
            }
            $this->query_string .= $key . '=' . $value;
        }

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
        $auth = $this->user;

        if ($auth === '') {
        } else {
            $auth .= ':';
            $auth .= $this->password . '/';
        }

        $port = $this->port;

        if ($port == '' || $port == 80 || $port == 443) {
            $port = '';
        } else {
            $port = ':' . $port;
        }

        $authority = $auth;
        $authority .= $this->host;
        $authority .= $port;

        $this->authority = $authority;

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

        $this->content_type = strtolower($content_type_array[0]);

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
        $base_url = $this->scheme;
        $base_url .= $this->authority;

        if ($this->port == '' || $this->port == 80 || $this->port == 443) {
        } else {
            $base_url .= ':' . $this->port;
        }

        $this->base_url = $base_url;

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
        $path = $this->uri;

        if (strpos($path, '?')) {
            $path = substr($path, 0, strpos($path, '?'));
        }
        if (strpos($path, 'index.php')) {
            $path = substr($path, 0, strpos($path, 'index.php'));
        }
        $path = rtrim($path, '/');

        $this->path = $path;

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
        $url = $this->base_url;

        $url .= $this->path;

        if (strpos($this->uri, 'index.php')) {
            $url .= '/index.php';
        }

        if (strpos($this->uri, '?')) {
            $url .= '?' . $this->query_string;
        }

        $this->url = $url;

        return $this;
    }
}
