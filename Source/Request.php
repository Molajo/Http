<?php
/**
 * Http Request Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http;

use CommonApi\Http\RequestInterface;
use stdClass;

/**
 * Http Request Class
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
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
     * @since  1.0.0
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
     * @since  1.0.0
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
     * @since   1.0.0
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
     * @since   1.0.0
     */
    protected function setRequest()
    {
        $this->setRequestSubclass('Scheme');
        $this->setRequestSubclass('Authority');
        $this->setBaseUrl();
        $this->setRequestSubclass('Query');
        $this->setRequestSubclass('Path');
        $this->setUrl();

        foreach ($this->property_array as $key) {
            $this->request->$key = $this->$key;
        }
    }

    /**
     * Process Request Subclass
     *
     * @param   string $class
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setRequestSubclass($class)
    {
        $fqn      = 'Molajo\\Http\\Request\\' . $class;
        $instance = new $fqn($this->server_object, $this->scheme);
        $results  = $instance->set();

        foreach ($results as $key => $value) {
            $this->$key = $value;
        }

        return $this;
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
     * @since   1.0.0
     */
    public function get()
    {
        return $this->request;
    }

    /**
     * Sets Base Url Value
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setBaseUrl()
    {
        $this->base_url = $this->scheme;
        $this->base_url .= $this->authority;

        return $this;
    }

    /**
     * Sets Url Value
     *
     * @return  $this
     * @since   1.0.0
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
