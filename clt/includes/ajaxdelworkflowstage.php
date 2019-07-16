
<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);
$hdate = date("Y-m-d");
$ttime = strftime("%H:%M", time());
$pid = $_REQUEST['tid'];

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

$uid = $_SESSION["s_memberid"];

$db->query("select member_id as mid,process from ".$cltdb.".workflow_xref where workflow_xref_id = ".$pid);
$row = $db->single();
extract($row);

$db->query("delete from ".$cltdb.".workflow_xref where workflow_xref_id = ".$pid);
$db->execute();

$db->query("insert into ".$cltdb.".audit (ddate,ttime,user_id,uname,member_id,action) values (:ddate,:ttime,:user_id,:uname,:member_id,:action)");
$db->bind(':ddate', $hdate);
$db->bind(':ttime', $ttime);
$db->bind(':user_id', $user_id);
$db->bind(':uname', $sname);
$db->bind(':member_id', $uid);
$db->bind(':action', 'Delete workflow stage '.$process);

$db->execute();

$db->closeDB();


echo '<script>';
echo 'window.open("","editmembers").jQuery("#mworkflowlist").trigger("reloadGrid");';
echo 'window.open("","editmembers").jQuery("#mstatus").val("Passive");';

echo 'this.close();';
echo '</script>';	
	
?>

