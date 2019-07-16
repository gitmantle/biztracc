
<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

$docuid = $_REQUEST['tid'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$cltdb = $_SESSION['s_cltdb'];

$db->query("select doc from ".$cltdb.".documents where doc_id = ".$docuid);
$row = $db->single();
extract($row);

$dir = '../documents/sub_'.$subscriber.'/clients';

$path=$dir.'/'.$doc;
	
@unlink($path);

$db->query("delete from ".$cltdb.".documents where doc_id = ".$docuid);
$db->execute();

$db->closeDB();

?>

