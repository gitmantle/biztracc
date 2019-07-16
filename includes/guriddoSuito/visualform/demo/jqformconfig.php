<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$SERVER_HOST = "http://localhost/";        // the host name
$SELF_PATH = "suitophp/visualform/demo/";    // the web path to the project without http
$CODE_PATH = "/var/www/html/suitophp/php/PHPSuito/"; // the physical path to the php files
$DRIVER_PATH = "/var/www/html/suitophp/php/PHPSuito/DBdrivers/"; // path to the database drivers

define('DB_DSN','mysql:host=localhost;dbname=northwind');
define('DB_USER', 'root');     // Your MySQL username
define('DB_PASSWORD', ''); // ...and password
define('DB_DATABASE', 'northwind'); // ...and password


/*
 * In case you vat to use the conn you can include it in the paths
include_once $DRIVER_PATH.'jqGridPdo.php';
$conn = new PDO('mysql:host=localhost;dbname=northwind','root','');
 * 
 */