<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$findb = $_SESSION['s_findb'];

$db->query('select * from '.$findb.'.branch order by branchname');
$rows = $db->resultset();
// populate branches list
$branch_options = "<option value=\"*\">All Branches</option>";
foreach ($rows as $row) {
	extract($row);
	$branch_options .= "<option value=\"".$branch."\">".$branchname."</option>";
}


date_default_timezone_set($_SESSION['s_timezone']);

$stkgroup = "";
// populate groups drop down
$db->query("select * from ".$findb.".stkgroup ");
$rows = $db->resultset();
$stkgroup_options = "<option value=\"*\">All Groups</option>";
foreach ($rows as $row) {
	extract($row);
	if ($groupid == $stkgroup) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$stkgroup_options .= '<option value="'.$groupid.'"'.$selected.'>'.$groupname.'</option>';
}

$stkcat = "";
// populate category drop down
$db->query("select * from ".$findb.".stkcategory ");
$rows = $db->resultset();
$stkcat_options = "<option value=\"*\">All Catgegories</option>";
foreach ($rows as $row) {
	extract($row);
	if ($catid == $stkcat) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$stkcat_options .= '<option value="'.$catid.'"'.$selected.'>'.$category.'</option>';
}

$db->query('select * from '.$findb.'.stklocs order by location');
$rows = $db->resultset();
// populate location list
$loc_options = "<option value=\"*\">Select Location</option>";
foreach ($rows as $row) {
	extract($row);
	$loc_options .= "<option value=\"".$uid."\">".$location."</option>";
}

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Stocktake Parameters</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

	window.name = "strep_stktake";


</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="600" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Stocktake Parameters</label></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Group</td>
      <td><select name="stkgroup" id="stkgroup"><?php echo $stkgroup_options;?>
      </select></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Category</td>
      <td><select name="stkcat" id="stkcat"><?php echo $stkcat_options;?>
      </select></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Location</td>
    <td><select name="loc" id="loc"><?php echo $loc_options;?>
    </select></td>
  </tr>
  <tr>
    <td class="boxlabelleft">If random selection, pick how many items</td>
    <td><input type="text" name="randomqty" id="randomqty" value="0"/>Leave as 0 if all items</td>
  </tr>
  <tr>
    <td class="boxlabelleft">&nbsp;</td>
    <td class="boxlabelleft">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="right"><input type="button" value="Run" name="run"  onClick="stktake()" ></td>
  </tr>
  </table>
</form>
</body>
</html>