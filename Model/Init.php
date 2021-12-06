<?php
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
session_start();
define('DB_USER', 'root');
define('DB_PWD', 'x2~_KaUT.cj#=W3');
define('DB_NAME', 'ig_data');
define('DB_HOST', 'localhost');
define('DB_DSN', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME .'');
?>