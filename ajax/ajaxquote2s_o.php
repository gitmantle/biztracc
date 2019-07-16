<?php
session_start();
$qref = $_REQUEST['qref'];
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$staffname = $uname;

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select * from quotes where ref_no = '".$qref."'";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$ddate = date('Y-m-d');
$ref_no = 'S_O'.$invno;
$tid = $member_id;
$coyidno = $coy_id;

// if accountno = 0 add client as debtor of relevant company
if ($accountno == 0) {
	$dracno = 30000000 + $tid;
	
	$query = "select uid from client_company_xref where company_id = ".$coyidno." and drno = ".$dracno;
	$result = mysql_query($query) or die($query);
	
	$query = "select lastname from members where member_id = ".$tid;
	$result = mysql_query($query) or die($query);
	$row = mysql_fetch_array($result);
	extract($row);
	
		
	$SQLString = "insert into client_company_xref (client_id,company_id,drno,sortcode,member) values ";
	$SQLString .= "(".$tid.",";
	$SQLString .= $coyidno.",";
	$SQLString .= $dracno.",'";
	$SQLString .= $lastname.$dracno."-0','";
	$SQLString .= $lastname."')";
	
	$result = mysql_query($SQLString) or die(mysql_error().' - '.$SQLString);
	
}

$q = "insert into quotes (member_id,coy_id,accountno,branch,sub,gldesc,invno,ref_no,ddate,totvalue,tax,staff,postaladdress,deliveryaddress,client,note,coyname) values (".$member_id.",".$coy_id.",".$accountno.",'".$branch."',".$sub.",'".$gldesc."',".$invno.",'".$ref_no."','".$ddate."',".$totvalue.",".$tax.",'".$staffname."','".$postaladdress."','".$deliveryaddress."','".$client."','".$note."','".$coyname."')";
$r = mysql_query($q) or die(mysql_error().' '.$q);

$q = "update quotes set xref = '".$ref_no."' where ref_no = '".$qref."'";
$r = mysql_query($q) or die(mysql_error().' '.$q);

?>
