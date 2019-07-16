<?php
$usersession = $_COOKIE['usersession'];
$dbs = "ken47109_kenny";

require("../db.php");
mysql_select_db($dbs) or die(mysql_error());
$query = "select * from sessions where session_id = ".$usersession;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subid = $sub_id;
$from = $_REQUEST['from'];
if (isset($_REQUEST['memid'])) {
	$memid = $_REQUEST['memid'];
} else {
	$memid = 'N';
}

$dbs = "ken47109_kenny";
require("../db.php");
mysql_select_db($dbs) or die(mysql_error());


// populate status drop down
$query = "select * from status";
$result = mysql_query($query) or die(mysql_error());
$mstatus_options = '<option value="0">Select Status</option>';
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$mstatus_options .= '<option value="'.$status_id.'" >'.$status.'</option>';
}

// populate process drop down
$query = "select * from workflow";
$result = mysql_query($query) or die(mysql_error());
$mprocess_options = '<option value="0">Select Process</option>';
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$mprocess_options .= '<option value="'.$process_id.'" >'.$process.'</option>';
}

// populate dbs drop down
$query = "select * from dbs";
$result = mysql_query($query) or die(mysql_error());
$mdbs_options = '<option value="0">Select Database</option>';
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$mdbs_options .= '<option value="'.$dbs_id.'">'.$database_name.'</option>';
}

// populate client types drop down
$query = "select * from client_types ";
$result = mysql_query($query) or die(mysql_error());
$mtype_options = '<option value="0">Select Client Type</option>';
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$mtype_options .= '<option value="'.$client_type_id.'" >'.$client_type.'</option>';
}

// populate advisor drop down
$query = "select * from staff where sub_id = ".$subid." and advisor = 'Y' order by lastname";
$result = mysql_query($query) or die(mysql_error());
$mpadvisor_options = '<option value="0">Select Advisor</option>';
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$mpadvisor_options .= '<option value="'.$firstname.' '.$lastname.'">'.$firstname.' '.$lastname.'</option>';
}


// populate referred drop down
$query = "select * from referred";
$result = mysql_query($query) or die(mysql_error());
$mreferred_options = '<option value="0">Select Referred By</option>';
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$mreferred_options .= '<option value="'.$referred_id.'">'.$referred.'</option>';
}

// populate title list
    $arr = array('Mr', 'Mrs', 'Ms', 'Miss', 'Master', 'Dr', 'Professor', 'Reverend');
	$mtitle_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		$mtitle_options .= '<option value="'.$arr[$i].'">'.$arr[$i].'</option>';
 	}

// populate gender list
    $arr = array('Male', 'Female');
	$mgender_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		$mgender_options .= '<option value="'.$arr[$i].'" >'.$arr[$i].'</option>';
 	}

// populate smoker list
    $arr = array('No', 'Yes');
	$msmoker_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		$msmoker_options .= '<option value="'.$arr[$i].'">'.$arr[$i].'</option>';
 	}

// populate married list
    $arr = array('Single', 'Married', 'Divorced', 'Widowed', 'De Facto');
	$mmarried_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		$mmarried_options .= '<option value="'.$arr[$i].'">'.$arr[$i].'</option>';
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
	$myear_options = "<option value=\"0000\">0000</option>";
    for($i = $thisyear - 120; $i < $thisyear + 1; $i++)	{
		$myear_options .= '<option value="'.$i.'">'.$i.'</option>';
 	}



?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Member</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jqgrid/themes/coffee/grid.css" />
<script src="../includes/jquery.js" type="text/javascript"></script>
<script src="../includes/jqgrid/jquery.jqGrid.js" type="text/javascript"></script>
<script src="../includes/jqgrid/js/jqDnR.js" type="text/javascript"></script>
<script src="../includes/jqgrid/js/jqModal.js" type="text/javascript"></script>

<script>

window.name = "adddependant";

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
		document.getElementById('mlastname').value = nm[1].toUpperCase();
		document.pdmem.mprefname.focus();
	}
	if (x == 3) {
		document.getElementById('mfirstname').value = changeCase(nm[0]);
		document.getElementById('mmiddlename').value = changeCase(nm[1]);
		document.getElementById('mlastname').value = nm[2].toUpperCase();
		document.pdmem.mprefname.focus();
	}
}

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}


