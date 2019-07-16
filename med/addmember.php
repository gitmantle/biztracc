<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);


// populate title list
    $arr = array('Mr', 'Mrs', 'Ms', 'Miss', 'Master', 'Dr', 'Professor', 'Reverend');
	$mtitle_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		$mtitle_options .= '<option value="'.$arr[$i].'">'.$arr[$i].'</option>';
 	}

// populate day list
    $arr = array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');
	$mday_options = "<option value=\"00\">00</option>";
    for($i = 0; $i < count($arr); $i++)	{
		$mday_options .= '<option value="'.$arr[$i].'">'.$arr[$i].'</option>';
 	}

// populate month list
    $arr = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
	$mmonth_options = "<option value=\"00\">00</option>";
    for($i = 0; $i < count($arr); $i++)	{
		$mmonth_options .= '<option value="'.$arr[$i].'">'.$arr[$i].'</option>';
 	}

// populate year list
	$thisyear = date("Y");
	$myear_options = "";
    for($i = $thisyear - 120; $i < $thisyear - 50; $i++)	{
		$myear_options .= '<option value="'.$i.'">'.$i.'</option>';
 	}
	$myear_options .= "<option value=\"0000\" selected>0000</option>";
    for($i = $thisyear - 50; $i < $thisyear + 1; $i++)	{
		$myear_options .= '<option value="'.$i.'">'.$i.'</option>';
 	}

// populate gender list
    $arr = array('Male', 'Female');
	$mgender_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		$mgender_options .= '<option value="'.$arr[$i].'" >'.$arr[$i].'</option>';
 	}
	

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Member</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>

<script>

window.name = "addmember";

function changeCase(frmObj) {
	var index;
	var tmpStr;
	var tmpChar;
	var preString;
	var postString;
	var strlen;
	tmpStr = frmObj.toLowerCase();
	strLen = tmpStr.length;
	if (strLen > 0)  {
		for (index = 0; index < strLen; index++)  {
			if (index == 0)  {
				tmpChar = tmpStr.substring(0,1).toUpperCase();
				postString = tmpStr.substring(1,strLen);
				tmpStr = tmpChar + postString;
			} else {
				tmpChar = tmpStr.substring(index, index+1);
				if (tmpChar == " " && index < (strLen-1))  {
					tmpChar = tmpStr.substring(index+1, index+2).toUpperCase();
					preString = tmpStr.substring(0, index+1);
					postString = tmpStr.substring(index+2,strLen);
					tmpStr = preString + tmpChar + postString;
				 }
			}
		}
	}
	return tmpStr;
}


function mbreakname(name) {
	var nm = name.split(' ');
	var x = nm.length;
	if (x == 2) {
		document.getElementById('mfirstname').value = changeCase(nm[0]);
		document.getElementById('mlastname').value = nm[1];
		document.admem.mprefname.focus();
	}
	if (x == 3) {
		document.getElementById('mfirstname').value = changeCase(nm[0]);
		document.getElementById('mmiddlename').value = changeCase(nm[1]);
		document.getElementById('mlastname').value = nm[2];
		document.admem.mprefname.focus();
	}
}

function pbreakname(name) {
	var nm = name.split(' ');
	var x = nm.length;
	if (x == 2) {
		document.getElementById('pfirstname').value = changeCase(nm[0]);
		document.getElementById('plastname').value = nm[1];
		document.admem.pprefname.focus();
	}
	if (x == 3) {
		document.getElementById('pfirstname').value = changeCase(nm[0]);
		document.getElementById('pmiddlename').value = changCase(nm[1]);
		document.getElementById('plastname').value = nm[2];
		document.admem.pprefname.focus();

	}
}


function calcagem() {
	var d = new Date();
	var curr_day = d.getDate();
	var curr_month = d.getMonth()+1;
	var curr_year = d.getFullYear();
	var birthYear = document.getElementById('myear').value;
	var birthMonth = document.getElementById('mdobmonth').value;
	var birthDay = document.getElementById('mdobday').value;
	var age = curr_year - birthYear;
	if (curr_month < birthMonth || (curr_month == birthMonth && curr_day < birthDay)) {
		age = age - 1;
	}
	
	document.getElementById('mage').value = age;
}


