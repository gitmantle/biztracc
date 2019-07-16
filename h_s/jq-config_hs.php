<?php
// ** MySQL settings ** //
session_start();
$usersession = $_SESSION['usersession'];
$icid = $_SESSION['s_incidentid'];

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$incfile = 'ztmp'.$user_id.'_hs';

$moduledb = $_SESSION['h_sdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select incid,subid,coyid from ".$incfile." where uid = ".$icid;
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$moduledb = 'log'.$subid.'_'.$coyid;

$host = $_SESSION['s_server'];
define('DB_DSN','mysql:host='.$host.';dbname='.$moduledb);
define('DB_USER', 'logtracc9');     // Your MySQL username
define('DB_PASSWORD', 'dun480can'); // ...and password

define('ABSPATH', dirname(__FILE__).'/');

?>
