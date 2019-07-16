<?php
// ** MySQL settings ** //
session_start();
$moduledb = $_SESSION['h_sdb'];
$host = $_SESSION['s_server'];
define('DB_DSN','mysql:host='.$host.';dbname='.$moduledb);
define('DB_USER', 'logtracc9');     // Your MySQL username
define('DB_PASSWORD', 'dun480can'); // ...and password

define('ABSPATH', dirname(__FILE__).'/');

?>
