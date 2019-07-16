<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subscriber = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];

$table = 'ztmp'.$user_id.'_trading';
	
	$lineno = $_REQUEST['lineno'];
	
	$db->query("select itemcode,item,price,unit,quantity,tax,value,taxindex,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,discamount,disctype from ".$findb.".".$table." where uid = :lineno");
	$db->bind(':lineno', $lineno);
	$row = $db->single();
	extract($row);
	$str = $itemcode."~".$item."~".$price."~".$quantity."~".$tax."~".$value."~".$taxindex."~".$sellacc."~".$sellbr."~".$sellsub."~".$purchacc."~".$purchbr."~".$purchsub."~".$groupid."~".$catid.'~'.$unit.'~'.$discamount.'~'.$disctype;
	echo $str;
	
$db->closeDB();

?>