<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$wagetable = 'ztmp'.$user_id.'_wages';
$table = 'ztmp'.$user_id.'_journal';

// populate driver drop down
$query = "select uid,concat_ws(' ',ufname,ulname) as fname from users where sub_id = ".$subscriber;
$result = mysql_query($query) or die(mysql_error().$query);
$op_options = "<option value=\"\">Select Driver</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$op_options .= '<option value="'.$fname.'"'.$selected.'>'.$fname.'</option>';
}

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select truckwagepcent,trailerwagepcent from params";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$kpcent = $truckwagepcent;
$lpcent = $trailerwagepcent;

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$wagetable;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$wagetable." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, operator varchar(45),truckno varchar(25),truckbranch varchar(4),truckamt decimal(16,2),trailerno varchar(25),trailerbranch varchar(4),traileramt decimal(16,2),total decimal(16,2))  engine myisam";
$calc = mysql_query($query) or die(mysql_error().' '.$query);

// populate trucks drop down
$query = "select branch,branchname from branch where branchname like 'Truck%'";
$result = mysql_query($query) or die(mysql_error().$query);
$truck_options = "<option value=\"\">Select Truck</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$truck_options .= '<option value="'.$branch.'~'.$branchname.'"'.$selected.'>'.$branchname.'</option>';
}

// populate trailers drop down
$query = "select branch,branchname from branch where branchname like 'Trailer%'";
$result = mysql_query($query) or die(mysql_error().$query);
$trailer_options = "<option value=\" \">Select Trailer</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$trailer_options .= '<option value="'.$branch.'~'.$branchname.'"'.$selected.'>'.$branchname.'</option>';
}

// populate bank accounts drop down
$query = "select account,accountno,branch,sub from glmast where accountno >750 and accountno <801 order by accountno";
$result = mysql_query($query) or die(mysql_error().$query);
$bank_options = "<option value=\"0\">Select Bank Account</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$bank_options .= '<option value="'.$account.'~'.$accountno.'~'.$branch.'~'.$sub.'"'.$selected.'>'.$account.' '.$accountno.' '.$branch.' '.$sub.'</option>';
}

// get labour account and sub account
$q = "select distinct accountno,sub from glmast where upper(account) = 'LABOUR'";
$r = mysql_query($q) or die(mysql_error().$q);
$row = mysql_fetch_array($r);
extract($row);
$labac = $accountno;
$labsb = $sub;
	
require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Allocate Wages</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script src="../fin/includes/ajaxgetref.js"></script>
<script type="text/javascript" src="includes/ajaxCheckTransDate.js"></script>
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>

<script>

	window.name = "allocwages";


</script>


<style type="text/css">
<!--
.style3 {color: #FFFFFF}
.style5 {font-size: x-small}
-->
</style>
</head>

<body>

<div id="tabs">
	<form name="trans" id="trans" method="post">
	<input type="hidden" name="Submit" value="true">	
    <input type="hidden" name="savebutton" id="savebutton" value="N">
	
	<table width="920" border="0" cellpadding="3" cellspacing="1" align="center">
      <tr bgcolor="<?php echo $bghead; ?>">
        <td colspan="4"><label style="color: <?php echo $thfont; ?>"><strong>Journal Transaction to Allocate Wages</strong></label></td>
      </tr>
      <tr>
        <td colspan="3" class="boxlabel">Only continue if you have a Cost of Sales/Purchases account named Labour for each truck and trailer:- </td>
        <td class="boxlabel">Found <input type="text" name="labac" id="labac" value="<?php echo $labac.' '.$labsb; ?>" size="9" readonly></td>
      </tr>
      <tr>
        <td class="boxlabel">Date</td>
        <td ><input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
        <td class="boxlabel">Reference</td>
        <td ><input name="ref" id="newref" value="JNL" type="text" size="4" readonly>
			<input type="text" name="refno" id="newrefno" value=" " size="10" onFocus="nextref()"></td>		
      </tr>
      <tr>
        <td class="boxlabel">Total being paid for this wage bill</td>
        <td ><input type="text" name="totwage" id="totwage" value="0" onFocus="this.select()"></td>
        <td class="boxlabel">Paid from account</td>
        <td><select name="bank" id="bank"><?php echo $bank_options;?>
      </select></td>
      </tr>
      <tr>
        <td class="boxlabel">Split between Trucks</td>
        <td class="boxlabelleft"><input type="text" name="truckpcent" id="truckpcent" size="6" value="<?php echo $kpcent; ?>" onFocus="this.select()">%</td>
        <td class="boxlabel">and Trailers</td>
        <td class="boxlabelleft"><input type="text" name="trailerpcent" id="trailerpcent" size="6" value="<?php echo $lpcent; ?>" onFocus="this.select()">%</td>
      </tr>
	<tr bgcolor="<?php echo $bghead; ?>">
	  <td colspan="2"><label style="color: <?php echo $thfont; ?>"><strong>New Transaction Line Details </strong></label></td>
	  <td colspan="2">
          <div align="right">
            <input type="button" name="add" id="add" value="Add" onclick="addwage()">
          </div></td>
	</tr>	  
	<tr>
	  <td class="boxlabelleft">Operator
	    <select name="loperator" id="loperator">
      	<?php echo $op_options; ?>
      </select></td>
      <td  class="boxlabelleft">Truck
        <select name="truck" id="truck"><?php echo $truck_options;?>
      </select></td>
      <td class="boxlabelleft" >Trailer
        <select name="trailer" id="trailer"><?php echo $trailer_options;?>
      </select></td>
      <td class="boxlabelleft" >Amount
        <input type="text" name="amount" id="amount" value="0" onFocus="this.select()"></td>
	  </tr>
      <tr>
      	<td colspan="4"><?php include "getwages.php"; ?></td>
      </tr>
      
	<tr bgcolor="<?php echo $bghead; ?>">
	 <td colspan="4"><div align="right"><input name="Submit" type="button" value="Post" onClick="postWages()"></div></td>
     </tr>
     </table>


</form>
</div>

</body>
</html>