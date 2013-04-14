<?php
/**
 * Response  Exception
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Http\Response\Exception;

defined('MOLAJO') or die;

use RuntimeException;

use Molajo\Http\Response\Api\ExceptionInterface;

/**
 * Response Exception
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
class ResponseException extends RuntimeException implements ExceptionInterface
{
}
