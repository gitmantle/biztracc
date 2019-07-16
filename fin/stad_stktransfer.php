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
$sname = $row['uname'];

$findb = $_SESSION['s_findb'];

$db->query('select * from '.$findb.'.branch order by branchname');
$rows = $db->resultset();
// populate branches list
$branch_options = "<option value=\"*\">All Branches</option>";
foreach ($rows as $row) {
	extract($row);
	$branch_options .= "<option value=\"".$branch."\">".$branchname."</option>";
}

$db->query('select * from '.$findb.'.stklocs order by location');
$rows = $db->resultset();
// populate location list
$loc_options = "<option value=\"\">Select Location</option>";
foreach ($rows as $row) {
	extract($row);
	$loc_options .= "<option value=\"".$uid."\">".$location."</option>";
}

date_default_timezone_set($_SESSION['s_timezone']);

$edate = date("d/m/Y");
$edateh = date("Y-m-d");

$db->query("select bedate from ".$findb.".globals");
$row = $db->single();
extract($row);
$bdateh = $bedate;
$dt = explode('-',$bedate);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$fdt = time(0,0,0,$m,$d,$y);
$bdate = date("d/m/Y",$fdt);

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Stock Transfer</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

	window.name = "stad_stktransfer";


</script>

</head>

<body>
<form name="form1" method="post" >
  <input type="hidden" name="trading" id="trading" value="transfer">
<br>
  <table width="900" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Stock Transfer</label></td>
  </tr>
  <tr>
    <td class="boxlabelleft">From stock code</td>
    <td><input type="text" name="stkitemout" id="stkitemout" size="40" readonly >
      <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onclick="stkvisible1()"></td>
  </tr>
  <tr>
    <td class="boxlabelleft">In location</td>
    <td><select name="loc1" id="loc1"><?php echo $loc_options; ?></select></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Number of items to transfer</td>
    <td><input type="text" name="transferqty" id="transferqty" onfocus="this.select();" onblur="CheckAvailablet(this.value)" /></td>
  </tr>
  <tr>
    <td class="boxlabelleft">At cost per 
      <input type="text" name="outunit" id="outunit" readonly/></td>
    <td><input type="text" name="outcost" id="outcost" /></td>
  </tr>
  <tr>
   	<td class="boxlabelleft">&nbsp;</td>
    <td>&nbsp;</td>
    </tr>
  <tr>
  	<td class="boxlabelleft">To stock code</td>
    <td><input type="text" name="stkitemin" id="stkitemin" size="40" readonly >
      <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onclick="stkvisible2()"></td>
    </tr>
  <tr>
    <td class="boxlabelleft">In location</td>
    <td><select name="loc2" id="loc2"><?php echo $loc_options; ?></select></td>
  </tr>
  <tr>
    <td class="boxlabelleft">As number of items </td>
    <td><input type="text" name="intoqty" id="intoqty" value="0"/></td>
  </tr>
  <tr>
    <td class="boxlabelleft">At cost per 
      <input type="text" name="inunit" id="inunit" readonly/></td>
    <td><input type="text" name="incost" id="incost" value="0"/></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="button" value="Save" name="run"  onClick="stktrf()" ></td>
  </tr>
  </table>
  
   <div id="stkselect1" style="position:absolute;visibility:hidden;top:156px;left:80px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchstk1" size="50" onkeypress="doSearchstk1()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="stkclose" onclick="sboxhidestk1()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectstktransfer1.php"; ?></td>
      </tr>
    </table>
  </div>
 
   <div id="stkselect2" style="position:absolute;visibility:hidden;top:156px;left:80px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchstk2" size="50" onkeypress="doSearchstk2()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="stkclose" onclick="sboxhidestk2()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectstktransfer2.php"; ?></td>
      </tr>
    </table>
  </div>
 
  
  
</form>
</body>
</html>