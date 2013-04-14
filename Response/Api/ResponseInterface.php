<?php
/**
 * Response Interface
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Response\Api;

defined('MOLAJO') or die;

use DateTime;
Use DateTimeZone;
use Molajo\Http\Response\Exception\ResponseException;

/**
 * Response Interface
 *
 * @author    Amy Stephen
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
Interface ResponseInterface
{
    /**
     * Set the value of a specified key
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  ResponseException
     */
    public function set($key, $value = null);

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   null   $key
     * @param   null   $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  ResponseException
     */
    public function get($key = null, $default = null);

    /**
     * Set HTTP response header - to remove a header, set the key and send in null for value
     *
     * @param   string $key
     * @param   string $value
     * @param   null   $date
     *
     * @return  $this
     * @since   1.0
     * @throws  ResponseException
     */
    public function setHeader($key = '', $value = null, $date = null);

    /**
     * Set Status Code
     *
     * RFC1945 (HTTP/1.0), RFC2616 (HTTP/1.1), and RFC2518 (WebDAV)
     *
     * @param   int    $status_code
     * @param   string $status_message
     *
     * @return  string
     * @since   1.0
     */
    public function setStatus($status_code = 0, $status_message = '');

    /**
     * Set the Body
     *
     * @param   string $body
     *
     * @return  $this
     * @since   1.0
     * @throws  ResponseException
     */
    public function setBody($body = null);

    /**
     * Send Headers and Body
     *
     * @return  string
     * @since   1.0
     * @throws  ResponseException
     */
    public function send();
}
