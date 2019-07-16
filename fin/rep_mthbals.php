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

// populate list of twelve months
for ($i = 1; $i <= 12; $i++) {
    $months[] = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));
}

$month_options = "";
foreach ($months as $value) {
	$month_options .= "<option value=\"".$value."\">".$value."</option>";
}

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Balances by Month</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

	window.name = "rep_mthbals";

</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="900" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="5" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Balances by Month</label></td>
  </tr>
  <tr>
    <td class="boxlabelleft">For the 12 Months ending</td>
    <td><select name="endmonth" id="endmonth" >
      <?php echo $month_options;?>
    </select></td>
    <td >&nbsp;</td>
    <td class="boxlabelleft">&nbsp;</td>
    <td id="edatecell">&nbsp;</td>
  </tr>
  <tr>
   	<td class="boxlabelleft">Branches</td>
    <td><select name="branch" id="branch" multiple="multiple"><?php echo $branch_options;?></select></td>
    <td colspan="3" class="boxlabelleft">Consolidate
    <input type="checkbox" name="consbranch" id="consbranch"/></td>
  </tr>
  <tr>
  	<td class="boxlabelleft">Sub Accounts</td>
    <td>&nbsp;</td>
    <td colspan="3" class="boxlabelleft">Consolidate
    <input type="checkbox" name="conssubac" id="conssubac"/></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2"><input type="button" value="Run" name="run"  onClick="mthbals()" ></td>
  </tr>
  </table>
  
  
  
</form>
</body>
</html>