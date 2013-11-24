<?php
/**
 * Http Redirect
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Http;

use Exception;
use Exception\Http\RedirectException;
use CommonApi\Http\RedirectInterface;
use CommonApi\Model\FieldhandlerInterface;

/**
 * Http Redirect
 *
 * http://tools.ietf.org/html/rfc2616#section-10.3
 *
 * @package    Molajo
 * @license    MIT
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
Class Redirect implements RedirectInterface
{
    /**
     * Field Handler
     *
     * @var    object
     * @since  1.0
     */
    protected $fieldhandler = null;

    /**
     * $url
     *
     * @var    string
     * @since  1.0
     */
    protected $url = null;

    /**
     * $status_code
     *
     * @var    integer
     * @since  1.0
     */
    protected $status_code = 0;

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'fieldhandler',
        'url',
        'status_code',
    );

    /**
     * Construct
     *
     * @param   string $fieldhandler
     * @param   string $url
     * @param   int    $status_code
     *
     * @since   1.0
     */
    public function __construct(
        FieldhandlerInterface $fieldhandler,
        $url = '',
        $status_code = 0
    ) {
        $this->fieldhandler = $fieldhandler;
        $this->url          = $url;
        $this->status_code  = $status_code;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  RedirectException
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new RedirectException ('Redirect Service Get: unknown key: ' . $key);
        }

        $this->$key = $default;

        return $this->$key;
    }

    /**
     * Set the value of the specified key
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  RedirectException
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new RedirectException ('Redirect Service Set: unknown key: ' . $key);
        }

        $this->$key = $value;

        return $this->$key;
    }

    /**
     * Redirect to the specified Url using the given Status Code
     *
     * @param   string $url
     * @param   int    $status_code
     *
     * @return  string
     * @since   1.0
     * @throws  RedirectException
     */
    public function redirect()
    {
        if ((string)$this->url == '') {
            throw new RedirectException ('Redirect: No Url provided for Redirect.');
        }

        $this->url = $this->validateRedirectUrl();

        if ((int)$this->status_code == 0) {
            $this->status_code = 302;
        }

        if ($this->status_code == 301) {
            header('HTTP/1.1 301 Moved Permanently');
        } elseif ($this->status_code == 302) {
            header('HTTP/1.1 302 Moved Temporarily');
        }

        header('Location: ' . htmlspecialchars($this->url, ENT_QUOTES, 'UTF-8'));

        exit();
    }

    /**
     * Validate Redirect Url
     *
     * @return  object
     * @since   1.0
     * @throws  RedirectException
     */
    protected function validateRedirectUrl()
    {
        $url = $this->url;

        try {

            return $this->fieldhandler->validate('url', $url, 'url');
        } catch (Exception $e) {

            throw new RedirectException ('Redirect: Invalid URL. ' . $e->getMessage());
        }
    }
}
