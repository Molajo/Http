<?php
/**
 * Client Interface
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Client\Api;

defined('MOLAJO') or die;

use Molajo\Http\Client\Exception\ClientException;

/**
 * Client Interface
 *
 * @author    Amy Stephen
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
Interface ClientInterface
{
    /**
     * Get Client Data
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  int
     * @since   1.0
     * @throws  ClientException
     */
    public function get($key = null, $default = null);
}
