<?php
session_start();
$itc = $_REQUEST['itemcode'];

$_SESSION['s_itemcode'] = $itc;

$usersession = $_SESSION['usersession'];
include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subscriber = $row['subid'];
$user_id = $row['user_id'];

$serialtable = 'ztmp'.$user_id.'_serialnos';

$findb = $_SESSION['s_findb'];

$db->query("insert into ".$findb.".".$serialtable." select ".$findb.".stkserials.uid,".$findb.".stkserials.itemcode,".$findb.".stkserials.item,".$findb.".stkserials.serialno,".$findb.".stkserials.locationid,".$findb.".stklocs.location,".$findb.".stkserials.ref_no,'N' from ".$findb.".stkserials,".$findb.".stklocs where ".$findb.".stkserials.locationid = ".$findb.".stklocs.uid and ".$findb.".stkserials.sold = '' and ".$findb.".stkserials.itemcode = :itc");
$db->bind(':itc', $itc);
$db->execute();

$db->closeDB();

?>
