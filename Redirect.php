<?php
/**
 * Http Redirect Class
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Http;

use CommonApi\Http\RedirectInterface;
use CommonApi\Exception\UnexpectedValueException;

/**
 * Http Redirect Class
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
     * Url
     *
     * @var    string
     * @since  1.0
     */
    protected $url = null;

    /**
     * Status Code
     *
     * @var    integer
     * @since  1.0
     */
    protected $status_code = 0;

    /**
     * Status Message
     *
     * @var    string
     * @since  1.0
     */
    protected $status_message = '';

    /**
     * HTTP response codes with associated messages
     *
     * @link   http://tools.ietf.org/html/rfc2616
     * @var    array
     * @since  1.0
     */
    protected $header_status = array(
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        306 => '306 (Reserved)',
        307 => '307 Temporary Redirect',
        308 => '308 Permanent Redirect'
    );

    /**
     * Construct
     *
     * @param   string $url
     * @param   int    $status_code
     *
     * @since   1.0
     */
    public function __construct(
        $url = '\\',
        $status_code = 301
    ) {
        $this->url         = $url;
        $this->status_code = $status_code;
    }

    /**
     * Sets Redirect Headers for Application
     *
     * @return  $this
     * @throws  \CommonApi\Exception\UnexpectedValueException
     * @since   0.1
     */
    public function redirect()
    {
        $this->url = filter_var($this->url, FILTER_SANITIZE_URL);

        if ($this->url === false || (string)$this->url === '' || $this->url === null) {
            throw new UnexpectedValueException
            ('Redirect: Invalid or No Url provided for Redirect.');
        }

        if ((int)$this->status_code === 0) {
            $this->status_code = 301;
        }

        if (isset($this->header_status[$this->status_code])) {
        } else {
            $this->status_code = 301;
        }

        $this->status_message = $this->header_status[$this->status_code];

        header('Status: ' . $this->status_message);
        header("Location: " . htmlspecialchars($this->url, ENT_QUOTES, 'UTF-8'));

        return $this;
    }
}
