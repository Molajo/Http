<?php
/**
 * Http Response Class
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Response;

defined('MOLAJO') or die;

use DateTime;
use DateTimeZone;
use Molajo\Http\Response\Api\ResponseInterface;
use Molajo\Http\Response\Exception\ResponseException;

/**
 * Http Response Class
 *
 * @author    Amy Stephen
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
class Response implements ResponseInterface
{
    /**
     * HTTP response codes with associated messages
     *
     * 1xx Informational
     * 2xx Successful
     * 3xx Redirection
     * 4xx Client Error
     * 5xx Server Error
     *
     * http://restpatterns.org/
     *
     * @var array
     */
    protected $header_status = array(
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        102 => '102 Processing',
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
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        306 => '306 (Reserved)',
        307 => '307 Temporary Redirect',
        308 => '308 Permanent Redirect',
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
    protected $version = null;

    /**
     * Charset
     *
     * @var    string
     * @since  1.0
     */
    protected $charset = null;

    /**
     * Status Code
     *
     * @var    string
     * @since  1.0
     */
    protected $status_code = null;

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
    protected $mimetype = null;

    /**
     * Date
     *
     * @var    Datetime
     * @since  1.0
     */
    protected $date = null;

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
     * Cachable
     *
     * @var    boolean
     * @since  1.0
     */
    protected $cachable = null;

    /**
     * Fieldhandler Class
     *
     * @var    array
     * @since  1.0
     */
    protected $fieldHandlerClass = null;

    /**
     * Property Array
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array = array(
        'fieldHandlerClass',
        'version',
        'charset',
        'status_code',
        'status_message',
        'mimetype',
        'date',
        'headers',
        'body',
        'length',
        'cachable'
    );

    /**
     * Construct
     *
     * @param   int    $version
     * @param   string $charset
     * @param   int    $status_code
     * @param   string $mimetype
     * @param   null   $date
     * @param   array  $headers
     * @param   null   $body
     * @param   int    $cachable
     * @param   string $fieldHandlerClass
     *
     * @since   1.0
     * @throws  ResponseException
     */
    public function __construct(
        $version = 1,
        $charset = 'UTF-8',
        $status_code = 200,
        $mimetype = 'text/html',
        $date = null,
        $headers = array(),
        $body = null,
        $cachable = 0,
        $fieldHandlerClass = 'Molajo\\FieldHandler\\Adapter'
    ) {

        if ($date === null) {
        } else {
            $this->date = $date;
        }

        $this->setVersion((string)$version);
        $this->setCharset((string)$charset);
        $this->setStatus((int)$status_code);
        $this->setMimetype((string)$mimetype);
        $this->setCachable($cachable);
        $this->setBody($body);

        $this->setHeader('Content-Type');
        $this->setHeader('Date');
        $this->setHeader('Last-Modified');
        $this->setHeader('Expires');
        $this->setHeader('Cache-Control');
        $this->setHeader('Pragma');

//        $this->cookies           = array();
        $this->fieldHandlerClass = $fieldHandlerClass;

        return;
    }

    /**
     * Set the value of a specified key
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  ResponseException
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new ResponseException
            ('Response: Set value for unknown key: ' . $key);
        }

        $this->$key = $value;

        return $this->$key;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   null   $key
     * @param   null   $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  ResponseException
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if ((string)$key === '') {
            $response = array();
            foreach ($this->property_array as $key) {
                $response[$key] = $this->$key;
            }

            return $response;
        }

        if (in_array($key, $this->property_array)) {
        } else {
            throw new ResponseException
            ('Response: Unknown key: ' . $key);
        }

        if ($this->$key === null) {
            $this->$key = $default;
        }

        return $this->$key;
    }

    /**
     * Set HTTP response header - to remove a header, set the key and send in null for value
     *
     * @param   string $key
     * @param   string $value
     * @param   null   $date
     *
     * @return  $this
     * @since   1.0
     * @throws  ResponseException
     */
    public function setHeader($key = '', $value = null, $date = null)
    {
        if ((string)$key == '') {
            throw new ResponseException
            ('Response: No Key value sent in for setHeader');
        }

        if ($value === null) {
            if (isset($this->headers[$key])) {
                unset($this->headers[$key]);
            }
        }

        if ($date === null) {
            if ($this->date === null) {
                $this->date     = new DateTime('GMT');
            } else {
                $date  = $this->date;
            }
        }

        if ($key == 'Content-Type') {
            $this->setContentTypeHeader();

        } elseif ($key == 'Date') {
            $this->setDateHeader($date);

        } elseif ($key == 'Last-Modified') {
            $this->setLastModifiedHeader($date);

        } elseif ($key == 'Expires') {
            $this->setExpiredHeader($date);

        } elseif ($key == 'Cache-Control') {
            $this->setCacheControlHeader();

        } elseif ($key == 'Pragma') {
            $this->setPragmaHeader();

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
     * @param   int    $status_code
     * @param   string $status_message
     *
     * @return  string
     * @since   1.0
     */
    public function setStatus($status_code = 0, $status_message = '')
    {
        if ((int)$status_code === 0) {
            $status_code = 200;
        }

        $this->set('status_code', (int)$status_code);

        if ($status_code == 204
            || $status_code == 304
        ) {
            if (isset($this->headers['Content-Type'])) {
                unset($this->headers['Content-Type']);
            }
        }

        if ((string)$status_message === '') {

            $status_code = (int)$status_code;

            if (isset($this->header_status[$status_code])) {
                $status_message = $this->header_status[$status_code];
            }
        }

        $this->set('status_message', (string)$status_message);

        return $this;
    }

    /**
     * Set the Body
     *
     * @param   string $body
     *
     * @return  $this
     * @since   1.0
     * @throws  ResponseException
     */
    public function setBody($body = null)
    {
        if (is_string($body)
            || is_numeric($body)
            || is_null($body)
        ) {
        } else {
            throw new ResponseException
                ('HttpResponse: Invalid Content');
        }

        $this->body = (string)$body;

        if (isset($this->headers['Content-Length'])) {
            unset($this->headers['Content-Length']);
        }
        $this->headers['Content-Length'] = strlen($this->body);

        return $this;
    }

    /**
     * Sets the charset of this response.
     *
     * @param   int $version
     *
     * @return  $this
     * @since   1.0
     */
    protected function setVersion($version = 1)
    {
        if ((string)$version == '') {
            $version = 1;
        }

        $this->set('version', (string)$version);

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
    protected function setCharset($charset = 'UTF-8')
    {
        if ((string)$charset == '') {
            $charset = 'UTF-8';
        }

        $this->set('charset', (string)$charset);

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
        if ((string)$mimetype == '') {
            throw new ResponseException
                ('Response: No value sent in for setCharset');
        }

        $this->set('mimetype', (string)$mimetype);

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
            $this->set('cachable', 0);
        } else {
            $this->set('cachable', 1);
        }

        return $this;
    }

    /**
     * Set Content Type Header
     *
     * @param   null $value
     *
     * @since   1.0
     * @return  $this
     * @throws  ResponseException
     */
    protected function setContentTypeHeader($value = null)
    {
        if ((string)$value == '') {
            $value = $this->mimetype . '; charset=' . $this->charset;
        }

        $this->headers['Content-Type'] = $value;

        return $this;
    }

    /**
     * Set Date Header
     *
     * @param   DateTime $date
     *
     * @since   1.0
     * @return  $this
     * @throws  ResponseException
     */
    protected function setDateHeader(DateTime $date = null)
    {
        if ($date == null) {
        } else {
            $this->headers['Date'] = $date->format('D, d M Y H:i:s') . ' GMT';
            return $this;
        }

        $date->setTimezone(new DateTimeZone('UTC'));

        $this->headers['Date'] = $date->format('D, d M Y H:i:s') . ' GMT';

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
    protected function setLastModifiedHeader(DateTime $date = null)
    {
        if ($date == null) {
        } else {
            $this->headers['Last-Modified'] = $date->format('D, d M Y H:i:s') . ' GMT';
            return $this;
        }

        $date->setTimezone(new DateTimeZone('UTC'));

        if ($this->cachable == '1') {
            $this->headers['Last-Modified'] = gmdate('D, d M Y H:i:s', time() + 900) . ' GMT';

        } else {
            $this->headers['Last-Modified'] = $date->format('D, d M Y H:i:s') . ' GMT';
        }

        return $this;
    }

    /**
     * Set Expired Date Header
     *
     * @param   DateTime $date
     *
     * @since   1.0
     * @return  $this
     * @throws  ResponseException
     */
    protected function setExpiredHeader(DateTime $date = null)
    {
        if ($date == null) {
        } else {
            $this->headers['Expires'] = $date->format('D, d M Y H:i:s') . ' GMT';
            return $this;
        }

        $date->setTimezone(new DateTimeZone('UTC'));

        if ($this->cachable == '1') {
            $this->headers['Expires'] = 'Mon, 1 Jan 2001 00:00:00 GMT';

        } else {
            $this->headers['Expires'] = gmdate('D, d M Y H:i:s', time() + 900) . ' GMT';
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
        if ((string)$value == '') {
            $value = 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0';
        }

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
    protected function setPragmaHeader($value = '')
    {
        if ((string)$value == '') {
            $value = 'no-cache';
        }

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
        foreach ($this->cookies as $cookie) {
        if (empty($cookie->value)) {
        setcookie(
        $cookie->name,
        '',
        time() - 90000,
        $cookie->path,
        $cookie->domain,
        $cookie->secure,
        $cookie->httponly
        );
        } else {
        setcookie(
        $cookie->name,
        $cookie->value,
        $cookie->expires,
        $cookie->path,
        $cookie->domain,
        $cookie->secure,
        $cookie->httponly
        );
        }
        }
         */

        return $this->get('status_code');
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
        header('HTTP/1.1 ' . $this->status_code . ' ' . $this->status_message);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
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
}
