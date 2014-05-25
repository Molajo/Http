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
     * List of Properties
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array = array(
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
     *
     * @since   1.0
     */
    public function __construct(
        $server_object = null
    ) {
        $this->server_object = $server_object;

        $this->getRemoteAddress();
        $this->getRemoteHost();
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
            if (empty($this->server_object['HTTP_X_FORWARDED_FOR'])) {
                $remote_address = $this->server_object['REMOTE_ADDR'];
            } else {
                $remote_address = $this->server_object['HTTP_X_FORWARDED_FOR'];
            }

        } else {
            $remote_address = $this->server_object['HTTP_CLIENT_IP'];
        }

        $this->remote_address = $remote_address;

        return $this;
    }

    /**
     * Remote Host for Client
     *
     * @return  Client
     * @since   1.0
     */
    protected function getRemoteHost()
    {
        if (empty($this->server_object['REMOTE_HOST'])) {
            $remote_host = '';
        } else {
            $remote_host = $this->server_object['REMOTE_HOST'];
        }

        $this->remote_host = $remote_host;

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
     * @return  Client
     * @since   1.0
     */
    protected function setClient()
    {
        $user_agent = '';

        if (empty($this->server_object['HTTP_USER_AGENT'])) {
            $platform        = 'unknown';
            $desktop         = 0;
            $browser         = 'unknown';
            $browser_version = 'unknown';
            $is_bot          = 0;
            $bot             = 'unknown';
            $is_mobile       = 0;
            $device          = 'unknown';
        } else {
            $user_agent = strtolower($this->server_object['HTTP_USER_AGENT']);

            /** Platform approximations */
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

            /** Desktop approximation */
            if ($platform == 'unknown') {
                $desktop = 0;
            } else {
                $desktop = 1;
            }

            $this->desktop = $desktop;

            /** Browser and Version Approximation */
            $browsers = array(
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

            $browser         = '';
            $browser_version = '';
            foreach ($browsers as $browser) {

                if (preg_match("#($browser)[/ ]?([0-9.]*)#", $user_agent, $match)) {
                    $browser         = $match[1];
                    $browser_version = $match[2];
                    break;
                }
            }

            /** Bot */
            $bots = array(
                'googlebot',
                'msnbot',
                'yahoo!',
                'slurp',
                'bot',
                'spider'
            );

            $is_bot = 0;
            $bot    = '';
            foreach ($bots as $item) {
                if (strpos($user_agent, $item)) {
                    $is_bot = 1;
                    $bot    = $item;
                }
            }

            /** Mobile Devices */
            $devices = array(
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

            $is_mobile = 0;
            $device    = '';
            foreach ($devices as $item) {
                if (strpos($user_agent, $item)) {
                    $is_mobile = 1;
                    $device    = $item;
                }
            }
        }

        $this->browser         = $browser;
        $this->browser_version = $browser_version;
        $this->user_agent      = $user_agent;
        $this->platform        = $platform;
        $this->desktop         = $desktop;
        $this->is_bot          = $is_bot;
        $this->bot             = $bot;
        $this->is_mobile       = $is_mobile;
        $this->mobile_device   = $device;

        return $this;
    }
}
