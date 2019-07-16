<?php
session_start();
$cust = $_REQUEST['id'];

$usersession = $_SESSION['usersession'];

$findb = $_SESSION['s_findb'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_dns';

$sql = "delete from ".$findb.".".$table;
$db->query($sql);
$db->execute();

$c = explode('~',$cust);
$ac = $c[0];
$sb = $c[1];

$db->query("select ref_no,accountno,sub,ddate,client,totvalue,invoice from ".$findb.".invhead where ref_no like 'D_N%' and invoice not like 'INV%' and accountno = ".$ac." and sub = ".$sb);
$rows = $db->resultset();
if (count($rows) > 0) {
	foreach ($rows as $row) {
		extract($row);
		$db->query("insert into ".$findb.".".$table." (ref_no,accountno,sub,ddate,client,totvalue,invoice) values (:ref_no,:accountno,:sub,:ddate,:client,:totvalue,:invoice)");
		$db->bind(':ref_no', $ref_no);
		$db->bind(':accountno', $accountno);
		$db->bind(':sub', $sub);
		$db->bind(':ddate', $ddate);
		$db->bind(':client', $client);
		$db->bind(':totvalue', $totvalue);
		$db->bind(':invoice', $invoice);
		$db->execute();
		
		$db->query("select locid from ".$findb.".stktrans where ref_no = '".$ref_no."' limit 1");
		$row = $db->single();
		extract($row);
		$db->query("update ".$findb.".".$table." set locid = :locid where ref_no = :ref_no");
		$db->bind(':locid', $locid);
		$db->bind(':ref_no', $ref_no);
		$db->execute();
	}
}

$db->closeDB();

$_SESSION['s_customer'] = $cust;
?>
