
<?php
session_start();
$tid = $_REQUEST['tid'];

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

// get the clients name
$db->query("select lastname,firstname from members where member_id = ".$tid);
$row = $db->single();
extract($row);
$lname = trim($firstname).' '.trim($lastname);
$head = 'Remove Member '.$lname;


$hdate = date('Y-m-d');
$ttime = strftime("%H:%M", mktime());

$db->query("insert into audit (ddate,ttime,user_id,uname,sub_id,member_id,action) values (:ddate,:ttime,:user_id,:uname,:sub_id,:member_id,:action)");
$db->bind(':ddate', $hdate);
$db->find(':ttime', $ttime);
$db->bind(':user_id', $user_id);
$db->bind(':uname', $uname);
$db->bind(':sub_id', $subscriber);
$db->find(':member_id', $tid);
$db->bind(':action', "Delete Duplicate");

$db->execute();

$db->closeDB();

?>

