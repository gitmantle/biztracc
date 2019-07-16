
<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;
$docuid = $_REQUEST['tid'];

require("../../db.php");
$moduledb = $_SESSION['s_clientdb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "select doc from documents where doc_id = ".$docuid;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);


$dir = '../documents/sub_'.$subscriber.'/clients';

$path=$dir.'/'.$doc;
	
@unlink($path);

$query = "delete from documents where doc_id = ".$docuid;
$result = mysql_query($query) or die(mysql_error());

?>

