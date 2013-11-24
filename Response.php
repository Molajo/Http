<?php
/**
 * Http Response Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http;

use DateTime;
use DateTimeZone;
use CommonApi\Http\ResponseInterface;
use CommonApi\User\CookieInterface;
use CommonApi\User\SessionInterface;
use Exception\Http\ResponseException;
use CommonApi\Model\FieldhandlerInterface;

/**
 * Http Response Class
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Response implements ResponseInterface
{
    /**
     * HTTP response codes with associated messages
     *
     * http://restpatterns.org/
     * http://tools.ietf.org/html/rfc2616
     *
     * @var array
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
     * Version
     *
     * @var    string
     * @since  1.0
     */
    protected $version = '1.0';

    /**
     * Charset
     *
     * @var    string
     * @since  1.0
     */
    protected $charset = 'utf-8';

    /**
     * Status Code
     *
     * @var    string
     * @since  1.0
     */
    protected $status_code = 200;

    /**
     * Status Message
     *
     * @var    string
     * @since  1.0
     */
    protected $status_message = null;

    /**
     * Mimetype
     *
     * @var    string
     * @since  1.0
     */
    protected $mimetype = 'text/html';

    /**
     * Expires Date
     *
     * @var    string
     * @since  1.0
     */
    protected $expires_date = null;

    /**
     * Version
     *
     * @var    string
     * @since  1.0
     */
    protected $language = 'en-GB';

    /**
     * Cachable
     *
     * @var    boolean
     * @since  1.0
     */
    protected $cachable = null;

    /**
     * HTTP response headers
     *
     * @var    array
     * @since  1.0
     */
    protected $headers = array();

    /**
     * Body
     *
     * @var    string
     * @since  1.0
     */
    protected $body = null;

    /**
     * Length
     *
     * @var    int
     * @since  1.0
     */
    protected $length = 0;

    /**
     * Fieldhandler Instance
     *
     * @var    object CommonApi\User\CookieInterface
     * @since  1.0
     */
    protected $cookie = null;

    /**
     * Fieldhandler Instance
     *
     * @var    object CommonApi\User\SessionInterface;
     * @since  1.0
     */
    protected $session = null;

    /**
     * Fieldhandler Instance
     *
     * @var    object CommonApi\Model\FieldhandlerInterface
     * @since  1.0
     */
    protected $fieldhandler = null;

    /**
     * Construct
     *
     * @param   int                   $version
     * @param   string                $charset
     * @param   int                   $status_code
     * @param   string                $mimetype
     * @param   DateTime              $date
     * @param   int                   $cachable
     * @param   string                $language
     * @param   null                  $body
     * @param   FieldhandlerInterface $fieldhandler
     *
     * @since   1.0
     * @throws  ResponseException
     */
    public function __construct(
        $version = '1.0',
        $charset = 'utf-8',
        $status_code = 200,
        $mimetype = 'text/html',
        $expires_date = 'Fri, 14 Sep 2012 01:52:00 GMT',
        $cachable = 0,
        $language = 'en-GB',
        $body = null,
        FieldhandlerInterface $fieldhandler
    ) {
        if ((string)$version === '') {
            $version = '1.0';
        }
        $this->setVersion((string)$version);

        /** Content Type Defaults */
        if ((string)$charset === '') {
            $this->setCharset('utf-8');
        } else {
            $this->setCharset((string)$charset);
        }

        if ((int)$status_code === 0) {
            $this->setStatusCode(200);
        } else {
            $this->setStatusCode((int)$status_code);
        }

        if ((string)$mimetype === '') {
            $this->setMimetype('text/html');
        } else {
            $this->setMimetype((string)$mimetype);
        }
        $this->setHeader('Content-Type', $mimetype);

        /** Cacheable */
        if ((int)$cachable === 0) {
            $this->setCachable($cachable);
        }

        /** Language */
        if ($language === null) {
            $this->language = 'en-GB';
        } else {
            $this->language = $language;
        }

        $this->setHeader('Content-Language', $this->language);

        $this->setBody((string)$body);

        $this->cookies      = array();
        $this->fieldhandler = $fieldhandler;

        return;
    }

    /**
     * Set HTTP response header
     *
     * JSON Response
     *
     * $this->setHeader('Content-Type', 'application/json');
     *
     * Download a PDF
     *
     * $this->setHeader('Content-Type', 'application/pdf');
     * $this->setHeader('Content-Disposition', 'attachment; filename="downloaded.pdf"');
     *
     * @param   string      $key
     * @param   null|string $value
     *
     * @return  $this
     * @since   1.0
     * @throws  ResponseException
     */
    public function setHeader($key, $value = null)
    {
        if ($value === null) {
            if (isset($this->headers[$key])) {
                unset($this->headers[$key]);
            }
            return $this;
        }

        if ($key == 'Content-Type') {
            $this->setContentType($value);

        } elseif ($key == 'Last-Modified') {
            $this->setLastModifiedHeader($value);

        } elseif ($key == 'Expires') {
            $this->setExpiresHeader($value);

        } elseif ($key == 'cacheable') {
            $this->setCacheControlHeader();

        } else {
            $this->headers[$key] = $value;
        }

        return $this;
    }

    /**
     * Set Status Code
     *
     * RFC1945 (HTTP/1.0), RFC2616 (HTTP/1.1), and RFC2518 (WebDAV)
     *
     * @param   int $status_code
     *
     * @return  $this
     * @since   1.0
     * @throws  \Exception\Http\ResponseException
     */
    public function setStatusCode($status_code = 200)
    {
        if ((int)$status_code === 0) {
            $status_code = 200;
        }

        $this->status_code = (int)$status_code;

        if ($this->status_code == 204
            || $this->status_code == 304
        ) {
            if (isset($this->headers['Content-Type'])) {
                unset($this->headers['Content-Type']);
            }
        }

        if (isset($this->header_status[$this->status_code])) {
            $this->status_message = $this->header_status[$this->status_code];
            return $this;
        }

        throw new ResponseException ('Http Response Status: ' . $status_code . 'is unknown.');
    }

    /**
     * Set the Body
     *
     * @param   string $body
     *
     * @return  $this
     * @since   1.0
     * @throws  \Exception\Http\ResponseException
     */
    public function setBody($body)
    {
        if (is_string($body)
            || is_numeric($body)
            || is_null($body)
        ) {
        } else {
            throw new ResponseException ('Httpresponse: Invalid Content');
        }

        $this->body = (string)$body;

        if (isset($this->headers['Content-Length'])) {
            unset($this->headers['Content-Length']);
        }
        $this->headers['Content-Length'] = strlen($this->body);

        return $this;
    }

    /**
     * Set HTTP response header
     * Sets the defined browser cookie. Defaults to one year expiry on the root domain.
     *
     * @param   string $key
     * @param   string $value
     * @param   string $date
     *
     * @return  $this
     * @since   1.0
     * @throws  \Exception\Http\ResponseException
     */
    public function setCookie($key = '', $value = null, $date = null)
    {

    }

    /**
     * Removes the defined browser cookie.
     *
     * @param   string $key
     *
     * @return  $this
     * @since   1.0
     * @throws  \Exception\Http\ResponseException
     */
    public function unsetCookie($key)
    {

    }

    /**
     * Send Headers and Body
     *
     * @return  string
     * @since   1.0
     * @throws  ResponseException
     */
    public function send()
    {
        if ($this->canHaveBody()) {
            $this->length = strlen($this->body);
        } else {
            $this->body   = '';
            $this->length = 0;
        }

        if (headers_sent()) {
        } else {
            $this->sendHeaders();
        }

        $this->sendBody();

        /**
         * foreach ($this->cookies as $cookie) {
         * if (empty($cookie->value)) {
         * setcookie(
         * $cookie->name,
         * '',
         * time() - 90000,
         * $cookie->path,
         * $cookie->domain,
         * $cookie->secure,
         * $cookie->httponly
         * );
         * } else {
         * setcookie(
         * $cookie->name,
         * $cookie->value,
         * $cookie->expires,
         * $cookie->path,
         * $cookie->domain,
         * $cookie->secure,
         * $cookie->httponly
         * );
         * }
         * }
         */

        return $this->status_code;
    }

    /**
     * Sets the charset of this response.
     *
     * @param   int $version
     *
     * @return  $this
     * @since   1.0
     */
    protected function setVersion($version = '1.0')
    {
        $this->version = (string)$version;

        return $this;
    }

    /**
     * Sets the charset of this response.
     *
     * @param   string $charset
     *
     * @return  $this
     * @since   1.0
     */
    protected function setCharset($charset = 'utf-8')
    {
        $this->charset = (string)$charset;

        return $this;
    }

    /**
     * Sets the mimetype of this response.
     *
     * @param   string $mimetype
     *
     * @return  $this
     * @since   1.0
     * @throws  ResponseException
     */
    protected function setMimetype($mimetype = 'text/html')
    {
        $this->mimetype = (string)$mimetype;

        return $this;
    }

    /**
     * Set Cachable
     *
     * @param   int $cachable
     *
     * @return  $this
     * @since   1.0
     */
    protected function setCachable($cachable = 1)
    {
        if ((int)$cachable == 0) {
            $this->cachable = 0;
            $this->setHeader('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $this->setHeader('Pragma', 'no-cache');
            $this->setHeader('Expires', $this->expires_date);
        } else {
            $this->cachable = 1;
            $this->setHeader('Cache-Control', 'max-age=3600, public');
        }

        return $this;
    }

    /**
     * Set Content Type Header
     *
     * @param   string $value
     *
     * @since   1.0
     * @return  $this
     * @throws  ResponseException
     */
    protected function setContentType($value)
    {
        if ($this->charset === null || $this->charset == '') {
            $this->setCharset('utf-8');
        }

        if ((string)$value == '') {
            $value = $this->mimetype . '; charset=' . $this->charset;
        }

        $this->headers['Content-Type'] = $value;

        return $this;
    }

    /**
     * Set Last Modified Datetime Header
     *
     * @param   DateTime $date
     *
     * @since   1.0
     * @return  $this
     * @throws  ResponseException
     */
    protected function setLastModifiedHeader($last_modified_date)
    {
        if ($this->cachable == '1') {
            $this->headers['Last-Modified'] = gmdate('D, d M Y H:i:s', time() + 900) . ' GMT';
        } else {
            $this->headers['Last-Modified'] = $last_modified_date;
        }

        return $this;
    }

    /**
     * Set Expires Date Header
     *
     * @param   string $expires_date
     *
     * @since   1.0
     * @return  $this
     * @throws  ResponseException
     */
    protected function setExpiresHeader($expires_date)
    {
        if ($this->cachable == '1') {
            $this->headers['Expires'] = 'Mon, 1 Jan 2001 00:00:00 GMT';
        } else {
            $this->headers['Expires'] = $expires_date;
        }

        return $this;
    }

    /**
     * Cache-Control
     *
     * @param   string $value
     *
     * @since   1.0
     * @return  $this
     * @throws  ResponseException
     */
    protected function setCacheControlHeader($value = '')
    {
        if ($this->cachable == '1') {
            $this->headers['Cache-Control'] = $value;
        }

        return $this;
    }

    /**
     * Pragma
     *
     * @param   string $value
     *
     * @since   1.0
     * @return  $this
     * @throws  ResponseException
     */
    protected function setPragmaHeader($value)
    {
        if ($this->cachable == '1') {
            $this->headers['Pragma'] = $value;
        }

        return $this;
    }

    /**
     * Send Headers and Body
     *
     * @return  string
     * @since   1.0
     * @throws  ResponseException
     */
    protected function sendHeaders()
    {
        if (strpos(PHP_SAPI, 'cgi') === 0) {
            header('Status: ' . $this->status_code . ' ' . $this->status_message);
        } else {
            $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
            header($protocol . ' ' . $this->status_code . ' ' . $this->status_message);
        }

        foreach ($this->headers as $name => $values) {
            $header_values = explode("\n", $values);
            foreach ($header_values as $value) {
                header("$name: $value", false);
            }
        }

        flush();
    }

    /**
     * Send Headers and Body
     *
     * @return  string
     * @since   1.0
     * @throws  ResponseException
     */
    protected function sendBody()
    {
        if ($this->canHaveBody()) {
            $this->length = strlen($this->body);
        } else {
            $this->body   = '';
            $this->length = 0;
        }

        echo $this->body;

        flush();
    }

    /**
     * Can Have Body
     *
     * @return  string
     * @since   1.0
     * @throws  ResponseException
     */
    protected function canHaveBody()
    {
        return true;
    }

    /**
     * Can Have Body
     *
     * @return  string
     * @since   1.0
     * @throws  ResponseException
     */
    public function redirect($url = '', $redirect_code = 302)
    {
        header("Location: {$url}");

        exit;
    }
}
