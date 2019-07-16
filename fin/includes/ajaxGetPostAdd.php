<?php
	session_start();
	$cid = $_REQUEST['cid'];

	$cltdb = $_SESSION['s_cltdb'];
	
include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select addresses.street_no,addresses.ad1,addresses.ad2,addresses.suburb,addresses.town,addresses.postcode,addresses.billing from ".$cltdb.".addresses where addresses.member_id = ".$cid." and billing = 'Y'");
$rows = $db->resultset();
$postadd = "";
foreach ($rows as $row) {
	extract($row);
	
	if ($street_no.$ad1 <> '') {
		$add .= str_replace(',',' ',trim($street_no." ".$ad1." ".$ad2));
	}
	if ($suburb <> '') {
		$add .= ','.str_replace(',',' ',trim($suburb));
	}
	if ($town <> '') {
		$add .= ','.str_replace(',',' ',trim($town));
	}
	if ($postcode <> '') {
		$add .= ','.trim($postcode);
	}
	
	if ($billing == 'Y') {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$postadd .= '<option value="'.$add.'"'.$selected.'>'.$add.'</option>';
}				

echo $postadd;

$db->closeDB();

?>