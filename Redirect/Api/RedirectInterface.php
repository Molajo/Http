<?php
/**
 * Http Redirect Interface
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   MIT
 */
namespace Molajo\Http\Redirect\Api;

defined('MOLAJO') or die;

use Molajo\Http\Redirect\Exception\RedirectException;

/**
 * Http Redirect Interface
 *
 * http://tools.ietf.org/html/rfc2616#section-10.3
 *
 * @package   Molajo
 * @license   MIT
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
Interface RedirectInterface
{
    /**
     * Set the value of the Redirect Url
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0
     * @throws  RedirectException
     */
    public function setUrl($value = '');

    /**
     * Set the Redirect Status Code
     *
     * @param   int $value
     *
     * @return  string
     * @since   1.0
     * @throws  RedirectException
     */
    public function setStatusCode($value = 302);

    /**
     * Redirect to the specified Url using the given Status Code
     *
     * @param   string $url
     * @param   int    $status_code
     *
     * @return  string
     * @since   1.0
     * @throws  RedirectException
     */
    public function redirect($url = '', $status_code = 0);
}
