<?php
include_once __DIR__ . '/Bootstrap.php';
ob_start();

$url = 'http://www.example.com';
$code = 302;
$class = 'Molajo\Http\Redirect';
$instance     = new $class($url, $code);
$instance->redirect();
$headers_list = headers_list();
header_remove();
var_dump($headers_list);
?>
