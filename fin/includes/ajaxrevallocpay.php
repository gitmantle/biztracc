<?php
session_start();
$ac = $_REQUEST['ac'];
$sb = $_REQUEST['sb'];

require("../../db.php");

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = 'select (current+d30+d60+d90+d120) as tbal from client_company_xref where crno = '.$ac.' and crsub = '.$sb;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
$totbal = $tbal;

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "SELECT sum( totvalue + tax - cash - cheque - eftpos - ccard - valreturned ) AS alloc FROM invhead WHERE accountno = ".$ac." and sub = ".$sb." and (ref_no like 'GRN%')";
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);

$unallocated = $totbal + $alloc;
echo $unallocated;

?>
