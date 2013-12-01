<?php
/**
 * Http Response Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Tests;

use Molajo\Http\Response;

/**
 * Http Response Unit Tests
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Gets the Response Object
     *
     * @covers Molajo\Http\Response::get
     */
    public function testSend()
    {
        $version      = '1.0';
        $charset      = 'utf-8';
        $status_code  = 200;
        $content_type = 'text/html';
        $expires_date = 'Fri, 14 Sep 2012 01:52:00 GMT';
        $cachable     = 0;
        $language     = 'en-GB';
        $headers      = array();
        $body         = '<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>Test</title>
    </head>
    <body>
        <p>Stuff goes here.</p>
    </body>
</html>';

        $instance = new Response(
            $version,
            $charset,
            $status_code,
            $content_type,
            $expires_date,
            $cachable,
            $language,
            $headers,
            $body
        );

        ob_start();
        $response = $instance->send();
        $rendered_page = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($body, $rendered_page);

        return $this;
    }
}
