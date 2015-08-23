<?php
/**
 * Unit Testing for Redirect (Manual -- fire up in a browser)
 *
 * Redirect Unit Testing cannot be done using phpUnit, error message follows:
 *
 * 1) Molajo\Tests\Http\RedirectTest::testGet
 *
 * Cannot modify header information - headers already sent by (output started
 *  at /usr/local/php5-5.4.15-20130520-154451/lib/php/PHPUnit/Util/Printer.php:172)
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
include_once __DIR__ . '/Bootstrap.php';

ob_start();

// Set Redirect Parameters
$url  = 'http://www.example.com';
$code = 301;

// Create Redirect Class
$class    = 'Molajo\Http\Redirect';
$instance = new $class($url, $code);

// Sets Headers
$instance->redirect();

// Retrieve and Remove Headers
$headers_list = headers_list();
header_remove();

// Compare to Expected Results
if (in_array("Status: 301 Moved Permanently", $headers_list)) {
    echo 'Redirect Test Success: Status header <br />';
} else {
    echo 'Redirect Test Failed: Status header incorrect <br />';
}

if (in_array("Location: http://www.example.com", $headers_list)) {
    echo 'Redirect Test Success: Location header <br />';
} else {
    echo 'Redirect Test Failed: Location Header incorrect <br />';
}
