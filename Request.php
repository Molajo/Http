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
use Exception;
use Exception\Http\RequestException;
use CommonApi\Http\RequestInterface;
use CommonApi\Model\FieldhandlerInterface;

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
     * Method - GET, HEAD, POST, DELETE, PUT. PATCH
     *
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
     *
     * @var    string
     * @since  1.0
     */
    protected $method = null;

    /**
     * Request Uri
     *
     * http://molajo:crocodile/molajo.org:80/base/path/index.php?name=value#fragment
     *
     * @var    string
     * @since  1.0
     */
    protected $uri = null;

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
    protected $is_secure = 0;

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
     * Path
     *
     * ex base/path/
     *
     * @var    string
     * @since  1.0
     */
    protected $path = null;

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
     * @var    array
     * @since  1.0
     */
    protected $parameters = array();

    /**
     * Fragment
     *
     * #fragment
     *
     * @var    string
     * @since  1.0
     */
    protected $fragment = null;

    /**
     * Content Type - mimetype
     *
     * @var    array
     * @since  1.0
     */
    protected $mimetype = null;

    /**
     * Fieldhandler Instance
     *
     * @var    array
     * @since  1.0
     */
    protected $fieldhandler = null;

    /**
     * URL
     *
     * @var    string
     * @since  1.0
     */
    protected $url = null;

    /**
     * Base Url
     *
     * @var    string
     * @since  1.0
     */
    protected $base_url = null;

    /**
     * Property Array
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array = array(
        'method',
        'url',
        'base_url',
        'uri',
        'scheme',
        'is_secure',
        'user',
        'password',
        'host',
        'port',
        'authority',
        'path',
        'query_string',
        'parameters',
        'fragment',
        'mimetype'
    );

    /**
     * Construct
     *
     * @param   FieldhandlerInterface $fieldhandler
     *
     * @since   1.0
     */
    public function __construct(FieldhandlerInterface $fieldhandler)
    {
        $this->fieldhandler = $fieldhandler;

        $this->getMethod();
        $this->getUri();
        $this->getScheme();
        $this->getUser();
        $this->getPassword();
        $this->getHost();
        $this->getPort();
        $this->getQueryString();
        $this->getAuthority();
        $this->getContentType();

        $this->getFragment();
        $this->getPath();
        $this->getUrl();
        $this->getBaseUrl();
    }

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
    public function get($key = null, $default = null, $filter = 'Alphanumeric', $filter_options = array())
    {
        $key = strtolower($key);

        if ((string)$key === '*' || trim($key) === '') {
            $request = new stdClass();
            foreach ($this->property_array as $key) {
                $request->$key = $this->$key;
            }
            return $request;
        }

        if (in_array($key, $this->property_array)) {
        } else {
            throw new RequestException('Request: Unknown key: ' . $key);
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
     * @throws  RequestException
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new RequestException('Request: Set value for unknown key: ' . $key);
        }

        $this->$key = $value;

        return $this->$key;
    }

    /**
     * Get Request Method - 'GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS', 'PATCH'
     *
     * @return  mixed|string|array
     * @since   1.0
     * @throws  RequestException
     */
    public function getMethod()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $method_array = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS', 'PATCH');
        if (in_array($method, $method_array)) {
        } else {
            throw new RequestException
            ('Request: No ($_SERVER[REQUEST_METHOD] value: ' . $_SERVER['REQUEST_METHOD']);
        }

        $this->method = $method;

        return $method;
    }

    /**
     * Get Uri
     *
     * @return  string
     * @since   1.0
     * @throws  RequestException
     */
    public function getUri()
    {
        // Apache and IIS 6
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
            // IIS 5 and PHP as CGI
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            $uri = $_SERVER['ORIG_PATH_INFO'];

            if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '') {
                $uri .= '?' . $_SERVER['QUERY_STRING'];
            }
        } else {
            $uri = '';
        }

        try {
            $uri = $this->filter('uri', $uri, 'url', array());
        } catch (Exception $e) {
            throw new RequestException
            ('Request: Filter class Failed for Key: uri Filter: uri ' . $uri . ' ' . $e->getMessage());
        }

        $this->uri = $uri;

        return $uri;
    }

    /**
     * Returns the Scheme - HTTP or HTTPS
     *
     * @return  string
     * @since   1.0
     */
    public function getScheme()
    {
        $scheme = 'http';

        if (empty($_SERVER['HTTPS'])) {
        } else {
            $https = strtolower($_SERVER['HTTPS']);

            if ($https == 'on' || $https == '1') {
                $scheme = 'https';
            }
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {

            $temp = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']);

            if ($temp == 'https') {
                $scheme = 'https';
            }
        }

        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') {
            $scheme = 'https';
        }
        $scheme .= '://';
        $this->scheme = $scheme;

        if ($scheme == 'https://') {
            $this->secure = 1;
        } else {
            $this->secure = 0;
        }

        return $scheme;
    }

    /**
     * Is a secure protocol used for the Request?
     *
     * @return  string
     * @since   1.0
     */
    public function isSecure()
    {
        $this->getScheme();

        return $this->secure;
    }

    /**
     * Get the User
     *
     * @return  string
     * @since   1.0
     */
    public function getUser()
    {
        $user = '';

        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $user = $_SERVER['PHP_AUTH_USER'];
        }

        $this->user = $user;

        return $user;
    }

    /**
     * Get the Password
     *
     * @return  string
     * @since   1.0
     */
    public function getPassword()
    {
        $password = '';

        if (isset($_SERVER['PHP_AUTH_PW'])) {
            $password = $_SERVER['PHP_AUTH_PW'];
        }

        $this->password = $password;

        return $password;
    }

    /**
     * Host
     *
     * @return  string
     * @since   1.0
     * @throws  RequestException
     */
    public function getHost()
    {
        $this->getScheme();
        $this->getUser();
        $this->getPassword();

        if (empty($_SERVER['HTTP_HOST'])) {

            if (empty($_SERVER['SERVER_NAME'])) {

                if (empty($_SERVER['SERVER_ADDRESS'])) {
                    $host = '';
                } else {
                    $host = $_SERVER['SERVER_ADDRESS'];
                }
            } else {
                $host = $_SERVER['SERVER_NAME'];
            }
        } else {
            $temp = explode(':', $_SERVER['HTTP_HOST']);
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
            throw new RequestException
            ('Request: Host value is invalid: ' . $host);
        }

        $this->host = $host;

        return $host;
    }

    /**
     * Port
     *
     * @return  string
     * @since   1.0
     */
    public function getPort()
    {
        if (empty($_SERVER['SERVER_PORT'])) {
            $port = '';
        } else {
            $port = $_SERVER['SERVER_PORT'];
        }

        $this->getHost();

        if ($this->scheme == 'https' && $port == '443') {
            $port = '';
        }
        if ($this->scheme == 'http' && $port == '80') {
            $port = '';
        }

        if ((int)$this->port == 0) {
            $this->port = $port;
        }

        return $port;
    }

    /**
     * Authority
     *
     * @var     string
     * @since   1.0
     */
    public function getAuthority()
    {
        $auth = $this->getUser();

        if ($auth === '') {
        } else {
            $auth .= ':';
            $auth .= $this->getPassword() . '/';
        }

        $port = $this->getPort();

        if ($port == '' || $port == 80 || $port == 443) {
            $port = '';
        } else {
            $port = ':' . $port;
        }

        $authority = $auth;
        $authority .= $this->getHost();
        $authority .= $port;

        $this->authority = $authority;

        return $authority;
    }

    /**
     * Builds normalized query string with alphabetized key/value pairs
     *
     * @return  string
     * @since   1.0
     */
    public function getQueryString()
    {
        $query_string = $_SERVER['QUERY_STRING'];

        if ($query_string == '') {
            $this->query_string = '';
            $this->parameters   = array();
            return '';
        }

        $parameter_pairs = array();
        $parameter_keys  = array();
        $parts           = explode("&", $_SERVER['QUERY_STRING']);

        if (is_array($parts) && count($parts) > 0) {
            foreach ($parts as $keyAndValue) {
                $pair                  = explode('=', $keyAndValue);
                $key                   = rawurlencode(urldecode($pair[0]));
                $value                 = rawurlencode(urldecode($pair[1]));
                $parameter_pairs[$key] = $value;
                $parameter_keys[]      = $key;
            }
        }

        if (count($parameter_pairs) > 0) {
        } else {
            $this->query_string = '';
            $this->parameters   = array();
            return '';
        }

        array_multisort($parameter_keys, SORT_ASC, $parameter_pairs);

        $this->query_string = $query_string;
        $this->parameters   = $parameter_pairs;

        return $query_string;
    }

    /**
     * Content Type
     *
     * @var     string
     * @since   1.0
     */
    public function getContentType()
    {
        $content_type_array = preg_split('/\s*[;,]\s*/', $_SERVER['HTTP_ACCEPT']);

        $this->mimetype = strtolower($content_type_array[0]);

        return strtolower($content_type_array[0]);
    }

    /**
     * Returns the Fragment
     *
     * @return  string
     * @since   1.0
     */
    public function getFragment()
    {
        $uri = $this->getUri();

        // todo: What????

        if (isset($parsed['fragment'])) {
            $fragment = $parsed['fragment'];
        } else {
            $fragment = '';
        }

        $this->fragment = $fragment;

        return $fragment;
    }

    /**
     * Returns Path
     *
     * @var     string
     * @since   1.0
     */
    public function getPath()
    {
        $uri = $this->getUri();

        $path = $uri;

        $path = ltrim($path, '/');

        $fragment = $this->getFragment();

        if ($fragment == '') {
        } else {
            $fragment = '#' . $fragment;
        }

        $path = rtrim($path, $fragment);

        $query_string = $this->getQueryString();
        $path         = rtrim($path, $query_string);
        $path         = rtrim($path, '?');

        if (strpos($path, 'index.php')) {
            $path = substr($path, 0, strlen($path) - strlen('index.php'));
        }

        $path = rtrim($path, '/');

        $this->path = $path;

        return $path;
    }

    /**
     * Returns Url
     *
     * @var     string
     * @since   1.0
     */
    public function getUrl()
    {
        $url = $this->getScheme();

        $url .= $this->getHost();

        if ($this->getPort() == '') {
        } else {
            $url .= ':' . $this->getPort();
        }

        $uri = $this->getUri();

        $port = $this->getPort();
        if ($port == '' || $port == 80 || $port == 443) {
        } else {
            $test = ':' . $port;
            if (strpos($uri, $test)) {
                $url .= ':' . $port;
            }
        }

        $url .= $uri;

        $fragment = $this->getFragment();
        if ($fragment == '') {
        } else {
            $url .= '#' . $fragment;
        }

        $this->url = $url;

        return $url;
    }

    /**
     * Returns Base Url
     *
     * @var     string
     * @since   1.0
     */
    public function getBaseUrl()
    {
        $base_url = $this->getScheme();
        $base_url .= $this->getAuthority();

        $this->base_url = $base_url;

        return $base_url;
    }

    /**
     * Filter Input
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  string $filter
     * @param  array  $filter_options
     *
     * @return  mixed
     * @since   1.0
     * @throws  RequestException
     */
    protected function filter($key, $value = null, $filter, $filter_options)
    {
        try {
            $value = $this->fieldhandler->filter($key, $value, $filter, $filter_options);
        } catch (Exception $e) {
            throw new RequestException
            ('Request: Filter class Failed for Key: ' . $key . ' Filter: ' . $filter . ' ' . $e->getMessage());
        }

        return $value;
    }
}
