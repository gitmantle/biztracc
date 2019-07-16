<?php
// ** MySQL settings ** //
//session_start();
$dbuser = $_SESSION['s_dbuser'];
$moduledb = $_SESSION['s_findb'];
$host = $_SESSION['s_server'];
define('DB_DSN','mysql:host='.$host.';dbname='.$moduledb);
define('DB_USER', $dbuser);     // Your MySQL username
define('DB_PASSWORD', 'dun480can'); // ...and password

define('ABSPATH', dirname(__FILE__).'/');

?>
