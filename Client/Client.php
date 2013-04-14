<?php
/**
 * Http Client
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Client;

defined('MOLAJO') or die;

use Molajo\Http\Client\Api\ClientInterface;

use Molajo\Http\Client\Exception\ClientException;

/**
 * Http Client
 *
 * http://goo.gl/aXHML
 *
 * @author    Amy Stephen
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
class Client implements ClientInterface
{
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
     * __construct
     *
     * @since   1.0
     */
    public function __construct()
    {
        $this->getRemoteAddress();
        $this->getRemoteHost();
        $this->isAjax();
        $this->isCli();
        $this->setClient();

        return $this;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  ClientException
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if ((string) $key === '') {

            $results = array();

            foreach ($this->property_array as $key) {
                $results[$key] = $this->$key;
            }

            return $results;
        }

        if (in_array($key, $this->property_array)) {
        } else {
            throw new ClientException('Client: Get for unknown key: ' . $key);
        }

        if ($this->$key === null) {
            $this->$key = $default;
        }

        return $this->$key;
    }

    /**
     * Set the value of a specified key
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  ClientException
     */
    protected function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new ClientException('Client: Set for unknown key: ' . $key);
        }

        $this->$key = $value;

        return $this->$key;
    }

    /**
     * Remote Address for Client
     *
     * @return  string
     * @since   1.0
     */
    protected function getRemoteAddress()
    {
        if (empty($_SERVER['HTTP_CLIENT_IP'])) {
        } else {
            $remote_address = $_SERVER['HTTP_CLIENT_IP'];
        }

        if (empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $remote_address = $_SERVER['REMOTE_ADDR'];

        } else {
            $remote_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        $this->set('remote_address', $remote_address);

        return $remote_address;
    }

    /**
     * Remote Host for Client
     *
     * @return  string
     * @since   1.0
     */
    protected function getRemoteHost()
    {
        if (empty($_SERVER['REMOTE_HOST'])) {
            $remote_host = '';

        } else {
            $remote_host = $_SERVER['REMOTE_HOST'];
        }

        $this->set('remote_host', $remote_host);

        return $remote_host;
    }

    /**
     * Determine if Request is the result of an Ajax call
     *
     * @return  int
     * @since   1.0
     */
    protected function isAjax()
    {
        $ajax = 0;

        if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        } else {
            if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $ajax = 1;
            }
        }

        $this->set('Ajax', $ajax);

        return $ajax;
    }

    /**
     * Determine if Request is the result of an CLI call
     *
     * @return  int
     * @since   1.0
     */
    protected function isCli()
    {
        $is_cli = 0;

        if (isset($_SERVER['HTTP_HOST'])) {
            $this->set('is_cli', 0);
        } else {
            $this->set('is_cli', 1);
        }

        return $is_cli;
    }

    /**
     * Get client information using HTTP_USER_AGENT (very rough and not very reliable)
     *
     * - might be *somewhat* helpful (*maybe* better than nothing) for very high-level guess about desktop versus mobile
     *   in those cases where it's critical to handle the payload or interface differently on the server side
     *   if this disturbs you to even read this comment avoid using this data and nothing will rub off on you.
     *
     * http://goo.gl/aXHML
     *
     * @return  object
     * @since   1.0
     */
    protected function setClient()
    {
        $user_agent = '';

        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            $platform        = 'unknown';
            $desktop         = 0;
            $browser         = 'unknown';
            $browser_version = 'unknown';
            $is_bot          = 0;
            $bot             = 'unknown';
            $is_mobile       = 0;
            $device          = 'unknown';

        } else {
            $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

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

            $this->set('platform', $platform);

            /** Desktop approximation */
            if ($platform == 'unknown') {
                $desktop = 0;
            } else {
                $desktop = 1;
            }

            $this->set('desktop', $desktop);

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

        $this->set('browser', $browser);
        $this->set('browser_version', $browser_version);
        $this->set('user_agent', $user_agent);
        $this->set('platform', $platform);
        $this->set('desktop', $desktop);
        $this->set('is_bot', $is_bot);
        $this->set('bot', $bot);
        $this->set('is_mobile', $is_mobile);
        $this->set('mobile_device', $device);

        return $this;
    }
}
