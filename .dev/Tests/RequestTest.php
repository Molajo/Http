<?php
/**
 * Http Request Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 *
 */
namespace Molajo\Http\Tests;

use Molajo\Http\Request;

/**
 * Http Request Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 *
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    protected $server_object;

    /**
     * Get the current value (or default) of the specified key
     *
     * @covers Molajo\Http\Request\Adapter::getMethod
     */
    public function testGet($key = null, $default = null, $filter = 'Alphanumeric', $filter_options = array())
    {
        foo://username:password@example.com:8042/over/there/index.dtb?type=animal&name=narwhal#nose

        $this->server_object['REQUEST_METHOD']  = 'GET';
        $this->server_object['REQUEST_URI']     = '/base/path/index.php?name=value&amy=first';
        $this->server_object['HTTPS']           = null;
        $this->server_object['SERVER_PORT']     = 80;
        $this->server_object['PHP_AUTH_USER']   = 'molajo';
        $this->server_object['PHP_AUTH_PW']     = 'crocodile';
        $this->server_object['HTTP_HOST']       = 'molajo.org';
        $this->server_object['QUERY_STRING']    = 'name=value&amy=first';
        $this->server_object['SCRIPT_FILENAME'] = '';
        $this->server_object['HTTP_ACCEPT']     = 'text/html';
        $this->server_object['LANG']            = 'en_US.UTF-8';

        $this->request_object = new Request($this->server_object);
        $request = $this->request_object->get();

        $this->assertEquals('GET', $request->method);
        $this->assertEquals('/base/path/index.php?name=value&amy=first', $request->uri);
        $this->assertEquals('http://', $request->scheme);
        $this->assertEquals(false, $request->is_secure);
        $this->assertEquals('molajo', $request->user);
        $this->assertEquals('crocodile', $request->password);
        $this->assertEquals('molajo.org', $request->host);
        $this->assertEquals('', $request->port);
        $this->assertEquals(array('amy' => 'first', 'name' => 'value'), $request->query_parameters);
        $this->assertEquals('amy=first&name=value', $request->query_string);
        $this->assertEquals('molajo:crocodile/molajo.org', $request->authority);
        $this->assertEquals('text/html', $request->content_type);
        $this->assertEquals('http://molajo:crocodile/molajo.org', $request->base_url);
        $this->assertEquals('/base/path', $request->path);
        $this->assertEquals('http://molajo:crocodile/molajo.org/base/path/index.php?amy=first&name=value', $request->url);

        return $this;
    }
}
