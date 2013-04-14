<?php
/**
 * Cookie
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Cookie;

defined('MOLAJO') or die;

use Exception;
use stdClass;

use Molajo\Http\Cookie\Exception\CookieException;

use Molajo\Http\Cookie\Api\CookieInterface;

/**
 * Cookie Class
 *
 * @author    Amy Stephen
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
class Cookie implements CookieInterface
{
    /**
     * Cookie Cookies
     *
     * @var    array
     * @since  1.0
     */
    protected $requestCookies = array();

    /**
     * Response Cookies
     *
     * @var    array
     * @since  1.0
     */
    protected $responseCookies = array();

    /**
     * Cookie
     *
     * @var    object
     * @since  1.0
     */
    protected $cookie = null;

    /**
     * Cookie Name
     *
     * @var    bool
     * @since  1.0
     */
    protected $name = null;

    /**
     * Cookie Value
     *
     * @var    mixed
     * @since  1.0
     */
    protected $value = null;

    /**
     * Expire
     *
     * @var    string
     * @since  1.0
     */
    protected $expire = null;

    /**
     * Path
     *
     * @var    string
     * @since  1.0
     */
    protected $path = null;

    /**
     * Domain
     *
     * @var    string
     * @since  1.0
     */
    protected $domain = null;

    /**
     * Secure
     *
     * @var    bool
     * @since  1.0
     */
    protected $secure = null;

    /**
     * Http Only
     *
     * @var    bool
     * @since  1.0
     */
    protected $http_only = null;

    /**
     * Property Array
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array = array(
        'cookie',
        'cookies',
        'name',
        'value',
        'expire',
        'path',
        'domain',
        'secure',
        'http_only'
    );

    /**
     * Construct
     *
     * @param   string $Http\\CookieClass
     *
     * @see     setCookie()
     * @link    http://www.faqs.org/rfcs/rfc6265.html
     * @return  mixed
     * @since   1.0
     */
    public function __construct($class = 'Molajo\\Http\\Cookie\\Adapter')
    {
        $this->class = 'Molajo\\Http\\Cookie\\Adapter';
        $this->getCookies();

        return $this;
    }

    /**
     * getCookies
     *
     * @return  $this
     * @since   1.0
     * @throws  CookieException
     */
    public function getCookies()
    {
        $cookies = $_COOKIE;

        if (is_array($cookies)) {
        } else {
            return $this;
        }

        if (count($cookies) == 0) {
            return $this;
        }

        foreach ($cookies as $cookie) {
            $this->getCookie($cookie);
        }

        return $this;
    }

    /**
     * Get an HTTP Cookie
     *
     * @param           $name
     *
     * @link    http://www.faqs.org/rfcs/rfc6265.html
     * @return  $this
     * @since   1.0
     */
    public function getCookie(
        $name,
        $value = null,
        $expire = 0,
        $path = '/',
        $domain = '',
        $secure = false,
        $http_only = true
    ) {
        $this->setName($name);
        $results = $this->setValue($value);
        if ($results === false) {
            return $this;
        }
        $this->setExpire($expire);
        $this->setPath($path);
        $this->setDomain($domain);
        $this->setSecure((boolean)$secure);
        $this->setHttpOnly((boolean)$http_only);

        $cookie            = new stdClass();
        $cookie->name      = $this->name;
        $cookie->value     = $this->value;
        $cookie->expire    = $this->expire;
        $cookie->path      = $this->path;
        $cookie->domain    = $this->domain;
        $cookie->secure    = $this->secure;
        $cookie->http_only = $this->http_only;

        $this->responseCookies[$this->name] = $cookie;

        return $this;
    }

    /**
     * Sets a value for an HTTP Cookie to be sent with the HTTP response
     *
     * @param           $name
     * @param   null    $value
     * @param   int     $expire
     * @param   string  $path
     * @param   string  $domain
     * @param   bool    $secure
     * @param   bool    $http_only
     * @param   string  $request_or_response
     *
     * @see     setCookie()
     * @link    http://www.faqs.org/rfcs/rfc6265.html
     * @return  $this
     * @since   1.0
     */
    public function setCookie(
        $name,
        $value = null,
        $expire = 0,
        $path = '/',
        $domain = '',
        $secure = false,
        $http_only = true,
        $request_or_response = 'response'
    ) {
        $this->setName($name);
        $results = $this->setValue($value);
        if ($results === false) {
            return $this;
        }
        $this->setExpire($expire);
        $this->setPath($path);
        $this->setDomain($domain);
        $this->setSecure((boolean)$secure);
        $this->setHttpOnly((boolean)$http_only);

        $cookie            = new stdClass();
        $cookie->name      = $this->name;
        $cookie->value     = $this->value;
        $cookie->expire    = $this->expire;
        $cookie->path      = $this->path;
        $cookie->domain    = $this->domain;
        $cookie->secure    = $this->secure;
        $cookie->http_only = $this->http_only;

        $this->responseCookies[$this->name] = $cookie;

        return $this;
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
     * @throws  CookieException
     */
    public function get($key = null, $default = null, $filter = 'Alphanumeric', $filter_options = array())
    {
        $key = strtolower($key);

        if ((string)$key === '') {
            $cookie = new stdClass();
            foreach ($this->property_array as $key) {
                $cookie->$key = $this->$key;
            }
            return $cookie;
        }

        if (in_array($key, $this->property_array)) {
        } else {
            throw new CookieException('Cookie: Unknown key: ' . $key);
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
     * @throws  CookieException
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new CookieException('Cookie: Set value for unknown key: ' . $key);
        }

        $this->$key = $value;

        return $this->$key;
    }
    /**
     * Set Cookie Name
     *
     * @param   string $name
     *
     * @return  string
     * @since   1.0
     */
    public function setName($name)
    {
        $this->set('name', $name);

        return $this;
    }

    /**
     * Set Cookie Value
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0
     */
    public function setValue($value = null)
    {
        if ($value === null) {

            if (isset($this->responseCookies[$this->name])) {
                unset($this->responseCookies[$this->name]);
                return false;
            }
        }

        $this->value = $value;

        return $this;
    }

    /**
     * Set Expire
     *
     * @param   int $expire
     *
     * @return  $this
     * @since   1.0
     */
    public function setExpire($expire = 0)
    {
        if ($expire === 0) {
            $expire = 365 * 24 * 60 * 60;
        }

        $expire = time() + $expire;

        $this->set('expire', $expire);

        return $this;
    }

    /**
     * Set Path
     *
     * @param   string $path
     *
     * @return  $this
     * @since   1.0
     */
    public function setPath($path = '/')
    {
        if ((string)$path === '') {
            $path = '/';
        }

        $this->set('path', $path);

        return $this;
    }

    /**
     * Set Domain
     *
     * @param   string $domain
     *
     * @return  string
     * @since   1.0
     */
    public function setDomain($domain = '')
    {
        $this->set('domain', (string)$domain);

        return $this;
    }

    /**
     * Set Secure
     *
     * @param   bool $secure
     *
     * @return  object
     * @since   1.0
     */
    public function setSecure($secure = true)
    {
        if ($secure === true) {
        } else {
            $secure = false;
        }

        $this->set('secure', $secure);

        return $this;
    }

    /**
     * Set setHttpOnly
     *
     * @param   bool $http_only
     *
     * @return  object
     * @since   1.0
     */
    public function setHttpOnly($http_only = true)
    {
        if ($http_only === true) {
        } else {
            $http_only = false;
        }

        $this->set('http_only', $http_only);

        return $this;
    }

    /**
     * Delete a cookie
     *
     * @param   string $name
     *
     * @return  $this
     * @since   1.0
     * @throws  CookieException
     */
    public function delete($name)
    {
        $name = (string)$name;

        if (isset($this->responseCookies[$name])) {
            unset($this->responseCookies[$name]);
        }

        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
        }

        return $this;
    }

    /**
     * sendCookies
     *
     * @return  $this
     * @since   1.0
     * @throws  CookieException
     */
    public function sendCookies()
    {
        if (count($this->responseCookies) === 0) {
            return $this;
        }

        foreach ($this->responseCookies as $cookie) {
            $this->sendCookie($cookie);
        }

        return $this;
    }

    /**
     * sendCookie
     *
     * @param   object $cookie
     *
     * @return  $this
     * @since   1.0
     * @throws  CookieException
     */
    public function sendCookie($cookie)
    {
        try {
            setcookie(
                $cookie->name,
                $cookie->value,
                $cookie->expire,
                $cookie->path,
                $cookie->domain,
                $cookie->secure,
                $cookie->http_only
            );

        } catch (Exception $e) {
            throw new CookieException
            ('Cookie Response: setcookie failed for : ' . $cookie->name . ' ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * Authenticate
     *
     * @return bool
     * @since   1.0
     * @throws  CookieException
     */
    public function encrypt()
    {
        $password = null;
        if (isset($parameters['password'])) {
            $password = $parameters['password'];
        }

        $username = null;
        if (isset($parameters['username'])) {
            $username = $parameters['username'];
        }

        $lib      = new PasswordLib / PasswordLib();
        $verified = $lib->verifyPasswordHash($password);

        if ($verified === true) {
        } else {
            throw new CookieException
            ('Authentication Password is incorrect.');
        }

        $actual_password = null;
        if (isset($parameters['actual_password'])) {
            $actual_password = $parameters['actual_password'];
        }

        $results = $this->calculateHash($password, $actual_password);
        if ($results === true) {
        } else {
            throw new CookieException
            ('The password is incorrect.');
        }

        return;
    }
}
