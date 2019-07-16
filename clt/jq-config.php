<?php
// ** MySQL settings ** //
//session_start();
$moduledb = $_SESSION['s_admindb'];
$host = $_SESSION['s_server'];
$dbuser = $_SESSION['s_dbuser'];
define('DB_DSN','mysql:host='.$host.';dbname='.$moduledb);
define('DB_USER', $dbuser);     // Your MySQL username
define('DB_PASSWORD', 'dun480can'); // ...and password

define('ABSPATH', dirname(__FILE__).'/');

?>
