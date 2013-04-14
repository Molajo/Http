<?php
/**
 * Server Interface
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Server\Api;

defined('MOLAJO') or die;

use Molajo\Http\Server\Exception\ServerException;

/**
 * Server Interface
 *
 * @author    Amy Stephen
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
Interface ServerInterface
{
    /**
     * Get Server Data
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  int
     * @since   1.0
     * @throws  ServerException
     */
    public function get($key = null, $default = null);
}
