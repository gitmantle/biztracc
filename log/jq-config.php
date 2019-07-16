<?php
// ** MySQL settings ** //
session_start();
$moduledb = $_SESSION['s_logdb'];
$host = $_SESSION['s_server'];
define('DB_DSN','mysql:host='.$host.';dbname='.$moduledb);
define('DB_USER', 'infinint_sagana');     // Your MySQL username
define('DB_PASSWORD', 'dun480can'); // ...and password

define('ABSPATH', dirname(__FILE__).'/');

?>
