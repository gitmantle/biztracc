<?php
session_start();
$tid = $_REQUEST['tid'];
$doc = $_REQUEST['doc'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$cltdb = $_SESSION['s_cltdb'];

$dir = "../../documents/campaign";

$path=$dir.'/'.$doc;
	
@unlink($path);

$db->query("delete from ".$cltdb.".campaign_docs where campdoc_id = ".$tid);
$db->execute();

$db->closeDB();


?>