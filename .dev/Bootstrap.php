<?php
/**
 * Fieldhandler
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
include_once __DIR__ . '/CreateClassMap.php';

if (! defined('PHP_VERSION_ID')) {
    $version = explode('.', phpversion());
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

$base                               = substr(__DIR__, 0, strlen(__DIR__) - 5);
$classmap                           = array();
$classmap                           = createClassMap($base . '/vendor/commonapi/http', 'CommonApi\\Http\\');
$results                            = createClassMap(
    $base . '/vendor/commonapi/exception',
    'CommonApi\\Exception\\'
);
$classmap                                  = array_merge($classmap, $results);

$classmap['Molajo\\Http\\Client']   = $base . '/Client.php';
$classmap['Molajo\\Http\\Redirect'] = $base . '/Redirect.php';
$classmap['Molajo\\Http\\Request']  = $base . '/Request.php';
$classmap['Molajo\\Http\\Response'] = $base . '/Response.php';
$classmap['Molajo\\Http\\Server']   = $base . '/Server.php';
$classmap['Molajo\\Http\\Upload']   = $base . '/Upload.php';
ksort($classmap);

spl_autoload_register(
    function ($class) use ($classmap) {
        if (array_key_exists($class, $classmap)) {
            require_once $classmap[$class];
        }
    }
);

$manual_redirect_test = __DIR__ . '/Tests/RedirectTest.php';
include $manual_redirect_test;
$class = 'Molajo\Http\Tests\RedirectTest';
$instance = new $class();
