<?php
session_start();

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$stfile = 'ztmp'.$user_id.'_stkavailable';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$stfile);
$db->execute();

$db->query("create table ".$findb.".".$stfile." (itemcode varchar(30) default '', item varchar(100) default '', onhand  decimal(17,3) default 0, uncosted decimal(17,3) default 0, salesorders decimal(17,3) default 0, purchaseorders decimal(17,3) default 0, stkrequired decimal(17,3) default 0)  engine myisam");
$db->execute();

$db->query("select itemcode,item,onhand,uncosted from ".$findb.".stkmast where stock = 'Stock'");
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query("insert into ".$findb.".".$stfile." (itemcode,item,onhand,uncosted) values (:itemcode,:item,:onhand,:uncosted)");
	$db->bind(':itemcode', $itemcode);
	$db->bind(':item', $item);
	$db->bind(':onhand', $onhand);
	$db->bind(':uncosted', $uncosted);
	
	$db->execute();
}

$db->query("select itemcode from ".$findb.".".$stfile);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$itemid = $itemcode;
	$db->query("select sum(quantity - returns) as so from ".$findb.".invtrans where itemcode = '".$itemid."' and substring(ref_no,1,3) = 'S_O'");
	$sorow = $db->single();
	extract($sorow);
	if (!isset($so)) {
		$so = 0;
	}
	$db->query("update ".$findb.".".$stfile." set salesorders = ".$so." where itemcode = '".$itemid."'");
	$db->execute();
	$db->query("select sum(quantity-supplied) as po from ".$findb.".p_olines where itemcode = '".$itemid."'");
	$porow = $db->single();
	extract($porow);
	if (!isset($po)) {
		$po = 0;
	}
	$db->query("update ".$findb.".".$stfile." set purchaseorders = ".$po." where itemcode = '".$itemid."'");
	$db->execute();
}

$db->query("update ".$findb.".".$stfile." set stkrequired = if((onhand + uncosted + purchaseorders - salesorders) >= 0, 0,(onhand + uncosted + purchaseorders - salesorders) * -1)");
$db->execute();


$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Stock Availability</title>
<script type="text/javascript">

window.name = "stkavailable";


</script>
</head>
<body>
  <table width="950" border="0">
    <tr>
      <td><?php include "../fin/getstkavailable.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>
</html>