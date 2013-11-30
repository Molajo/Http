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
     * @param   string $url
     * @param   int    $status_code
     *
     * @since   1.0
     */
    public function __construct(
        $url = '',
        $status_code = 0
    ) {
        $this->url          = $url;
        $this->status_code  = $status_code;
    }

    /**
     * Redirect Application
     *
     * @return  void
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
