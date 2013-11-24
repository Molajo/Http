<?php
/**
 * Molajo FileUpload Package Bootstrap
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
define('BASE_FOLDER', substr(__DIR__, 0, strlen(__DIR__) - 5));

$classMap = array(
    'Molajo\\FileUpload\\CommonApi\\UploadInterface'       => BASE_FOLDER . '/Api/UploadInterface.php',
    'Molajo\\FileUpload\\CommonApi\\ExceptionInterface'    => BASE_FOLDER . '/Api/ExceptionInterface.php',
    'Molajo\\FileUpload\\Exception\\UploadException' => BASE_FOLDER . '/Exception/UploadException.php',
    'Molajo\\FileUpload\\Upload'                     => BASE_FOLDER . '/Upload.php',
    'Molajo\\FileUpload\\Test\\UploadTest'           => BASE_FOLDER . '/Tests/UploadTest.php',
);

spl_autoload_register(
    function ($class) use ($classMap) {
        if (array_key_exists($class, $classMap)) {
            require_once $classMap[$class];
        }
    }
);
