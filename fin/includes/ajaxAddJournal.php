<?php
session_start();
$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_journal';


$jaccount = $_REQUEST['account'];
$jnote = $_REQUEST['descript'];
$jdebit = $_REQUEST['debit'];
$jcredit = $_REQUEST['credit'];
$jaccno = $_REQUEST['accno'];
$jsubac = $_REQUEST['subac'];
$jbrac = $_REQUEST['brac'];
$jddate = $_REQUEST['ddate'];
$jreference = $_REQUEST['reference'];
$jdrgst = $_REQUEST['drgst'];


$findb = $_SESSION['s_findb'];

$db->query("insert into ".$findb.".".$table." (account,note,debit,credit,accno,subac,brac,ddate,reference,drgst,crgst) values (:account,:note,:debit,:credit,:accno,:subac,:brac,:ddate,:reference,:drgst,:crgst)");
$db->bind(':account', $jaccount);
$db->bind(':note', $jnote);
$db->bind(':debit', $jdebit);
$db->bind(':credit', $jcredit);
$db->bind(':accno', $jaccno);
$db->bind(':subac', $jsubac);
$db->bind(':brac', $jbrac);
$db->bind(':ddate', $jddate);
$db->bind(':reference', $jreference);
$db->bind(':drgst', $jdrgst);
$db->bind(':crgst', $jdrgst);

$db->execute();
$db->closeDB();


?>
