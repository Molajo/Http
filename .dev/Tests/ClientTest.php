<?php
/**
 * Http Client Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Tests;

use Molajo\Http\Client;

/**
 * Http Client Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Client Object
     */
    protected $client = null;

    /**
     * Get the Client Object
     *
     * @covers Molajo\Http\Client::__construct
     * @covers Molajo\Http\Client::setClientData
     * @covers Molajo\Http\Client::get
     * @covers Molajo\Http\Client::getRemoteAddress
     * @covers Molajo\Http\Client::getRemoteAddressNoHttpClientIP
     * @covers Molajo\Http\Client::setRemoteHost
     * @covers Molajo\Http\Client::isAjax
     * @covers Molajo\Http\Client::isCli
     * @covers Molajo\Http\Client::setClient
     * @covers Molajo\Http\Client::setClientMobileDevice
     * @covers Molajo\Http\Client::setClientBrowser
     * @covers Molajo\Http\Client::setClientPlatform
     * @covers Molajo\Http\Client::setClientDesktop
     * @covers Molajo\Http\Client::setClientBot
     * @covers Molajo\Http\Client::setClientUnknownAgent
     */
    public function testGet()
    {
        $server_object = array();

        $server_object['HTTP_CLIENT_IP']        = '127.0.0.1';
        $server_object['REMOTE_HOST']           = 'xyz';
        $server_object['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
        $server_object['HTTP_HOST']             = 0; //not cli
        $server_object['HTTP_USER_AGENT']       = 'macintosh|mac os x firefox';
        $server_object['HTTP_X_FORWARDED_FOR']  = '127.0.0.1';
        $server_object['REMOTE_ADDR']           = '127.0.0.1';
        $server_object['HTTP_CLIENT_IP']        = '127.0.0.1';

        $instance     = new Client($server_object);
        $this->client = $instance->get();

        $this->assertEquals('127.0.0.1', $this->client->remote_address);
        $this->assertEquals('xyz', $this->client->remote_host);
        $this->assertEquals('1', $this->client->ajax);
        $this->assertEquals('firefox', $this->client->browser);
        $this->assertEquals('', $this->client->browser_version);
//$this->assertEquals('macintosh|mac os x firefox', $this->client->user_agent);
        $this->assertEquals('1', $this->client->desktop);
        $this->assertEquals('mac', $this->client->platform);
        $this->assertEquals('0', $this->client->is_bot);
        $this->assertEquals('0', $this->client->is_cli);
        $this->assertEquals('', $this->client->bot);
        $this->assertEquals('0', $this->client->is_mobile);
        $this->assertEquals('', $this->client->mobile_device);
    }
}
