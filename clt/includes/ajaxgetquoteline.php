<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$table = 'ztmp'.$user_id.'_quote';
	
	$lineno = $_REQUEST['lineno'];

	$cltdb = $_SESSION['s_cltdb'];
	
	$db->query("select itemcode,item,price,unit,quantity,tax,value,taxindex,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,discamount,disctype,note from ".$cltdb.".".$table." where uid = ".$lineno);
	$row = $db->single();
	extract($row);
	$str = $itemcode."~".$item."~".$price."~".$quantity."~".$tax."~".$value."~".$taxindex."~".$sellacc."~".$sellbr."~".$sellsub."~".$purchacc."~".$purchbr."~".$purchsub."~".$groupid."~".$catid.'~'.$unit.'~'.$discamount.'~'.$disctype.'~'.$note;
	echo $str;
	
$db->closeDB();

?>