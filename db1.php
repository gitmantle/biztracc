<?php
	session_start();
	$server = $_SESSION['s_server'];
	$user = "infinint_sagana";
	$pwd = "dun480can";
	$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection - db1"." SERVER ".$server);

?>