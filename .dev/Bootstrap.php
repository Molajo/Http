<?php
/**
 * Unit Testing Bootstrap
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
$base = substr(__DIR__, 0, strlen(__DIR__) - 5);
if (function_exists('CreateClassMap')) {
} else {
    include_once __DIR__ . '/CreateClassMap.php';
}
include_once $base . '/vendor/autoload.php';

$classmap['Molajo\\Http\\Client']             = $base . '/Source/Client.php';
$classmap['Molajo\\Http\\Request']            = $base . '/Source/Request.php';
$classmap['Molajo\\Http\\Request\\Scheme']    = $base . '/Source/Request/Scheme.php';
$classmap['Molajo\\Http\\Request\\Authority'] = $base . '/Source/Request/Authority.php';
$classmap['Molajo\\Http\\Request\\Query']     = $base . '/Source/Request/Query.php';
$classmap['Molajo\\Http\\Request\\Path']      = $base . '/Source/Request/Path.php';
$classmap['Molajo\\Http\\Response']           = $base . '/Source/Response.php';
$classmap['Molajo\\Http\\Server']             = $base . '/Source/Server.php';
$classmap['Molajo\\Http\\Upload']             = $base . '/Source/Upload.php';

spl_autoload_register(
    function ($class) use ($classmap) {
        if (array_key_exists($class, $classmap)) {
            require_once $classmap[$class];
        }
    }
);
