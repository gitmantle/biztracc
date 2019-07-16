
<?php
session_start();
$tid = $_REQUEST['tid'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$cltdb = $_SESSION['s_cltdb'];

$db->query('delete from '.$cltdb.'.clienttype_xref where clienttype_xref_id = '.$tid);
$db->execute();

$db->closeDB();

?>

