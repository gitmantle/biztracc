<?php
session_start();

$coyid = $_SESSION['s_coyid'];

// create date ranges
date_default_timezone_set($_SESSION['s_timezone']);

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select lstatdt, pstatdt, lastmedate from ".$findb.".globals");
$row = $db->single();

extract($row);
$curdat = $lstatdt;
$monthend = $lastmedate;

$lastcur = date("Y-m-d", strtotime($curdat));
$lastd30 = date("Y-m-d", strtotime($lastcur." -1 month"));
$lastd60 = date("Y-m-d", strtotime($lastd30." -1 month"));
$lastd90 = date("Y-m-d", strtotime($lastd60." -1 month"));
$lastd120 = date("Y-m-d", strtotime($lastd90." -1 month"));

$newcur = date("Y-m-d", strtotime($lastcur." +1 month"));
$newd30 = $lastcur;
$newd60 = $lastd30;
$newd90 = $lastd60;
$newd120 = $lastd90;

// age Debtor and Creditor aged balances by one month

$db->query("update ".$cltdb.".client_company_xref set d120 = d120 +d90");
$db->execute();

$db->query("update ".$cltdb.".client_company_xref set d90 = d60");
$db->execute();

$db->query("update ".$cltdb.".client_company_xref set d60 = d30");
$db->execute();

$db->query("update ".$cltdb.".client_company_xref set d30 = current");
$db->execute();

$db->query("update ".$cltdb.".client_company_xref set current = 0");
$db->execute();

// update dates in globals

$tday = date("Y-m-d");

$db->query("update ".$findb.".globals set lastmedate = '".$tday."'");
$db->execute();

$db->closeDB();

echo 'Aged balances updated.';
return;


?>
