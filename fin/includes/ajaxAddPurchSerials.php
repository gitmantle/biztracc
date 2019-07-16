<?php
session_start();
//ini_set('display_errors', true);

$refno = $_REQUEST['ref'];

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$serialtable = 'ztmp'.$user_id.'_serialnos';

$findb = $_SESSION['s_findb'];

$db->query("insert into ".$findb.".".$serialtable." select stkserials.uid,stkserials.itemcode,stkserials.item,stkserials.serialno,stkserials.locationid,stklocs.location,stkserials.ref_no,'Y' from ".$findb.".stkserials,".$findb.".stklocs where stkserials.locationid = stklocs.uid and stkserials.ref_no = '".$refno."' and (sold = '' and substring(sold,0,3) != 'CRN')");
$db->execute();

$db->closeDB();

?>
