<?php
session_start();
$usersession = $_SESSION['usersession'];
$coyid = $_SESSION['s_coyid'];
$cltdb = $_SESSION['s_cltdb'];
$_SESSION['s_customer'] = '0~0';
$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$db->query("select member, client_id, drno, drsub from ".$cltdb.".client_company_xref where company_id = ".$coyid." and drno > 0");
$rows = $db->resultset();
$customer_options = "<option value=\"\">Select Customer</option>";
foreach ($rows as $row) {
	extract($row);
	$customer_options .= "<option value=\"".$drno.'~'.$drsub.'~'.$client_id."\">".$member."</option>";
}

$table = 'ztmp'.$user_id.'_dns';

$sql = "drop table if exists ".$findb.".".$table;
$db->query($sql);
$db->execute();

$sql = "create table ".$findb.".".$table." (ref_no varchar(15) default '',accountno int(11) default 0,sub int(11) default 0,ddate date,client varchar(70) default '',totvalue decimal(16,2) default 0,invoice varchar(15) default 0,selected char(1) default 'N', locid int(11) default 0, currency char(3) default '', rate decimal(7,3) default 1) engine innodb";
$db->query($sql);
$db->execute();
$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Delivery notes to invoice</title>
<script type="text/javascript">

window.name = "updtdns";

function refreshdnsgrid() {
	jQuery("#d4ilist").setGridParam({url:"getdns4inv.php"}).trigger("reloadGrid"); 
}


</script>
</head>
<body>
  <table width="800" border="0" align="center">
	<tr>
    	<td> Select Customer to Invoice &nbsp;&nbsp;<select name="cust" id="cust" onchange="selectcustomer()"><?php echo $customer_options; ?></select></td>
    </tr>
    <tr>
      <td align="center"><?php include "getdns4inv.php"; ?></td>
    </tr>
    <tr>
    <td align="right"><input type="button" name="binv" id="binv" value="Create Invoice from selected Delivery Notes" onClick="createinv()"/></td> 
    </tr>
    <tr>
      <td align="center"><?php include "getdns4invlines.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>
</html>