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
$subscriber = $subid;
$sname = $row['uname'];;

$obaltable = 'ztmp'.$user_id.'_stkobal';
$serialtable = 'ztmp'.$user_id.'_serialnos';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$obaltable);
$db->execute();
$db->query("drop table if exists ".$findb.".".$serialtable);
$db->execute();

$db->query("create table ".$findb.".".$obaltable." (itemid int(11),groupid int(11), groupname varchar(25),catid int(11),category varchar(25),itemcode varchar(30) default '',item varchar(100) default '',stock char(7), unit varchar(20) default '',quantity decimal(16,3) default 0, avgcost decimal(16,2) default 0,trackserial char(3), location int(11) default 1 )  engine myisam");
$db->execute();

$db->query("insert into ".$findb.".".$obaltable." SELECT stkmast.itemid,stkmast.groupid,stkgroup.groupname,stkmast.catid,stkcategory.category,stkmast.itemcode,stkmast.item,stkmast.stock,stkmast.unit,0,0,stkmast.trackserial,1 from ".$findb.".stkmast,".$findb.".stkgroup,".$findb.".stkcategory where stkmast.groupid = stkgroup.groupid and stkmast.catid = stkcategory.catid and stkmast.stock = 'Stock'");
$db->execute();

$db->query("create table ".$findb.".".$serialtable." (itemcode varchar(30) default '',item varchar(100) default '',serialno varchar(50) default '', locationid int(11) default 0, location varchar(30) default '', ref_no varchar(15) default '', selected char(1) default 'N')  engine myisam");
$db->execute();

// populate location list
$db->query('select * from '.$findb.'.stklocs order by location');
$rows = $db->resultset();
$loc_options = "<option value=\"\">Select Location</option>";
foreach ($rows as $row) {
	extract($row);
	$loc_options .= "<option value=\"".$uid."\">".$location."</option>";
}

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Stock Opening balances</title>
<script type="text/javascript">

	window.name = "stad_openbal";



</script>
</head>
<body>
  <table width="910" border="0">
  	<tr>
    	<td colspan="2">Opening stock balances for location&nbsp;<select name="loc" id="loc"><?php echo $loc_options; ?>
      </select> 
    	Enter Opening Stock balances for each location separately </td>
  	</tr>
    <tr>
      <td colspan="2"><?php include "getstk.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
      <td align="right"><input type="button" name="bpost" id="bpost" value="Update Stock" onClick="stkupdtobal()"/></td>
    </tr>
  </table>
</body>
</html>