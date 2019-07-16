<?php
session_start();
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);


		$thm = $_REQUEST['thm'];
		$q = 'update users set theme = "'.$thm.'" where uid = '.$user_id;
		$r = mysql_query($q) or die(mysql_error().$q);

		$_SESSION['deftheme'] = $thm;

		echo '<script>';
		echo 'alert("To change the theme of your pages you may need to refresh your browser ");';
		echo 'this.opener.close();';
		echo 'this.close();';
		echo '</script>';


?>