function exit() {
	var x = confirm('You are about to exit without saving this record. Is that what you want to do?');
	if (x == true) {
		this.close();
	} else {
		return false;
	}
}

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function checkage(from) {
	if (from == 'm') {
		var yr = document.getElementById('mage').value;
	} else {
		var yr = document.getElementById('agep').value;
	}
	if (isNaN(yr)) {
		alert('Age must be numeric');
		return false;
	}
	var d = new Date();
	var thisyear = d.getFullYear();
	if (from == 'm') {
		var dobyr = document.getElementById('myear').value;
		if (dobyr == '0000') {
			var byear = thisyear - yr;
			document.getElementById('myear').value = byear;
		} else {
			var yearentered = document.getElementById('myear').value;
			if ((thisyear - yr) != yearentered) {
				alert('Age and Year of Birth do not correlate')
				calcagem();
			}
		}
	} else {
		var dobyr = document.getElementById('pyear').value;
		if (dobyr == '0000') {
			var byear = thisyear - yr;
			document.getElementById('pyear').value = byear;
		} else {
			var yearentered = document.getElementById('pyear').value;
			if ((thisyear - yr) != yearentered) {
				alert('Age and Year of Birth do not correlate')
				calcagep();
			}
		}
	}
}

function post() {

	//add validation here if required.
	var lname = document.getElementById('mlastname').value;
	var ok = "Y";
	if (lname == "") {
		alert("Please enter a last name.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('admem').submit();
	}
	
	
}



</script>


<style type="text/css">
<!--
.style1 {font-size: large}
.star {
	color: #F00;
}
-->
</style>
</head>


<body>
<form name="admem" id="admem" method="post">
 <input type="hidden" name="savebutton" id="savebutton" value="N">


<div id="htop41" style="position:absolute;visibility:visible;top:1px;left:1px;height:160px;width:950px;border-width:thin thin thin thin; border-color:#999; border-style:solid;">
	<table width="950" border="0" align="center" bgcolor="<?php echo $bgcolor; ?>">
		<tr>
		  	<td colspan="2" align="left" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size: 14px;">Member</label></td>
	  	  <td width="140" align="left" bgcolor="#FF0000"><input type="button" value="Save" name="save" id="save" onClick="post()">
	  	    &nbsp;&nbsp;
	  	    <input type="button" name="out" id="out" value="Exit" onClick="exit()"></td>
	  	  <td colspan="3" bgcolor="<?php echo $bghead; ?>" class="boxlabel">&nbsp;</td>
   	    </tr>
		<tr>
		  	<td width="145" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Title</label></td>
		  	<td width="230" align="left"><select name="mtitle" id="mtitle" tabindex="1"><?php echo $mtitle_options;?></select></td>
	  	  <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Date of Birth</label></td>
		  	<td colspan="3" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">dd&nbsp;</label>
            	<select name="mdobday" id="mdobday" tabindex="6"><?php echo $mday_options;?></select>
		  	  <label style="color: <?php echo $tdfont; ?>">&nbsp;mm</label>
            	<select name="mdobmonth" id="mdobmonth" tabindex="7"><?php echo $mmonth_options;?></select>
	  	      <label style="color: <?php echo $tdfont; ?>">&nbsp;yyyy</label>
            	<select name="myear" id="myear" tabindex="8" onChange="calcagem()"><?php echo $myear_options;?></select>
	  	      <label style="color: <?php echo $tdfont; ?>">&nbsp;Age</label>
              <input name="mage" type="text" id="mage" size="3" maxlength="3" value="0" onChange="checkage('m')"></td>
	 	</tr>
		<tr>
			<td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">First Name</label></td>
			<td align="left"><input name="mfirstname" id="mfirstname" type="text" size="25" maxlength="45"tabindex="2" onfocus="this.select();" onBlur="mbreakname(this.value)" ></td>
	  	  <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Gender</label></td>
		  	<td align="left"><select name="mgender" id="mgender"><?php echo $mgender_options;?></select></td>
		  	<td class="boxlabel">&nbsp;</td>
		  	<td  align="left">&nbsp;</td>
  	    </tr>
		<tr>
		 	<td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Middle Name</label></td>
		  	<td align="left"><input name="mmiddlename" id="mmiddlename" type="text" size="25" maxlength="45" tabindex="3" onfocus="this.select();"></td>
	  	  <td class="boxlabel">&nbsp;</td>
		  	<td align="left">&nbsp;</td>
		  	<td class="boxlabel">&nbsp;</td>
		  	<td align="left">&nbsp;</td>
	  	</tr>
		<tr>
			<td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Last Name</label></td>
			<td align="left"><input name="mlastname" id="mlastname" type="text" size="25" maxlength="45" tabindex="4" onfocus="this.select();"><label style="color: <?php echo $tdfont; ?>; font-size:20px;">*</label></td>
	 	  <td class="boxlabel">Occupation</td>
		  	<td colspan="3" align="left"><input name="moccupation" id="moccupation" type="text" size="45" maxlength="45" tabindex="3" onfocus="this.select();"></td>
	 	</tr>
		<tr>
			<td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Preferred Name</label></td>
			<td align="left"><input name="mprefname" id="mprefname" type="text" size="25" maxlength="45" tabindex="5" onfocus="this.select();"></td>
	 	  <td class="boxlabel">Position</td>
		  	<td colspan="3" align="left"><input name="mposition" id="mposition" type="text" size="45" maxlength="45" tabindex="3" onfocus="this.select();"></td>
	  	</tr>
	</table>
</div>  
 
<div id="hbot42" style="position:absolute;visibility:visible;top:171px;left:1px;height:202px;width:950px;border-width:thin thin thin thin; border-color:#999; border-style:solid;">
   
   
</div>
	
</form>

<script>
	document.onkeypress = stopRKey;
	document.getElementById('mtitle').focus();
</script> 



<?php

	if($_REQUEST['savebutton'] == "Y") {
		
		
		include_once("../includes/cltadmin.php");
		$oCn = new cltadmin;	
		
		$oCn->sub_id = $subid;
		$oCn->firstname = ucwords($_REQUEST['mfirstname']);
		$oCn->lastname = ucwords($_REQUEST['mlastname']);
		$oCn->middlename = ucfirst($_REQUEST['mmiddlename']);
		$oCn->preferredname = ucfirst($_REQUEST['mprefname']);
		$oCn->title = $_REQUEST['mtitle'];
		$mdob = $_REQUEST['myear'].'-'.$_REQUEST['mdobmonth'].'-'.$_REQUEST['mdobday'];
		$oCn->dob = $mdob;
		$oCn->position = $_REQUEST['mposition'];
		$oCn->occupation = $_REQUEST['moccupation'];
		$oCn->gender = $_REQUEST['mgender'];
		$oCn->age = $_REQUEST['mage'];
		$oCn->memtype = 'M';
		
		
		$cluid = $oCn->AddMember();
		
		$_SESSION['s_memberid'] = $cluid;
		
		
		//****************************************************************
		// add member as debtor of company
		//****************************************************************
		$tid = $cluid;
		
		$dracno = 30000000 + $tid;
		
		$query = "select lastname from members where member_id = ".$tid;
		$result = mysql_query($query) or die($query);
		$row = mysql_fetch_array($result);
		extract($row);
		
			
		$SQLString = "insert into client_company_xref (client_id,company_id,drno,sortcode,member) values ";
		$SQLString .= "(".$tid.",";
		$SQLString .= "1,";
		$SQLString .= $dracno.",'";
		$SQLString .= $lastname.$dracno."-0','";
		$SQLString .= $lastname."')";
		
		$result = mysql_query($SQLString) or die(mysql_error().' - '.$SQLString);

		
		//****************************************************************
		
		$hdate = date('Y-m-d');
		$ttime = strftime("%H:%M", mktime());
		
		$query = "insert into auditmem (ddate,ttime,user_id,uname,sub_id,member_id,action) values ";
		$query .= "('".$hdate."',";
		$query .= "'".$ttime."',";
		$query .= $user_id.",";
		$query .= "'".$uname."',";
		$query .= $subid.",";
		$query .= $cluid.",";
		$query .= "'Add Member')";
		
		$result = mysql_query($query) or die(mysql_error().$query);

		echo '<script>';
		echo 'var x = 0, y = 0;';	
		echo 'x = window.screenX +5;';
		echo 'y = window.screenY +200;';
		echo "window.open('editmember.php','edmem','toolbar=0,scrollbars=1,height=420,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);";
		echo 'this.close();';
		echo '</script>;';
				
	}

?>



</body>
</html>
