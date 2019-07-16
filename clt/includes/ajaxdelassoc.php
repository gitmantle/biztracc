<?php
session_start();
$associd = $_REQUEST['asid'];
$memid = $_SESSION["s_memberid"];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$cltdb = $_SESSION['s_cltdb'];

$db->query("delete from ".$cltdb.".assoc_xref where member_id = ".$memid." and of_id = ".$associd);
$db->execute();
	
$db->query("delete from ".$cltdb.".assoc_xref where member_id = ".$associd." and of_id = ".$memid);
$db->execute();
		
$db->closeDB();

?>

