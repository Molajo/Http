<?php
/**
 * Http
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   MIT
 */
define('MOLAJO', 'This is a Molajo Distribution');

if (substr($_SERVER['DOCUMENT_ROOT'], - 1) == '/') {
    define('ROOT_FOLDER', $_SERVER['DOCUMENT_ROOT']);
} else {
    define('ROOT_FOLDER', $_SERVER['DOCUMENT_ROOT'] . '/');
}

$base = substr(__DIR__, 0, strlen(__DIR__) - 5);
define('BASE_FOLDER', $base);

//include BASE_FOLDER . '/Tests/Testcase1/Data.php';

$classMap = array(
    'Molajo\\Http\\Client\\Api\\ClientInterface'           => BASE_FOLDER . '/Client/Api/ClientInterface.php',
    'Molajo\\Http\\Client\\Exception\\ClientException'         => BASE_FOLDER . '/Client/Exception/ClientException.php',
    'Molajo\\Http\\Client\\Exception\\ExceptionInterface'      => BASE_FOLDER . '/Client/Exception/ExceptionInterface.php',
    'Molajo\\Http\\Client\\Client'                             => BASE_FOLDER . '/Client/Client.php',
    'Molajo\\Http\\FileUpload\\Api\\FileUploadInterface'   => BASE_FOLDER . '/FileUpload/Api/FileUploadInterface.php',
    'Molajo\\Http\\FileUpload\\Exception\\FileUploadException' => BASE_FOLDER . '/FileUpload/Exception/FileUploadException.php',
    'Molajo\\Http\\FileUpload\\Exception\\ExceptionInterface'  => BASE_FOLDER . '/FileUpload/Exception/ExceptionInterface.php',
    'Molajo\\Http\\FileUpload\\FileUpload'                     => BASE_FOLDER . '/FileUpload/FileUpload.php',
    'Molajo\\Http\\Redirect\\Api\\RedirectInterface'       => BASE_FOLDER . '/Redirect/Api/RedirectInterface.php',
    'Molajo\\Http\\Redirect\\Exception\\RedirectException'     => BASE_FOLDER . '/Redirect/Exception/RedirectException.php',
    'Molajo\\Http\\Redirect\\Exception\\ExceptionInterface'    => BASE_FOLDER . '/Redirect/Exception/ExceptionInterface.php',
    'Molajo\\Http\\Redirect\\Redirect'                         => BASE_FOLDER . '/Redirect/Redirect.php',
    'Molajo\\Http\\Request\\Api\\RequestInterface'         => BASE_FOLDER . '/Request/Api/RequestInterface.php',
    'Molajo\\Http\\Request\\Exception\\RequestException'       => BASE_FOLDER . '/Request/Exception/RequestException.php',
    'Molajo\\Http\\Request\\Exception\\ExceptionInterface'     => BASE_FOLDER . '/Request/Exception/ExceptionInterface.php',
    'Molajo\\Http\\Request\\Request'                           => BASE_FOLDER . '/Request/Request.php',
    'Molajo\\Http\\Response\\Api\\ResponseInterface'       => BASE_FOLDER . '/Response/Api/ResponseInterface.php',
    'Molajo\\Http\\Response\\Exception\\ResponseException'     => BASE_FOLDER . '/Response/Exception/ResponseException.php',
    'Molajo\\Http\\Response\\Exception\\ExceptionInterface'    => BASE_FOLDER . '/Response/Exception/ExceptionInterface.php',
    'Molajo\\Http\\Response\\Response'                         => BASE_FOLDER . '/Response/Response.php',
    'Molajo\\Http\\Server\\Api\\ServerInterface'           => BASE_FOLDER . '/Server/Api/ServerInterface.php',
    'Molajo\\Http\\Server\\Exception\\ServerException'         => BASE_FOLDER . '/Server/Exception/ServerException.php',
    'Molajo\\Http\\Server\\Exception\\ExceptionInterface'      => BASE_FOLDER . '/Server/Exception/ExceptionInterface.php',
    'Molajo\\Http\\Server\\Server'                             => BASE_FOLDER . '/Server/Server.php'
);

spl_autoload_register(
    function ($class) use ($classMap) {
        if (array_key_exists($class, $classMap)) {
            require_once $classMap[$class];
        }
    }
);

/*
include BASE_FOLDER . '/' . 'ClassLoader.php';
$loader = new ClassLoader();
$loader->add('Molajo', BASE_FOLDER . '/src/');
$loader->add('Testcase1', BASE_FOLDER . '/Tests/');
$loader->register();
*/
