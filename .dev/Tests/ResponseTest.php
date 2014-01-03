<?php
/**
 * Http Redirect Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Tests\Http;

use Molajo\Http\Response;

/**
 * Http Redirect Unit Tests without HTML Body
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
class RedirectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Redirect Object
     */
    protected $redirect = null;

    protected $body = null;

    public function setUp()
    {
        $timezone   = 'UTC';
        $url        = 'http://google.com';
        $status     = 301;
        $this->body = '
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="5; url=http://example.com/">

<title>Redirecting to Example.com</title>
</head>
<body>
Redirecting to <a href="example.com">Example.com</a>.
</body>
</html>';

        $headers = array(
            'Location' => $url,
            'Status'   => $status
        );

        $this->redirect = new RedirectStub(
            $timezone,
            $headers,
            $this->body
        );
    }

    /**
     * Verify the Status Header
     *
     * @covers Molajo\Http\Response::setStatus
     */
    public function testStatus()
    {
        $this->assertEquals('HTTP/1.0 301 Moved Permanently', $this->redirect->testStatusHeader());
    }

    /**
     * Verify the Status Header
     *
     * @covers Molajo\Http\Response::setRedirect
     */
    public function testRedirect()
    {
        $results = $this->redirect->testRedirectHeader();

        $location_header = $results[0];
        $status          = $results[1];

        $this->assertEquals('http://google.com', $location_header);
        $this->assertEquals(301, $status);
    }

    /**
     * Verify Date Formatting
     *
     * @covers Molajo\Http\Response::setRedirect
     */
    public function testDate()
    {
        $string = $this->redirect->testDate();

        $this->assertEquals(29, strlen($string));
    }

    /**
     * Verify Date Formatting
     *
     * @covers Molajo\Http\Response::getDate
     */
    public function testHeaders()
    {
        $test_array = array('Location: http://google.com', 'Status: HTTP/1.0 301 Moved Permanently');

        $headers = $this->redirect->testHeaders();

        $this->assertEquals('Location: http://google.com', $headers[0]);
        $this->assertEquals('Status: HTTP/1.0 301 Moved Permanently', $headers[1]);
        $this->assertEquals('Content-Type: text/html; charset=UTF-8', $headers[2]);
        $this->assertEquals('Language: en-GB', $headers[5]);
        $this->assertEquals('Cache-Control: no-cache, no-store, max-age=0, must-revalidate', $headers[6]);
        $this->assertEquals('Pragma: no-cache', $headers[7]);
        $this->assertEquals('Content-Length: 293', $headers[9]);
    }

    /**
     * Verify Date Formatting
     *
     * @covers Molajo\Http\Response::setRedirect
     */
    public function testBody()
    {
        $string = $this->redirect->testBody();

        $this->assertEquals($this->body, $string);
    }

    /**
     * Verify Date Formatting
     *
     * @covers Molajo\Http\Response::setRedirect
     */
    public function testSend()
    {
        ob_start();
        $this->redirect->send();
        $rendered = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($this->body, $rendered);
    }
}

class RedirectStub extends Response
{
    /**
     * Test Status
     *
     * @return  $this
     * @since   1.0
     */
    public function testStatusHeader()
    {
        parent::setStatus();

        return $this->headers['Status'];
    }

    /**
     * Test Redirect Location
     *
     * @return  $this
     * @since   1.0
     */
    public function testRedirectHeader()
    {
        parent::setRedirect();

        return array($this->headers['Location'], $this->status);
    }

    /**
     * Test Date Formatting
     *
     * @return  $this
     * @since   1.0
     */
    public function testDate()
    {
        return parent::getDate();
    }

    /**
     * Test Date Formatting
     *
     * @return  $this
     * @since   1.0
     */
    public function testHeaders()
    {
        return parent::setHeaders();
    }

    /**
     * Test Date Formatting
     *
     * @return  $this
     * @since   1.0
     */
    public function testBody()
    {
        ob_start();
        parent::sendBody();
        $rendered = ob_get_contents();
        ob_end_clean();

        return $rendered;
    }
}
