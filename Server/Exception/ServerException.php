<?php
/**
 * Server Exception
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Http\Server\Exception;

defined('MOLAJO') or die;

use RuntimeException;

use Molajo\Http\Server\Api\ExceptionInterface;

/**
 * Server Exception
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
class ServerException extends RuntimeException implements ExceptionInterface
{
}
