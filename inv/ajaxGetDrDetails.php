<?php
session_start();
$acc = $_REQUEST['acc'];
$sb = $_REQUEST['sb'];

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

$q = "select comms.country_code,comms.area_code,comms.comm from comms,client_company_xref where client_company_xref.drno = ".$acc." and client_company_xref.drsub = ".$sb." and comms.member_id = client_company_xref.client_id and comms.comms_type_id = 2";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$ph = $country_code.' '.$area_code.' '.$comm;

$q = "select comms.comm from comms,client_company_xref where client_company_xref.drno = ".$acc." and client_company_xref.drsub = ".$sb." and comms.member_id = client_company_xref.client_id and comms.comms_type_id = 4";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$email = $comm;

$q = "select addresses.street_no,ad1,ad2,suburb,town from addresses,client_company_xref where client_company_xref.drno = ".$acc." and client_company_xref.drsub = ".$sb." and addresses.member_id = client_company_xref.client_id and addresses.address_type_id = 2";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$address = $street_no.' '.$ad1.', '.$ad2.', '.$suburb.', '.$town;

echo $ph.'~'.$email.'~'.$address;

?>
