<?php
/**
 * Http Client
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http;

use stdClass;
use CommonApi\Http\ClientInterface;

/**
 * Http Client
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Client implements ClientInterface
{
    /**
     * $server_object
     *
     * Injected copy of $_SERVER
     *
     * @var    object
     * @since  1.0
     */
    protected $server_object = null;

    /**
     * Ajax
     *
     * @var    bool
     * @since  1.0
     */
    protected $ajax = null;

    /**
     * Remote Address
     *
     * @var    string
     * @since  1.0
     */
    protected $remote_address;

    /**
     * Remote Host
     *
     * @var    string
     * @since  1.0
     */
    protected $remote_host;

    /**
     * Browser
     *
     * @var    string
     * @since  1.0
     */
    protected $browser = null;

    /**
     * Browser Version
     *
     * @var    string
     * @since  1.0
     */
    protected $browser_version = null;

    /**
     * Is Bot? True or False
     *
     * @var    bool
     * @since  1.0
     */
    protected $is_bot = null;

    /**
     * Bot
     *
     * @var    string
     * @since  1.0
     */
    protected $bot = null;

    /**
     * Is CLI? True or False
     *
     * @var    bool
     * @since  1.0
     */
    protected $is_cli = null;

    /**
     * Is Mobile? True or False
     *
     * @var    bool
     * @since  1.0
     */
    protected $is_mobile = null;

    /**
     * Mobile Device
     *
     * @var    string
     * @since  1.0
     */
    protected $mobile_device = null;

    /**
     * User Agent
     *
     * @var    string
     * @since  1.0
     */
    protected $user_agent = null;

    /**
     * Desktop
     *
     * @var    string
     * @since  1.0
     */
    protected $desktop = null;

    /**
     * Platform
     *
     * @var    string
     * @since  1.0
     */
    protected $platform = null;

    /**
     * Devices
     *
     * @var    array
     * @since  1.0
     */
    protected $devices
        = array(
            'alcatel',
            'android',
            'au-mic',
            'audiovox',
            'avantgo',
            'blackberry',
            'blazer',
            'cldc-',
            'danger',
            'epoc',
            'ericsson',
            'ericy',
            'iemobile',
            'ipaq',
            'iphone',
            'j2me',
            'kindle',
            'midp-',
            'minimo',
            'mobile',
            'mot',
            'netfront',
            'nitro',
            'nokia',
            'opera mini',
            'opera mobi',
            'palm',
            'palmsource',
            'panasonic',
            'philips',
            'pocketpc',
            'portable',
            'portalmmm',
            'rover',
            'samsung',
            'sanyo',
            'series60',
            'sharp',
            'sie-',
            'smartphone',
            'sony',
            'symbian',
            'up.browser',
            'up.link',
            'vodafone',
            'wap1.',
            'wap2.',
            'windows ce'
        );

    /**
     * Browsers
     *
     * @var    array
     * @since  1.0
     */
    protected $browsers
        = array(
            'firefox',
            'msie',
            'opera',
            'chrome',
            'safari',
            'mozilla',
            'seamonkey',
            'konqueror',
            'netscape',
            'gecko',
            'navigator',
            'mosaic',
            'lynx',
            'amaya',
            'omniweb',
            'avant',
            'camino',
            'flock',
            'aol'
        );


    /**
     * Bots
     *
     * @var    array
     * @since  1.0
     */
    protected $bots
        = array(
            'googlebot',
            'msnbot',
            'yahoo!',
            'slurp',
            'bot',
            'spider'
        );

    /**
     * List of Properties
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array
        = array(
            'ajax',
            'remote_address',
            'remote_host',
            'browser',
            'browser_version',
            'is_bot',
            'is_cli',
            'bot',
            'is_mobile',
            'mobile_device',
            'user_agent',
            'desktop',
            'platform'
        );

    /**
     * Construct
     *
     * @param   object $server_object
     * @param   array  $devices
     * @param   array  $browsers
     * @param   array  $bots
     *
     * @since   1.0
     */
    public function __construct(
        $server_object = null,
        array $devices = array(),
        array $browsers = array(),
        array $bots = array()
    ) {
        $this->server_object = $server_object;

        if (count($devices) > 0) {
            $this->devices = $devices;
        }

        if (count($browsers) > 0) {
            $this->browsers = $browsers;
        }

        if (count($bots) > 0) {
            $this->browsers = $browsers;
        }

        $this->getRemoteAddress();
        $this->setStandardProperty('REMOTE_HOST', 'remote_host');
        $this->isAjax();
        $this->isCli();
        $this->setClient();
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @return  stdClass
     * @since   1.0
     */
    public function get()
    {
        $client = new stdClass();

        foreach ($this->property_array as $key) {
            $client->$key = $this->$key;
        }

        return $client;
    }

    /**
     * Remote Address for Client
     *
     * @return  $this
     * @since   1.0
     */
    protected function getRemoteAddress()
    {
        if (empty($this->server_object['HTTP_CLIENT_IP'])) {
            $this->remote_address = $this->getRemoteAddressNoHttpClientIP();
        } else {
            $this->remote_address = $this->server_object['HTTP_CLIENT_IP'];
        }

        return $this;
    }

    /**
     * Remote Address for Client - no HTTP_CLIENT_IP
     *
     * @return  $this
     * @since   1.0
     */
    protected function getRemoteAddressNoHttpClientIP()
    {
        if (empty($this->server_object['HTTP_X_FORWARDED_FOR'])) {
            return $this->server_object['REMOTE_ADDR'];
        }

        return $this->server_object['HTTP_X_FORWARDED_FOR'];
    }

    /**
     * Set Properties
     *
     * @param string $client_property
     * @param string $property
     *
     * @return  Server
     * @since   1.0
     */
    protected function setStandardProperty($client_property, $property)
    {
        if (empty($this->server_object[$client_property])) {
            $this->$property = '';
        } else {
            $this->$property = $this->server_object[$client_property];
        }

        return $this;
    }

    /**
     * Determine if Request is the result of an Ajax call
     *
     * @return  Client
     * @since   1.0
     */
    protected function isAjax()
    {
        $ajax = 0;

        if (empty($this->server_object['HTTP_X_REQUESTED_WITH'])) {
        } else {
            if (strtolower($this->server_object['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $ajax = 1;
            }
        }

        $this->ajax = $ajax;

        return $this;
    }

    /**
     * Determine if Request is the result of an CLI call
     *
     * @return  Client
     * @since   1.0
     */
    protected function isCli()
    {
        if (isset($this->server_object['HTTP_HOST'])) {
            $this->is_cli = 0;
        } else {
            $this->is_cli = 1;
        }

        return $this;
    }

    /**
     * Get client information using HTTP_USER_AGENT (Warning: such data is not reliable)
     *
     * @return  client
     * @since   1.0
     */
    protected function setClient()
    {
        if (empty($this->server_object['HTTP_USER_AGENT'])) {
            return $this->setClientUnknownAgent();
        }

        $user_agent = strtolower($this->server_object['HTTP_USER_AGENT']);

        $this->setClientMobileDevice($user_agent);

        $this->setClientPlatform($user_agent);

        $this->setClientDesktop();

        $this->setClientBrowser($user_agent);

        $this->setClientBot($user_agent);

        return $this;
    }

    /**
     * Client Mobile Device
     *
     * @param   string $user_agent
     *
     * @return  $this
     * @since   1.0
     */
    protected function setClientMobileDevice($user_agent)
    {
        $this->is_mobile = 0;
        $this->device    = '';

        foreach ($this->devices as $item) {
            if (strpos($user_agent, $item)) {
                $this->is_mobile = 1;
                $this->device    = $item;
            }
        }

        return $this;
    }

    /**
     * Client Browser
     *
     * @param   string $user_agent
     *
     * @return  $this
     * @since   1.0
     */
    protected function setClientBrowser($user_agent)
    {
        $this->browser         = '';
        $this->browser_version = '';

        foreach ($this->browsers as $browser) {

            if (preg_match("#($browser)[/ ]?([0-9.]*)#", $user_agent, $match)) {
                $this->browser         = $match[1];
                $this->browser_version = $match[2];
                break;
            }
        }

        return $this;
    }

    /**
     * Set Client Platform
     *
     * @param   string $user_agent
     *
     * @return  $this
     * @since   1.0
     */
    protected function setClientPlatform($user_agent)
    {
        if (preg_match('/linux/i', $user_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $user_agent)) {
            $platform = 'windows';
        } else {
            $platform = 'unknown';
        }

        $this->platform = $platform;

        return $this;
    }

    /**
     * Set Client Desktop
     *
     * @return  $this
     * @since   1.0
     */
    protected function setClientDesktop()
    {
        if ($this->platform == 'unknown') {
            $this->desktop = 0;
        } else {
            $this->desktop = 1;
        }

        return $this;
    }

    /**
     * Set Client Bot
     *
     * @param   string $user_agent
     *
     * @return  $this
     * @since   1.0
     */
    protected function setClientBot($user_agent)
    {
        $this->is_bot = 0;
        $this->bot    = '';

        foreach ($this->bots as $item) {
            if (strpos($user_agent, $item)) {
                $this->is_bot = 1;
                $this->bot    = $item;
            }
        }

        return $this;
    }

    /**
     * User Agent Unknown
     *
     * @return  $this
     * @since   1.0
     */
    protected function setClientUnknownAgent()
    {
        $this->platform        = 'unknown';
        $this->desktop         = 0;
        $this->browser         = 'unknown';
        $this->browser_version = 'unknown';
        $this->is_bot          = 0;
        $this->bot             = 'unknown';
        $this->is_mobile       = 0;
        $this->device          = 'unknown';

        return $this;
    }
}
