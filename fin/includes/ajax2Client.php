<?php
session_start();
$acc = $_REQUEST['acc'];
$asb = $_REQUEST['asb'];
$coyid = $_SESSION['s_coyid'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$cltdb = $_SESSION['s_cltdb'];

// get member id 
$db->query("select client_id from ".$cltdb.".client_company_xref where company_id = ".$coyid." and drno = ".$acc." and drsub = ".$asb);
$row = $db->single();
extract($row);
$cid = $client_id;

$db->query("select status from ".$cltdb.".members where member_id = ".$cid);
$row = $db->single();
extract($row);

if ($status == 'Lead' || $status == 'Prospect') {
	$db->query("update ".$cltdb.".members set status = 'Client' where member_id = ".$cid);
	$db->execute();
}

$db->closeDB();

?>

