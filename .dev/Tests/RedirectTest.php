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
class RedirectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Redirect
     */
    protected $redirect_object;

    /**
     * Gets the Redirect Object
     *
     * @covers Molajo\Http\Redirect::redirect
     */
    public function testRedirect()
    {
        $url = '/feed/';
        $code = 301;

        $this->redirect_object = new Redirect($url, $code);
        $redirect              = $this->redirect_object->get();

        $this->assertRedirectTo('/feed/');

        return $this;
    }
}
