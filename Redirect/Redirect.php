<?php
/**
 * Http Redirect
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   MIT
 */
namespace Molajo\Http\Redirect;

defined('MOLAJO') or die;

use Molajo\Http\Redirect\Exception\RedirectException;

use Molajo\Http\Redirect\Api\RedirectInterface;

/**
 * Http Redirect
 *
 * http://tools.ietf.org/html/rfc2616#section-10.3
 *
 * @package   Molajo
 * @license   MIT
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
Class Redirect implements RedirectInterface
{
    /**
     * $configuration_sef
     *
     * @var    string
     * @since  1.0
     */
    protected $configuration_sef = null;

    /**
     * $base_url
     *
     * @var    string
     * @since  1.0
     */
    protected $base_url = null;

    /**
     * $application_url_path
     *
     * @var    string
     * @since  1.0
     */
    protected $application_url_path = null;

    /**
     * $field_handler_class
     *
     * @var    string
     * @since  1.0
     */
    protected $field_handler_class = null;

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
     * Construct
     *
     * @param   int    $configuration_sef
     * @param   string $base_url
     * @param   string $application_url_path
     * @param   string $fieldHandlerClass
     *
     * @since   1.0
     * @throws  RedirectException
     */
    public function __construct(
        $configuration_sef = 1,
        $base_url = '',
        $application_url_path = '',
        $fieldHandlerClass = 'Molajo\\FieldHandler\\Api\\FieldHandler'
    ) {
        $this->configuration_sef    = $configuration_sef;
        $this->base_url             = $base_url;
        $this->application_url_path = $application_url_path;
        $this->field_handler_class  = $fieldHandlerClass;

        return;
    }

    /**
     * Set the value of the Redirect Url
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0
     * @throws  RedirectException
     */
    public function setUrl($value = '')
    {
        if ($this->configuration_sef == 1) {
            $value = $this->base_url . $this->application_url_path . $value;
        }

        $this->url = $value;

        return $this->url;
    }

    /**
     * Set the Redirect Status Code
     *
     * @param   int $value
     *
     * @return  string
     * @since   1.0
     * @throws  RedirectException
     */
    public function setStatusCode($value = 302)
    {
        $this->status_code = $value;

        return $this->status_code;
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
    public function redirect($url = '', $status_code = 0)
    {
        if ($url == '') {
        } else {
            $this->url = $url;
        }

        if ((string)$this->url == '') {
            throw new RedirectException
            ('Redirect: No Url provided for Redirect.');
        }

        if ((int)$status_code == 0) {
        } else {
            $this->status_code = $status_code;
        }

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
}
