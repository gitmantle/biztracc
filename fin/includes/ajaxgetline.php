<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$table = 'ztmp'.$user_id.'_trans';

	
	$lineno = $_REQUEST['lineno'];

	$moduledb = $_SESSION['s_findb'];
	mysql_select_db($moduledb) or die(mysql_error());
	
	$query = "select acc2dr,subdr,brdr,acc2cr,subcr,brcr,ddate,descript1,reference,amount,tax,taxtype,taxpcent,total,refindex,taxindex,a2d,a2c from ".$table." where uid = ".$lineno;
	$result = mysql_query($query) or die($query);
	$row = mysql_fetch_array($result);
	extract($row);
	$str = $acc2dr."~".$subdr."~".$brdr."~".$acc2cr."~".$subcr."~".$brcr."~".$ddate."~".$descript1."~".$reference."~".$amount."~".$tax."~".$taxtype."~".$taxpcent."~".$total."~".$refindex."~".$taxindex."~".$a2d."~".$a2c;
	echo $str;

?>