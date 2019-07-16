<?php
session_start();
$coyid = $_REQUEST['coy'];
$usersession = $_SESSION['usersession'];


include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;

$findb = 'infinint_fin'.$subid.'_'.$coyid;

// populate branch drop down
$db->query("select * from ".$findb.".branch");
$rows = $db->resultset();
$options = "Select Branch,0~";
foreach ($rows as $row) {
	extract($row);
	$options .= $branchname.','.$branch.'~';
}

$db->closeDB();

$options = rtrim($options,'~');

echo $options;

?>

