<?php
session_start();
$costid = $_REQUEST['tid'];

include_once("../../includes/cltadmin.php");
$oAd = new cltadmin;	

$oAd->uid = $costid;

$oAd->DelCampCost();


?>