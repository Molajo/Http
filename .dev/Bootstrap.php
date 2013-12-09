<?php
/**
 * Unit Testing Bootstrap
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

$base     = substr(__DIR__, 0, strlen(__DIR__) - 5);
$classmap = array();
$classmap = createClassMap($base . '/vendor/commonapi/http', 'CommonApi\\Http\\');
$results  = createClassMap(
    $base . '/vendor/commonapi/exception',
    'CommonApi\\Exception\\'
);
$classmap = array_merge($classmap, $results);

$classmap['Molajo\\Http\\Client']   = $base . '/Source/Client.php';
$classmap['Molajo\\Http\\Redirect'] = $base . '/Source/Redirect.php';
$classmap['Molajo\\Http\\Request']  = $base . '/Source/Request.php';
$classmap['Molajo\\Http\\Response'] = $base . '/Source/Response.php';
$classmap['Molajo\\Http\\Server']   = $base . '/Source/Server.php';
$classmap['Molajo\\Http\\Upload']   = $base . '/Source/Upload.php';
ksort($classmap);

spl_autoload_register(
    function ($class) use ($classmap) {
        if (array_key_exists($class, $classmap)) {
            require_once $classmap[$class];
        }
    }
);
