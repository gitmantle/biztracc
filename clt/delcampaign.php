<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$campid = $_REQUEST['uid'];

include_once("../includes/cltadmin.php");
$oCm = new cltadmin;	

$oCm->uid = $campid;

$oCm->DelCampaign();

$cltdb = $_SESSION['s_cltdb'];

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$hdate = date('Y-m-d');
$ttime = strftime("%H:%M", time());

$db->query("insert into ".$cltdb.".audit (ddate,ttime,user_id,uname,sub_id,member_id,action) values (:ddate,:ttime,:user_id,:uname,:sub_id,:member_id,:action)");
$db->bind(':ddate', $hdate);
$db->bind(':ttime', $ttime);
$db->bind(':user_id', $user_id);
$db->bind(':uname', $sname);
$db->bind(':sub_id', $subid);
$db->bind(':member_id', $campid);
$db->bind(':action', "Deleted campaign id ".$campid);

$db->execute();

$db->closeDB();
	
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Delete Campaign</title>
</head>

<body> 

<?php
	echo '<script>';
	echo 'window.open("","updtcampaigns").jQuery("#campaignlist").trigger("reloadGrid");';
	echo 'this.close();';			
	echo '</script>';
?>

</body>
</html>
