<?php
/**
 * Http Response Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http;

use DateTime;
use CommonApi\Http\ResponseInterface;
use CommonApi\Exception\UnexpectedValueException;

/**
 * Http Response Class
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Response implements ResponseInterface
{
    /**
     * HTTP response codes with associated messages
     *
     * @link   http://tools.ietf.org/html/rfc2616
     * @link   http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     * @var    array
     * @since  1.0
     */
    protected $header_status = array(

        // 1xx Informational
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        102 => '102 Processing',
        // 2xx Successful
        200 => '200 Successful',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        207 => '207 Multi-Status',
        208 => '208 Already Reported',
        226 => '226 IM Used',
        // 3xx Redirection
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        306 => '306 (Reserved)',
        307 => '307 Temporary Redirect',
        308 => '308 Permanent Redirect',
        // 4xx Client Error
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        407 => '407 Proxy Authentication',
        408 => '408 Request Timeout',
        409 => '409 Conflict',
        410 => '410 Gone',
        411 => '411 Length Required',
        412 => '412 Precondition Failed',
        413 => '413 Request Entity Too Large',
        414 => '414 Request-URI Too Long',
        415 => '415 Unsupported Media Type',
        416 => '416 Requested Range Not Satisfiable',
        417 => '417 Expectation Failed',
        418 => '418 I\'m a teapot',
        422 => '422 Unprocessable Entity',
        423 => '423 Locked',
        424 => '424 Failed Dependency',
        425 => '425 Reserved for WebDAV advanced collections expired proposal',
        426 => '426 Upgrade Required',
        428 => '428 Precondition Required',
        429 => '429 Too Many Requests',
        431 => '431 Request Header Fields Too Large',
        // 5xx Server Error
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Timeout',
        505 => '505 HTTP Version Not Supported',
        507 => '507 Insufficient Storage',
        508 => '508 Loop Detected',
        510 => '510 Not Extended',
        511 => '511 Network Authentication Required'
    );

    /**
     * Status
     *
     * @var    int
     * @since  1.0
     */
    protected $status = 200;

    /**
     * Version
     *
     * @var    string
     * @since  1.0
     */
    protected $version = '1.0';

    /**
     * Url
     *
     * @var    string
     * @since  1.0
     */
    protected $url = null;

    /**
     * Timezone
     *
     * @var    string
     * @since  1.0
     */
    protected $timezone = null;

    /**
     * Headers
     *
     * @var    array
     * @since  1.0
     */
    protected $headers = array();

    /**
     * Formatted Headers
     *
     * @var    array
     * @since  1.0
     */
    protected $formatted_headers = array();

    /**
     * Body
     *
     * @var    string
     * @since  1.0
     */
    protected $body = null;

    /**
     * Construct
     *
     * @param   array $headers
     * @param   null  $body
     *
     * @since   1.0
     */
    public function __construct(
        $timezone = 'UTC',
        array $headers = array(),
        $body = null
    ) {
        $this->timezone = $timezone;

        if (isset($headers['Status'])) {
            $this->status = $headers['Status'];
            unset($headers['Status']);
        } else {
            $this->status = 200;
        }

        if (isset($headers['Version'])) {
            $this->version = $headers['Version'];
            unset($headers['Version']);
        } else {
            $this->version = '1.0';
        }

        if (isset($headers['Location'])) {
            $this->url = $headers['Location'];
            unset($headers['Location']);
            $this->setRedirect();
        }

        $this->setStatus();

        if ($this->status > 0 && $this->status < 200
            || in_array($this->status, array(201, 204, 304))
        ) {
            $this->body = '';
        } else {
            $this->body = (string)$body;
        }

        $content_type = '';
        if ($this->status == 204 || $this->status == 304) {
            $this->body = '';

        } else {
            if (isset($headers['Content-Type'])) {
                $content_type = $headers['Content-Type'];
                unset($headers['Content-Type']);
            } else {
                $content_type = 'text/html';
            }
            $content_type .= ';';
            if (isset($headers['Charset'])) {
                $content_type .= ' charset=' . $headers['Charset'];
                unset($headers['Charset']);
            } else {
                $content_type .= ' charset=UTF-8';
            }
        }
        if (isset($headers['Content-Type'])) {
            unset($headers['Content-Type']);
        }
        if (isset($headers['Charset'])) {
            unset($headers['Charset']);
        }

        if ($this->body === '') {
        } else {
            $this->headers['Content-Type'] = $content_type;
            if (isset($headers['Last-Modified'])) {
                $this->headers['Last-Modified'] = $headers['Last-Modified'];
            } else {
                $this->headers['Last-Modified'] = $this->getDate();
            }

            $this->headers['Date'] = $this->getDate();

            if (isset($headers['Language'])) {
                $this->headers['Language'] = $headers['Language'];
            } else {
                $this->headers['Language'] = 'en-GB';
            }

            if (isset($headers['Cachable']) && $headers['Cachable'] === 1) {
                $this->headers['Cache-Control'] = 'max-age=3600, public';
            } else {
                $this->headers['Cache-Control'] = 'no-cache, no-store, max-age=0, must-revalidate';
                $this->headers['Pragma']        = 'no-cache';
                $this->headers['Expires']       = $this->getDate();
            }
        }

        if (isset($headers['Last-Modified'])) {
            unset($headers['Last-Modified']);
        }
        if (isset($headers['Language'])) {
            unset($headers['Language']);
        }
        if (isset($headers['Cachable'])) {
            unset($headers['Cachable']);
        }

        if (count($headers) > 0) {
            foreach ($headers as $key => $value) {
                $key                 = (string)$key;
                $this->headers[$key] = (string)$value;
            }
        }

        return;
    }

    /**
     * Set Status
     *
     * @return  string
     * @since   1.0
     */
    protected function setStatus()
    {
        if (isset($this->header_status[$this->status])) {
        } else {
            $this->status = 200;
        }

        $this->headers['Status'] = sprintf(
            'HTTP/%s %s',
            $this->version,
            $this->header_status[$this->status]
        );

        return $this->headers['Status'];
    }

    /**
     * Set Redirect
     *
     * @return  string
     * @since   1.0
     */
    protected function setRedirect()
    {
        $this->url = filter_var($this->url, FILTER_SANITIZE_URL);

        if ((int)$this->status > 299 && (int)$this->status < 400) {
        } else {
            $this->status = 301;
        }

        $this->headers['Location'] = htmlspecialchars($this->url, ENT_QUOTES, 'UTF-8');

        return $this->headers['Location'];
    }

    /**
     * Get Date
     *
     * @return  string
     * @since   1.0
     */
    protected function getDate()
    {
        $date = new DateTime();

        $date->setTimezone(new \DateTimeZone($this->timezone));

        return $date->format('D, d M Y H:i:s') . ' GMT';
    }

    /**
     * Send Headers and Body
     *
     * @return  void
     * @since   1.0
     */
    public function send()
    {
        $this->setHeaders();

        $this->sendHeaders();

        if (strlen(trim($this->body)) > 0) {
            $this->sendBody();
        }

        return;
    }

    /**
     * Send Headers
     *
     * @return  array
     * @since   1.0
     */
    protected function setHeaders()
    {
        $this->formatted_headers = array();

        if (isset($this->headers['Content-Length'])) {
            unset($this->headers['Content-Length']);
        }

        if (strlen($this->body) > 0) {
            $this->headers['Content-Length'] = strlen($this->body);
        }

        foreach ($this->headers as $name => $values) {
            $header_values = explode("\n", $values);
            foreach ($header_values as $value) {
                $this->formatted_headers[] = "$name: $value";
            }
        }

        return $this->formatted_headers;
    }

    /**
     * Send Headers
     *
     * @return  $this
     * @since   1.0
     */
    public function sendHeaders()
    {
        if (headers_sent()) {
            return $this;
        }

        foreach ($this->formatted_headers as $header) {
            header($header, false);
        }

        return $this;
    }

    /**
     * Send Headers
     *
     * @return  void
     * @since   1.0
     */
    public function sendBody()
    {
        echo (string)$this->body;

        return;
    }
}
