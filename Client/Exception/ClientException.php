<?php
/**
 * Client  Exception
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Http\Client\Exception;

defined('MOLAJO') or die;

use RuntimeException;

use Molajo\Http\Client\Api\ExceptionInterface;

/**
 * Client Exception
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
class ClientException extends RuntimeException implements ExceptionInterface
{
}