function exit() {
	this.close();
}

function checkdaym(d) {
	if (d < 1 || d > 31) {
		alert('Day is outside acceptable range');
		document.getElementById('mdobday').value = '01';
		document.getElementById('mdobday').focus();
		return false;
	}
}

function checkmonthm(m) {
	if (m < 1 || m > 12) {
		alert('Month is outside acceptable range');
		document.getElementById('mdobmonth').value = '01';
		document.getElementById('mdobmonth').focus();
		return false;
	}
}

function checkyearm(y) {
	var d = new Date();
	var yr = d.getFullYear();	
	
	if (y < (yr-130) || y > yr) {
		alert('Year is outside acceptable range');
		document.getElementById('mdobyear').value = '1900';
		document.getElementById('mdobyear').focus();
		return false;
	}
	var age = calcagem();
}

function calcagem() {
	var d = new Date();
	var curr_date = d.getDate();
	var curr_month = d.getMonth();
	var curr_year = d.getFullYear();
	var birthYear = document.getElementById('mdobyear').value;
	var birthMonth = document.getElementById('mdobmonth').value;
	var birthDay = document.getElementById('mdobday').value;
	var age = curr_year - birthYear;
	if (curr_month < birthMonth || (curr_month == birthMonth && currentDay < birthDay)) {
		age = age - 1;
	}
	
	document.getElementById('mage').value = age;
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


</script>


</head>


<body>
<form name="pdmem" id="pdmem" method="post">

<div id="htop4">

<div id="htop41">
	<table width="1116" border="0" align="center">
		<tr>
		  	<td bgcolor="#0066CC" colspan="4"><div style="color: #FFF;">Member</div></td>
          	<td bgcolor="#0066CC"><div style="color: #FFF;"> Advisor</div></td>
          	<td align="left" bgcolor="#0066CC"><select name="mpadvisor" id="mpadvisor"><?php echo $mpadvisor_options;?></select></td>
	  	</tr>
		<tr>
		  	<td class="boxlabel">Title</td>
		  	<td align="left"><select name="mtitle" id="mtitle"><?php echo $mtitle_options;?></select></td>
	  	  <td class="boxlabel">Date of Birth</td>
		  	<td align="left">dd&nbsp;
            	<select name="mdobday" id="mdobday" tabindex="6"><?php echo $mday_options;?></select>
		  	  &nbsp;
		  	  mm
            	<select name="mdobmonth" id="mdobmonth" tabindex="7"><?php echo $mmonth_options;?></select>
	  	      &nbsp;
	  	      yyyy
            	<select name="myear" id="myear" tabindex="8" onChange="calcagem()"><?php echo $myear_options;?></select>
	  	      &nbsp;Age
              <input name="mage" type="text" id="mage" size="3" maxlength="3" value="0" onChange="checkage('m')"></td>
		 	<td class="boxlabel">Status</td>
		  	<td align="left"><select name="mstatus" id="mstatus"><?php echo $mstatus_options;?></select></td>
	 	</tr>
		<tr>
			<td class="boxlabel"><div align="right">First Name</div></td>
			<td align="left"><input name="mfirstname" id="mfirstname" type="text" size="25" maxlength="45" onBlur="mbreakname(this.value)" onfocus="this.select();"></td>
	  	  <td class="boxlabel">Gender</td>
		  	<td align="left"><select name="mgender" id="mgender"><?php echo $mgender_options;?></select></td>
		  	<td class="boxlabel">Process</td>
		  	<td align="left"><select name="mprocess" id="mprocess"><?php echo $mprocess_options;?></select></td>
		</tr>
		<tr>
		 	<td class="boxlabel">Middle Name</td>
		  	<td align="left"><input name="mmiddlename" id="mmiddlename" type="text" size="25" maxlength="45" onfocus="this.select();"></td>
	  	  <td class="boxlabel">Marital Status</td>
		  	<td align="left"><select name="mmarried" id="mmarried"><?php echo $mmarried_options;?></select></td>
		  	<td class="boxlabel">Client Type</td>
		  	<td align="left"><select name="mclient_type" id="mclient_type"><?php echo $mtype_options;?></select></td>
	  	</tr>
		<tr>
			<td class="boxlabel"><div align="right">Last Name</div></td>
			<td align="left"><input name="mlastname" id="mlastname" type="text" size="25" maxlength="45" onfocus="this.select();"> </td>
	 	  <td class="boxlabel">Smoker</td>
		 	<td align="left"><select name="msmoker" id="msmoker"><?php echo $msmoker_options;?></select></td>
		 	<td class="boxlabel">&nbsp;</td>
		 	<td align="left">&nbsp;</td>
		</tr>
		<tr>
			<td class="boxlabel"><div align="right">Preferred Name</div></td>
			<td align="left"><input name="mprefname" id="mprefname" type="text" size="25" maxlength="45" onfocus="this.select();"></td>
	 	  <td class="boxlabel">Referred By</td>
		 	<td align="left"><select name="mreferred" id="mreferred"><?php echo $mreferred_options;?></select></td>
		 	<td class="boxlabel">Relationship</td>
            <td align="left"><select name="relationship">
              <option value="Son">Son</option>
              <option value="Daughter">Daughter</option>
              <option value="Grandson">Grandson</option>
              <option value="Granddaughter">Granddaughter</option>
              <option value="Nephew">Nephew</option>
              <option value="Niece">Niece</option>
              <option value="Cousin">Cousin</option>
              <option value="Business Associate">Business Associate</option>
              <option value="Father">Father</option>
              <option value="Mother">Mother</option>
              <option value="Grandfather">Grandfather</option>
              <option value="Grandmother">Grandmother</option>
              <option value="Ward">Ward</option>
              <option value="Friend">Friend</option>
              <option value="Other">Other</option>
            </select>
	    </tr>
	</table>
</div>  
 

	
<div id="hbot45">	
   
	<table width="1116" border="0">
		<tr>
	      <td align="left"><input type="button" name="out" id="out" value="Exit without Saving" onClick="exit()"></td>
	      <td align="right"><input type="submit" value="Save" name="save"></td>
		</tr>	
   </table>
   
</div>
</div>
</form>
	<script>document.onkeypress = stopRKey;</script> 

<?php

	if(isset($_POST['save'])) {

		if ($_REQUEST['mlastname'] == '') {
			echo '<script>';
			echo 'alert("Please enter a member name.")';
			echo '</script>';	
		} else {			

				include_once("includes/cltadmin.php");
				$oCn = new cltadmin;	
			
				$oCn->sub_id = $subid;
				$oCn->firstname = $_REQUEST['mfirstname'];
				$oCn->lastname = $_REQUEST['mlastname'];
				$oCn->middlename = $_REQUEST['mmiddlename'];
				$oCn->preferredname = $_REQUEST['mprefname'];
				$oCn->title = $_REQUEST['mtitle'];
				$oCn->client_type_id = $_REQUEST['mclient_type'];
				$oCn->padvisor = $_REQUEST['mpadvisor'];
				$mdob = $_REQUEST['myear'].'-'.$_REQUEST['mdobmonth'].'-'.$_REQUEST['mdobday'];
				$oCn->dob = $mdob;
				$oCn->status_id = $_REQUEST['mstatus'];
				$oCn->gender = $_REQUEST['mgender'];
				$oCn->process_id = $_REQUEST['mprocess'];
				$oCn->married = $_REQUEST['mmarried'];
				$oCn->smoker = $_REQUEST['msmoker'];
				$oCn->referred_id = $_REQUEST['mreferred'];
		 		$oCn->sub_id = $sub_id;
		 		$oCn->age = $_REQUEST['mage'];
				
				
				$cluid = $oCn->AddMember();

				$oCn->policy_type_id = $poltypeid;
				$oCn->member_id = $cluid;
				$oCn->uid = $memid;
				$oCn->relationship = $_REQUEST['relationship'];
				
				$oCn->AddDependant();


				echo '<script>';
				if ($from == 'm') {
					echo 'window.open("","editmembers").jQuery("#mchildlist").trigger("reloadGrid");';
				} else {
					echo 'window.open("","editmembers").jQuery("#pchildlist").trigger("reloadGrid");';
				}
				echo 'this.close();';
				echo '</script>';
			
		}
	}

?>



</body>
</html>
