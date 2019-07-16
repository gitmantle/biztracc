<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$pcfile = 'ztmp'.$user_id.'_pc';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$pcfile);
$db->execute();

$db->query("create table ".$findb.".".$pcfile." (item varchar(100), asellprice decimal(16,2) NOT NULL default 0, ssellprice decimal(16,2) NOT NULL default 0)  engine myisam");
$db->execute();

// populate pc table

$db->query("select taxpcent from ".$findb.".taxtypes where defgst = 'Yes'");
$row = $db->single();
if (!empty($row)) {
	extract($row);	
	$tpcent = $taxpcent;
} else {
	$db->query("select taxpcent from ".$findb.".taxtypes where uid = 1");
	$row = $db->single();
	extract($row);	
	$tpcent = $taxpcent;
}
		
$db->query("select pcent from ".$findb.".stkpricepcent where uid = 1");
$row = $db->single();
extract($row);	
$spcent = $pcent;
		
$markup = 1 + $spcent/100;
$tax = 1 + $tpcent/100;		

$db->query("insert into ".$findb.".".$pcfile."(select item as it, avgcost * ".$markup." * ".$tax." as asell, setsell * ".$tax." as ssell from ".$findb.".stkmast where stock = 'Stock')");
$db->execute();

// Add uid
$db->query("alter table ".$findb.".".$pcfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db->execute();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

$db->closeDB();


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Price Comparison</title>


<script type="text/javascript">

window.name = 'pcgrid';


</script>
</head>
<body>
    <table align="center">
        <tr>
	        <td><?php include "getpc.php"; ?></td>
        </tr>
	</table>		

</body>
</html>