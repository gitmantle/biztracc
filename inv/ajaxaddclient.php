<?php
session_start();
$clname = trim($_REQUEST['clname']);
$clemail = trim($_REQUEST['clemail']);
$clphone = trim($_REQUEST['phone']);
$claddress = trim($_REQUEST['address']);

$coyid = $_SESSION['s_coyid'];

ini_set('display_errors', true);
require("../db.php");

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$cltdb = $_SESSION['s_cltdb'];
mysql_select_db($cltdb) or die(mysql_error());

// find client with this email address

$q = "select member_id from comms where comms_type_id = 4 and comm = '".$clemail."'";
$r = mysql_query($q) or die(mysql_error());
$numrows = mysql_num_rows($r);
if ($numrows > 0) {
	$row = mysql_fetch_array($r);
	extract($row);
	$memid = $member_id;
	
	$qd = "select drno,drsub,priceband from client_company_xref where client_id = ".$memid." and company_id = ".$coyid;
	$rd = mysql_query($qd) or die(mysql_error().' '.$qd);
	$rowd = mysql_fetch_array($rd);
	extract($rowd);
	
	$dracno = $drno;
	$sb = $drsub;
	$pband = $priceband;
	
	
} else { // add to client database and make debtor of this company
	$q = "insert into members (lastname,status) values ('".$clname."','Client')";
	$r = mysql_query($q) or die(mysql_error());
	$newid = mysql_insert_id();
	
	$dracno = 30000000 + $newid;
	$sb = 0;
	$pband = 1;
	$memid = $newid;
	
	
	$x = "insert into client_company_xref (client_id,company_id,drno,sortcode,member) values ";
	$x .= "(".$newid.",";
	$x .= $coyid.",";
	$x .= $dracno.",'";
	$x .= $clname.$dracno."-0','";
	$x .= $clname."')";
	
	$result = mysql_query($x) or die(mysql_error().' - '.$x);	
	
	$c = "insert into comms (member_id,comms_type_id,comm) values ";
	$c .= "(".$newid.",";
	$c .= "4,'";
	$c .= $clemail."')";
	
	$result = mysql_query($c) or die(mysql_error().' - '.$c);	
	
	$comm1 = str_replace(" ","",$clphone);
	$comm2 = str_replace("-","",$comm1);
	$comm3 = str_replace(")","",$comm2);
	$comm4 = str_replace("(","",$comm3);

	$p = "insert into comms (member_id,comms_type_id,comm,comm2) values ";
	$p .= "(".$newid.",";
	$p .= "2,'";
	$p .= $clphone."','";
	$p .= $comm4."')";

	$result = mysql_query($p) or die(mysql_error().' - '.$p);	
	
	$ad = explode(',',$claddress);
	$no = trim($ad[0]);
	$st = trim($ad[1]);
	$su = trim($ad[2]);
	$tn = trim($ad[3]);
	
	$a = "insert into addresses (member_id,location,address_type_id,street_no,ad1,suburb,town) values ";
	$a .= "(".$newid.",";
	$a .= "'Street',";
	$a .= "2,'";
	$a .= $no."','";
	$a .= mysql_real_escape_string($st)."','";
	$a .= mysql_real_escape_string($su)."','";
	$a .= mysql_real_escape_string($tn)."')";
	
	$result = mysql_query($a) or die(mysql_error().' - '.$a);	

}

$member =  $dracno.'~'.' '.'~'.$sb.'~'.$clname.'~'.$memid.'~'.$pband;

// return relevant data to invoice.php

$q = "select comms.country_code,comms.area_code,comms.comm from comms,client_company_xref where client_company_xref.drno = ".$dracno." and client_company_xref.drsub = ".$sb." and comms.member_id = client_company_xref.client_id and comms.comms_type_id = 2";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$ph = trim($country_code.' '.$area_code.' '.$comm);

$q = "select comms.country_code,comms.area_code,comms.comm from comms,client_company_xref where client_company_xref.drno = ".$dracno." and client_company_xref.drsub = ".$sb." and comms.member_id = client_company_xref.client_id and comms.comms_type_id = 4";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$email = trim($comm);

$q = "select addresses.street_no,ad1,ad2,suburb,town from addresses,client_company_xref where client_company_xref.drno = ".$dracno." and client_company_xref.drsub = ".$sb." and addresses.member_id = client_company_xref.client_id and addresses.address_type_id = 2";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$address = $street_no.' '.$ad1.', '.$ad2.', '.$suburb.', '.$town;

echo $member.'***'.$ph.'***'.$email.'***'.$address;

?>
