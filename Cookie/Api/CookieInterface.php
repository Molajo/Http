<?php
/**
 * Cookie Interface
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Cookie\Api;

defined('MOLAJO') or die;

use Molajo\Http\Cookie\Exception\CookieException;

/**
 * Cookie Interface
 *
 * @author    Amy Stephen
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
interface CookieInterface
{
    /**
     * getCookies
     *
     * @return  $this
     * @since   1.0
     * @throws  CookieException
     */
    public function getCookies();

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
    );

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
        $http_only = true
    );

    /**
     * Set Cookie Name
     *
     * @param   string $name
     *
     * @return  string
     * @since   1.0
     */
    public function setName($name);

    /**
     * Set Cookie Value
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0
     */
    public function setValue($value = null);

    /**
     * Set Expire
     *
     * @param   int $expire
     *
     * @return  $this
     * @since   1.0
     */
    public function setExpire($expire = 0);

    /**
     * Set Path
     *
     * @param   string $path
     *
     * @return  $this
     * @since   1.0
     */
    public function setPath($path = '/');

    /**
     * Set Domain
     *
     * @param   string $domain
     *
     * @return  string
     * @since   1.0
     */
    public function setDomain($domain = '');

    /**
     * Set Secure
     *
     * @param   bool $secure
     *
     * @return  object
     * @since   1.0
     */
    public function setSecure($secure = true);

    /**
     * Set setHttpOnly
     *
     * @param   bool $http_only
     *
     * @return  object
     * @since   1.0
     */
    public function setHttpOnly($http_only = true);

    /**
     * Delete a cookie
     *
     * @param   string $name
     *
     * @return  $this
     * @since   1.0
     * @throws  CookieException
     */
    public function delete($name);

    /**
     * sendCookies
     *
     * @return  $this
     * @since   1.0
     * @throws  CookieException
     */
    public function sendCookies();

    /**
     * sendCookie
     *
     * @param   object $cookie
     *
     * @return  $this
     * @since   1.0
     * @throws  CookieException
     */
    public function sendCookie($cookie);

    /**
     * Encrypts the current cookie
     *
     * @return  $this
     * @since   1.0
     */
    public function encrypt();
}
