<?php
	//session_start();
error_reporting(0); 
$usersession = $_SESSION['usersession'];

if (isset($usersession)) {
	$server = $_SESSION['s_server'];
	$user = "infinint_sagana";
	$pwd = "dun480can";
	$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection - db");
} else {
	header("Location: index.php");
}
?>