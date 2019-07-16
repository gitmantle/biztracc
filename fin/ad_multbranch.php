<?php
session_start();

$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

// populate list with branches
$db->query("select * from ".$findb.".branch order by branch");
$rows = $db->resultset();
$branchList = "<option value=\"\">Select Branch</option>";
foreach ($rows as $row) {
	extract($row);
	$branchList .= "<option value=\"".$branch."\">".$branchname."</option>";
}

$db->closeDB();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Add Accounts to Branches</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="includes/ajaxgetacsinbranch.js"></script>


</head>

<body>

<div id="tabs">

	<form name="form1" method="post" >
	<table width="850" border="0" cellpadding="3" cellspacing="1" align="center">
	<tr>
	  <td colspan="2"><div align="center"><strong><u>Add a range of Account Numbers to a  Branch</u></strong></div></td>
	  </tr>
	<tr>
	  <td width="454">Pick the Branch to be copied from </td>
	  <td width="381"><div align="left"><select name="frombranch" id="frombranch" onChange="display(this.value)">
          <?php echo $branchList; ?>
        </select></div></td>
	</tr>
	<tr id="arange1">
	  <td>Pick the first Account Number in the range to be copied </td>
	  <td><div align="left"><select name="fromacc" id="fromacc">
          <?php echo $accountsList; ?>
        </select></div></td>
	  </tr>
	<tr id="arange2">
	  <td>Pick the last Account Number in the range to be copied </td>
	  <td><div align="left"><select name="toacc" id="toacc">
          <?php echo $accountsList; ?>
        </select></div></td>
	  </tr>
	<tr id="brange1">
	  <td>Pick the Branch to copy Account Numbers to </td>
	  <td><div align="left"><select name="tobranch" id="tobranch">
          <?php echo $branchList; ?>
        </select></div></td>
	  </tr>
	<tr id="addbut">
	  <td>&nbsp;</td>
	  <td><div align="right">
     	 <input type="button" value="Add Accounts" name="go" id="go" onclick="gobr2()">
	    </div></td>
	  </tr>
	
	</table>
	<script>
		document.getElementById('arange1').style.display = 'none';
		document.getElementById('arange2').style.display = 'none';
		document.getElementById('brange1').style.display = 'none';
		document.getElementById('addbut').style.display = 'none';
	</script>	
</form>

</div>



</body>
</html>