<?php
/**
 * Request
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Request;

defined('MOLAJO') or die;

use stdClass;
use Exception;
use Molajo\Http\Request\Exception\RequestException;
use Molajo\Http\Request\Api\RequestInterface;

/**
 * Request Class
 *
 * @author    Amy Stephen
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
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
     * Application Name
     *
     * @var    string
     * @since  1.0
     */
    protected $application_name = null;

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
    protected $secure = 0;

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
     * Fieldhandler Class
     *
     * @var    array
     * @since  1.0
     */
    protected $fieldHandlerClass = null;

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
        'application_name',
        'method',
        'url',
        'base_url',
        'uri',
        'scheme',
        'secure',
        'user',
        'password',
        'host',
        'port',
        'authority',
        'path',
        'query_string',
        'parameters',
        'fragment',
        'fieldHandlerClass',
        'mimetype'
    );

    /**
     * Construct
     *
     * @param   string $fieldHandlerClass
     * @param   string $application_name
     *
     * @since   1.0
     * @throws  RequestException
     */
    public function __construct($fieldHandlerClass = 'Molajo\\FieldHandler\\Adapter',
        $application_name = '')
    {
        $this->application_name = $application_name;

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

        $this->fieldHandlerClass = $fieldHandlerClass;

        return;
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

        if ((string)$key === '') {
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

        $this->set('method', $method);

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

        /**
        try {
        $uri = new $this->fieldHandlerClass('filter', 'uri', $uri, 'url', array());

        } catch (Exception $e) {
        throw new RequestException
        ('Request: Filter class Failed for Key: ' . $key
        . ' Filter: uri ' . $uri . ' ' . $e->getMessage());
        }
         */

        $this->set('uri', $uri);

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
        $this->set('scheme', $scheme);

        if ($scheme == 'https://') {
            $this->set('secure', 1);
        } else {
            $this->set('secure', 0);
        }

        return $scheme;
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

        $this->set('user', $user);

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

        $this->set('password', $password);

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
            $this->set('port', $port);
        }

        if (preg_match('/^\[?(?:[a-zA-Z0-9-:\]_]+\.?)+$/', $host)) {
        } else {
            throw new RequestException
            ('Request: Host value is invalid: ' . $host);
        }

        $this->set('host', $host);

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

        if ($this->scheme == 'https' && $port == '443') {
            $port = '';
        }
        if ($this->scheme == 'http' && $port == '80') {
            $port = '';
        }

        if ((int)$this->get('port') == 0) {
            $this->set('port', $port);
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
        $auth = $this->get('user', '');
        if ($auth === '') {
        } else {
            $auth .= ':';
            $auth .= $this->get('password', '') . '/';
        }

        $port = $this->get('port', '');
        if ($port == '' || $port == 80 || $port == 443) {
            $port = '';
        } else {
            $port = ':' . $port;
        }

        $authority = $auth;
        $authority .= $this->get('host');
        $authority .= $port;

        $this->set('authority', $authority);

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
            $this->set('query_string', '');
            $this->set('parameters', array());
            return '';
        }

        $parameterPairs = array();
        $parameterKeys  = array();
        $parts          = explode("&", $_SERVER['QUERY_STRING']);

        if (is_array($parts) && count($parts) > 0) {
            foreach ($parts as $keyAndValue) {
                $pair                 = explode('=', $keyAndValue);
                $key                  = rawurlencode(urldecode($pair[0]));
                $value                = rawurlencode(urldecode($pair[1]));
                $parameterPairs[$key] = $value;
                $parameterKeys[]      = $key;
            }
        }

        if (count($parameterPairs) > 0) {
        } else {
            $this->set('query_string', '');
            $this->set('parameters', array());
            return '';
        }

        array_multisort($parameterKeys, SORT_ASC, $parameterPairs);

        $this->set('query_string', $query_string);

        $this->set('parameters', $parameterPairs);

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
        $content_type = $_SERVER['HTTP_ACCEPT'];

        $mimetypes = $this->getMimeArray();
        foreach ($mimetypes as $key => $value) {
            if ($value == $content_type) {
                $content_type = $key;
                break;
            }
        }

        $this->set('mimetype', $content_type);

        return $content_type;
    }

    /**
     * Returns the Fragment
     *
     * @return  string
     * @since   1.0
     */
    public function getFragment()
    {
        $uri = $this->get('uri', '');

        if (isset($parsed['fragment'])) {
            $fragment = $parsed['fragment'];
        } else {
            $fragment = '';
        }

        $this->set('fragment', $fragment);

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
        $uri = $this->get('uri');

        $remove = $this->get('scheme');
        $remove .= $this->get('authority');

        $port = $this->get('port', '');

        if ($port == '' || $port == 80 || $port == 443) {
            $test = ':' . $port;
            if (strpos($uri, $test)) {
                $remove .= ':' . $port;
            }
        }

        $path = $uri;
        if (strpos($path, $remove)) {
            $path = substr($uri, strlen($remove), 999);
        }

        $path = ltrim($path, '/');
        $path = ltrim($path, 'index.php');
        $path = ltrim($path, '?');

        $fragment = $this->get('fragment', '');
        if ($fragment == '') {
        } else {
            $fragment = '#' . $fragment;
        }

        $path = rtrim($path, $fragment);

        $query_string = $this->get('query_string');
        $path         = rtrim($path, $query_string);

        $path = rtrim($path, '?');
        $path = rtrim(strtolower($path), 'index.php');
        $path = rtrim($path, '/');

        $this->set('path', $path);

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
        $url = $this->get('scheme');
        $url .= $this->get('host');
        $uri = $this->get('uri');

        $port = $this->get('port', '');
        if ($port == '' || $port == 80 || $port == 443) {
        } else {
            $test = ':' . $port;
            if (strpos($uri, $test)) {
                $url .= ':' . $port;
            }
        }

        $url .= $uri;

        $fragment = $this->get('fragment', '');
        if ($fragment == '') {
        } else {
            $url .= '#' . $fragment;
        }

        $this->set('url', $url);

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
        $base_url = $this->get('scheme');
        $base_url .= $this->get('authority');

        $this->set('base_url', $base_url);

        return $base_url;
    }

    /**
     * Filter Input
     *
     * @param      $key
     * @param null $value
     * @param      $filter
     * @param      $filter_options
     *
     * @return  mixed
     * @since   1.0
     * @throws  RequestException
     */
    protected function filter($key, $value = null, $filter, $filter_options)
    {
        try {
            $this->$key = new $this->fieldHandlerClass('filter', $key, $value, $filter, $filter_options);

        } catch (Exception $e) {
            throw new RequestException
            ('Request: Filter class Failed for Key: ' . $key . ' Filter: ' . $filter . ' ' . $e->getMessage());
        }

    }

    /**
     * PUT THIS INTO FIELD FILTER
     *
     * Utility method - force consistency in True and False
     *
     * @param bool $variable
     * @param bool $default
     *
     * @return bool
     * @since   1.0
     */
    private function getMimeArray()
    {
        $mime_types = array(

            'txt'  => 'text/plain',
            'html' => 'text/html',
            'html' => 'application/xhtml+xml',
            'php'  => 'text/html',
            'css'  => 'text/css',
            'js'   => 'application/javascript',
            'js'   => 'application/x-javascript',
            'js'   => 'text/javascript',
            'json' => 'application/json',
            'json' => 'application/x-json',
            'xml'  => 'application/xml',
            'xml'  => 'text/xml',
            'xml'  => 'application/x-xml',
            'swf'  => 'application/x-shockwave-flash',
            'flv'  => 'video/x-flv',
            'rdf'  => 'application/rdf+xml',
            'atom' => 'application/atom+xml',
            'rss'  => 'application/rss+xml',
            'png'  => 'image/png',
            'jpe'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg'  => 'image/jpeg',
            'gif'  => 'image/gif',
            'bmp'  => 'image/bmp',
            'ico'  => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif'  => 'image/tiff',
            'svg'  => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'zip'  => 'application/zip',
            'rar'  => 'application/x-rar-compressed',
            'exe'  => 'application/x-msdownload',
            'msi'  => 'application/x-msdownload',
            'cab'  => 'application/vnd.ms-cab-compressed',
            'mp3'  => 'audio/mpeg',
            'qt'   => 'video/quicktime',
            'mov'  => 'video/quicktime',
            'pdf'  => 'application/pdf',
            'psd'  => 'image/vnd.adobe.photoshop',
            'ai'   => 'application/postscript',
            'eps'  => 'application/postscript',
            'ps'   => 'application/postscript',
            'doc'  => 'application/msword',
            'rtf'  => 'application/rtf',
            'xls'  => 'application/vnd.ms-excel',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',
            'odt'  => 'application/vnd.oasis.opendocument.text',
            'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        return $mime_types;
    }
}
