<?php
session_start();
$ac = $_REQUEST['ac'];
$sb = $_REQUEST['sb'];
$coyid = $_SESSION['s_coyid'];

$cltdb = $_SESSION['s_cltdb'];
$findb = $_SESSION['s_findb'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query('select (current+d30+d60+d90+d120) as tbal from '.$cltdb.'.client_company_xref where drno = '.$ac.' and drsub = '.$sb.' and company_id = '.$coyid);
$row = $db->single();
extract($row);
$totbal = $tbal;

$db->query("SELECT sum( totvalue + tax - cash - cheque - eftpos - ccard - valreturned ) AS alloc FROM ".$findb.".invhead WHERE accountno = ".$ac." and sub = ".$sb." and (ref_no like 'INV%')");
$row = $db->single();
extract($row);

$db->closeDB();

$unallocated = ($alloc - $totbal);
echo $unallocated;

?>
