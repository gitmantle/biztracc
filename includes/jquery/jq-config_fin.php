<?php
// ** MySQL settings ** //
//define('DB_NAME', 'northwind');    // The name of the database
//define('DB_HOST', 'localhost');    // 99% chance you won't need to change this value
$host = $_SESSION['s_server'];
define('DB_DSN','mysql:host='.$host.';dbname='.$moduledb);
define('DB_USER', 'logtracc9');     // Your MySQL username
define('DB_PASSWORD', 'dun480can'); // ...and password

define('ABSPATH', dirname(__FILE__).'/');
//require_once(ABSPATH.'tabs.php');
?>
