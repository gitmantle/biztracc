<?php
session_start();
$usersession = $_SESSION['usersession'];
$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$chargefile = 'ztmp'.$user_id.'_charges';

$db->query("drop table if exists ".$findb.".".$chargefile);
$db->execute();

$db->query("create table ".$findb.".".$chargefile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, supplier varchar(50) default '', acno int(11) default 0, sbno int(11) default 0, descript varchar(50), charge decimal(16,2) default 0, taxpcent decimal(6,2), taxtype char(3), currency char(3), rate decimal(7,3) default 1, cosacno int(11), cossub int(11) ) engine myisam"); 
$db->execute();

$db->closeDB();


require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Uncosted GRNs</title>

<script>

window.name = "uncostgrn";

</script>

</head>
<body>
  <table width="810" border="0">
    <tr>
    	<td colspan="2">Ensure you have all costs and charges available for any GRN you wish to cost</td>
    </tr>
    <tr>
      <td colspan="2"><?php include "getUncostGRNs.php"; ?></td>
    </tr>
    <tr>
      <td colspan="2"><?php include "getUncostGRNitems.php"; ?></td>
    </tr>
    <tr>
      <td colspan="2"><?php include "getCharges.php"; ?></td>
    </tr>
     <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="bValue" type="button" value="Apportion by Value" onClick="ipostTrdTrans('val')">&nbsp;&nbsp;&nbsp;&nbsp;<input name="bQty" type="button" value="Apportion by Quantity" onClick="ipostTrdTrans('qty')"></td>
      <td align="right"> <input name="bpost" type="button" value="Update stock costings" onClick="updtgrncosts()"></td>
    </tr>
  </table>
</body>

</html>