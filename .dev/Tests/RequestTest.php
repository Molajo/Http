<?php
/**
 * Http Request Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Tests;

use Molajo\Http\Request;

/**
 * Http Request Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    protected $request_object;

    /**
     * Gets the Request Object
     *
     * @covers Molajo\Http\Request::get
     */
    public function testGet()
    {
        $this->server_object['REQUEST_METHOD']  = 'GET';
        $this->server_object['REQUEST_URI']     = '/over/there/index.php?type=animal&name=narwhal';
        $this->server_object['HTTPS']           = null;
        $this->server_object['SERVER_PORT']     = 8042;
        $this->server_object['PHP_AUTH_USER']   = 'username';
        $this->server_object['PHP_AUTH_PW']     = 'password';
        $this->server_object['HTTP_HOST']       = 'example.com:8042';
        $this->server_object['QUERY_STRING']    = 'type=animal&name=narwhal';
        $this->server_object['SCRIPT_FILENAME'] = '';
        $this->server_object['HTTP_ACCEPT']     = 'text/html';
        $this->server_object['LANG']            = 'en_US.UTF-8';

        $this->request_object = new Request($this->server_object);
        $request              = $this->request_object->get();

        $this->assertEquals('GET', $request->method);
        $this->assertEquals('http://', $request->scheme);
        $this->assertEquals(false, $request->secure);
        $this->assertEquals('username', $request->user);
        $this->assertEquals('password', $request->password);
        $this->assertEquals('example.com', $request->host);
        $this->assertEquals('8042', $request->port);
        $this->assertEquals(array('name' => 'narwhal', 'type' => 'animal'), $request->parameters);
        $this->assertEquals('name=narwhal&type=animal', $request->query);
        $this->assertEquals('username:password@example.com:8042', $request->authority);
        $this->assertEquals('text/html', $request->content_type);
        $this->assertEquals('http://username:password@example.com:8042', $request->base_url);
        $this->assertEquals('/over/there/index.php', $request->path);
        $this->assertEquals(
            'http://username:password@example.com:8042/over/there/index.php?name=narwhal&type=animal',
            $request->url
        );

        return $this;
    }
}
