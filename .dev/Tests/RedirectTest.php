<?php
/**
 * Http Redirect Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Tests\Http;

use Molajo\Http\Redirect;

/**
 * Http Redirect Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
class RedirectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Redirect Object
     */
    protected $client = null;

    /**
     * Get the Redirect Object
     *
     * @covers Molajo\Http\Redirect::get
     */
    public function testGet()
    {
        /**
        ob_start();

        $url = 'http://www.example.com';
        $code = 302;
        $class = 'Molajo\Http\Redirect';
        $instance     = new $class($url, $code);
        $instance->redirect();
        $headers_list = headers_list();
        header_remove();
        var_dump($headers_list);
*/
    }
}
