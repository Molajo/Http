<?php
/**
 * Http Redirect Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Tests;

use Molajo\Http\Redirect;

/**
 * Http Redirect Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 *
 */
class RedirectTest
{
    /**
     * @var Redirect
     */
    protected $redirect_object;

    /**
     * Construct
     *
     * @param   string $url
     * @param   int    $status_code
     *
     * @since   1.0
     */
    public function __construct()
    {

    }

    /**
     * Test what header was set
     *
     * @param   string $url
     * @param   int    $status_code
     *
     * @since   1.0
     */
    public function testProperHeaderSet()
    {
        header("HTTP/1.1 302 Moved Temporarily");
        var_dump(headers_list());
    }
}
