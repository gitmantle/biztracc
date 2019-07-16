<?php

session_start();

$admindb = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];
$coyname = $_SESSION['s_coyname'];
$usersession = $_SESSION['usersession'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);


$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Import Accounts from "Excel</title>


</head>

<body>
<form name="form1" id="form1" method="post" >
  <table width="500" border="0" bgcolor="<?php echo $bgcolor; ?>" align="center">
	<tr>
	  <td colspan="2"><p>Use this facility to import data from excel spread sheets obtained from the export of data from other accounting systems. If the export was to a .csv file, please open it in Excel and save it as a .xlsx file.</p></td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
		<td>&nbsp;</td>
    </tr>
	<tr>
	  <td>General Ledger chart of accounts</td>
		<td><input type="button" name="btngl" id="btngl" value="Import" onclick="importaccounts('gl')" title="Import General Ledger"/></td>
    </tr>
	<tr>
	  <td>Debtors/Customers</td>
		<td><input type="button" name="btndr" id="btndr" value="Import" onclick="importaccounts('dr')" title="Import Debtors ledger"/></td>
    </tr>
	<tr>
	  <td>Creditors/Suppliers</td>
		<td><input type="button" name="btncr" id="btncr" value="Import" onclick="importaccounts('cr')" title="Import Creditors ledger"/></td>
    </tr>
	<tr>
	  <td>Stock items, average cost</td>
		<td><input type="button" name="btnst" id="btnst" value="Import" onclick="importaccounts('st')" title="Import Stock"/></td>
    </tr>
	<tr>
	  <td>Fixed Assets</td>
		<td><input type="button" name="btnas" id="btnas" value="Import" onclick="importaccounts('as')" title="Import Fixed Assets"/></td>
    </tr>
  </table>
</form>



</body>
</html>