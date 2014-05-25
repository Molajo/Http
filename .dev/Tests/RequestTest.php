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
     * @covers Molajo\Http\Request::__construct
     * @covers Molajo\Http\Request::setRequest
     * @covers Molajo\Http\Request::setRequestSubclass
     * @covers Molajo\Http\Request::get
     * @covers Molajo\Http\Request::setBaseUrl
     * @covers Molajo\Http\Request::setUrl
     *
     * @covers Molajo\Http\Request\Scheme::__construct
     * @covers Molajo\Http\Request\Scheme::set
     * @covers Molajo\Http\Request\Scheme::setMethod
     * @covers Molajo\Http\Request\Scheme::setContentType
     * @covers Molajo\Http\Request\Scheme::setScheme
     * @covers Molajo\Http\Request\Scheme::setSchemeServerObjectHttps
     * @covers Molajo\Http\Request\Scheme::setSchemeServerObjectHttpForwarded
     * @covers Molajo\Http\Request\Scheme::setSchemeServerObjectServerPort
     * @covers Molajo\Http\Request\Scheme::setIsSecure
     *
     * @covers Molajo\Http\Request\Authority::__construct
     * @covers Molajo\Http\Request\Authority::set
     * @covers Molajo\Http\Request\Authority::setUser
     * @covers Molajo\Http\Request\Authority::setPassword
     * @covers Molajo\Http\Request\Authority::setUserPassword
     * @covers Molajo\Http\Request\Authority::setUserinfo
     * @covers Molajo\Http\Request\Authority::setHost
     * @covers Molajo\Http\Request\Authority::setHostServerNameAddress
     * @covers Molajo\Http\Request\Authority::setHostVariable
     * @covers Molajo\Http\Request\Authority::setHostAndPort
     * @covers Molajo\Http\Request\Authority::validateHost
     * @covers Molajo\Http\Request\Authority::setPort
     * @covers Molajo\Http\Request\Authority::setPortAllowDefault
     * @covers Molajo\Http\Request\Authority::setPortAllowDefaultProtocol
     * @covers Molajo\Http\Request\Authority::setAuthority
     * @covers Molajo\Http\Request\Authority::setAuthorityUser
     * @covers Molajo\Http\Request\Authority::setAuthorityPort
     *
     * @covers Molajo\Http\Request\Query::__construct
     * @covers Molajo\Http\Request\Query::set
     * @covers Molajo\Http\Request\Query::setQueryParameters
     * @covers Molajo\Http\Request\Query::extractQueryParameterPairs
     * @covers Molajo\Http\Request\Query::setQueryString
     *
     * @covers Molajo\Http\Request\Path::__construct
     * @covers Molajo\Http\Request\Path::set
     * @covers Molajo\Http\Request\Path::setPath
     * @covers Molajo\Http\Request\Path::setPathOrigPathInfo
     * @covers Molajo\Http\Request\Path::setPathCleanup
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
        $this->assertEquals('username:password@example.com:8042/', $request->authority);
        $this->assertEquals('text/html', $request->content_type);
        $this->assertEquals('http://username:password@example.com:8042/', $request->base_url);
        $this->assertEquals('over/there/index.php', $request->path);
        $this->assertEquals(
            'http://username:password@example.com:8042/over/there/index.php?name=narwhal&type=animal',
            $request->url
        );

        return $this;
    }
}
