<?php
/**
 * Http Request Authority Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Request;

use stdClass;
use CommonApi\Exception\InvalidArgumentException;

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
class Authority
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
     * Scheme
     *
     * @var      string
     * @since    1.0
     */
    protected $scheme = null;

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
     * Property Array
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array
        = array(
            'user',
            'password',
            'userinfo',
            'host',
            'port',
            'authority',
        );

    /**
     * Construct
     *
     * @param   object $server_object
     * @param   string $scheme
     *
     * @since   1.0
     */
    public function __construct(
        $server_object,
        $scheme
    ) {
        $this->server_object = $server_object;
        $this->scheme        = $scheme;
    }

    /**
     * Process Request
     *
     * @return  stdClass
     * @since   1.0
     */
    public function set()
    {
        $this->setUser();
        $this->setPassword();
        $this->setUserinfo();
        $this->setHost();
        $this->setPort();
        $this->setAuthority();

        $authority = new stdClass();
        foreach ($this->property_array as $key) {
            $authority->$key = $this->$key;
        }

        return $authority;
    }

    /**
     * Set the User
     *
     * @return  $this
     * @since   1.0
     */
    protected function setUser()
    {
        return $this->setUserPassword('user', 'PHP_AUTH_USER');
    }

    /**
     * Set the Password
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPassword()
    {
        return $this->setUserPassword('password', 'PHP_AUTH_PW');
    }

    /**
     * Set the Password
     *
     * @param   string $property
     * @param   string $server_object
     *
     * @return  $this
     * @since   1.0
     */
    protected function setUserPassword($property, $server_object)
    {
        $this->$property = '';

        if (isset($this->server_object[$server_object])) {
            $this->$property = $this->server_object[$server_object];
        }

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
     * @return  Authority
     * @since   1.0
     */
    protected function setHost()
    {
        if (empty($this->server_object['HTTP_HOST'])) {
            $host = $this->setHostServerNameAddress();

        } else {
            $host = $this->setHostAndPort();
        }

        $this->validateHost($host);

        $this->host = $host;

        return $this;
    }

    /**
     * Set Host using server object SERVER_NAME and SERVER_ADDRESS
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setHostServerNameAddress()
    {
        $host = $this->setHostVariable('SERVER_NAME');

        if ($host === '') {
            $host = $this->setHostVariable('SERVER_ADDRESS');
        }

        return $host;
    }

    /**
     * Set Host using server object SERVER_NAME and SERVER_ADDRESS
     *
     * @param   string $server_object
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setHostVariable($server_object)
    {
        $host = '';

        if (! empty($this->server_object[$server_object])) {
            $host = $this->server_object[$server_object];
        }

        return $host;
    }

    /**
     * Set Host using server object SERVER_NAME and SERVER_ADDRESS
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setHostAndPort()
    {
        $temp = explode(':', $this->server_object['HTTP_HOST']);

        $host = $temp[0];

        if (isset($temp[1])) {
            $port = (int)$temp[1];
        } else {
            $port = '';
        }

        $this->port = $port;

        return $host;
    }

    /**
     * Set Host using server object SERVER_NAME and SERVER_ADDRESS
     *
     * @param   string $host
     *
     * @return  string
     * @since   1.0.0
     * @throws  \CommonApi\Exception\InvalidArgumentException
     */
    protected function validateHost($host)
    {
        if (preg_match('/^\[?(?:[a-zA-Z0-9-:\]_]+\.?)+$/', $host)) {
        } else {
            throw new InvalidArgumentException(
                'Request: Host value is invalid: ' . $host
            );
        }

        return $host;
    }

    /**
     * Set Port
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPort()
    {
        if ($this->port === '') {
        } else {
            // set in setHost
            return $this;
        }

        if ($this->setPortAllowDefault() === true) {
            $this->port = $this->server_object['SERVER_PORT'];
        }

        return $this;
    }

    /**
     * Set Port Allow Default based on Scheme
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function setPortAllowDefault()
    {
        if (empty($this->server_object['SERVER_PORT'])) {
            return false;
        }

        if ($this->setPortAllowDefaultProtocol('https', '443') === false) {
            return false;
        }

        if ($this->setPortAllowDefaultProtocol('http', '80') === false) {
            return false;
        }

        return true;
    }

    /**
     * Set Port Allow Default based on Scheme
     *
     * @param   string $scheme
     * @param   string $port
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function setPortAllowDefaultProtocol($scheme, $port)
    {
        if ($this->scheme === $scheme && $this->server_object['SERVER_PORT'] === $port) {
            return false;
        }

        return true;
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
            $this->authority = $this->setAuthorityUser();
        }

        $this->authority .= $this->host;
        $this->authority .= $this->setAuthorityPort();
        $this->authority .= '/';

        return $this;
    }

    /**
     * Set Authority User
     *
     * @return  string
     * @since   1.0
     */
    protected function setAuthorityUser()
    {
        $authority = $this->user;
        $authority .= ':';
        $authority .= $this->password . '@';

        return $authority;
    }

    /**
     * Set Authority Port
     *
     * @return  string
     * @since   1.0
     */
    protected function setAuthorityPort()
    {
        if ($this->port == '' || $this->port == 80 || $this->port == 443) {
            $authority = '';
        } else {
            $authority = ':' . $this->port;
        }

        return $authority;
    }
}
