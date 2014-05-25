<?php
/**
 * Http Server Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Tests;

use Molajo\Http\Server;

/**
 * Http Server Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 *
 */
class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Server
     */
    protected $server_object;

    /**
     * Gets the Server Object
     *
     * @covers Molajo\Http\Server::get
     */
    public function testGet()
    {
        $this->server_object['PHP_AUTH_USER']    = 'username';
        $this->server_object['PHP_AUTH_PW']      = 'password';
        $this->server_object['DOCUMENT_ROOT']    = '/Sites';
        $this->server_object['SCRIPT_FILENAME']  = '/usr/local/php5/bin/phpunit';
        $this->server_object['REMOTE_ADDR']      = '127.0.0.1';
        $this->server_object['SERVER_SIGNATURE'] = '123456789123456789';
        $this->server_object['SERVER_NAME']      = 'TEST SERVER';
        $this->server_object['SERVER_ADDR']      = '1.2.3.4';
        $this->server_object['SERVER_PORT']      = '88';
        $this->server_object['SERVER_ADMIN']     = 'Admin Istrator';
        $this->server_object['SERVER_SOFTWARE']  = 'Apache v 1.3';

        $this->server_object = new Server($this->server_object, 'username', 'password');
        $server              = $this->server_object->get();

        $this->assertEquals('username', $server->user);
        $this->assertEquals('password', $server->password);
        $this->assertEquals('/Sites', $server->document_root);
        $this->assertEquals('/usr/local/php5/bin/phpunit', $server->entry_point);
        $this->assertEquals('127.0.0.1', $server->remote_addr);
        $this->assertEquals('123456789123456789', $server->server_signature);
        $this->assertEquals('TEST SERVER', $server->server_name);
        $this->assertEquals('1.2.3.4', $server->server_addr);
        $this->assertEquals('88', $server->server_port);
        $this->assertEquals('Admin Istrator', $server->server_admin);
        $this->assertEquals('Apache v 1.3', $server->server_software);

        return $this;
    }
}
