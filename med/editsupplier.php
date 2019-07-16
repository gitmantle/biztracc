<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

$cluid = $_SESSION['s_memberid'];
$_SESSION['s_sup'] = 'Supplier';

$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subscriber = $subid;
$sname = $uname;

$hdate = date('Y-m-d');
$ttime = strftime("%H:%M", mktime());


// populate staff drop down
$query = "select * from users where sub_id = ".$subscriber." order by ulname";
$result = mysql_query($query) or die(mysql_error().$query);
$staff_options = "<option value=\"0\">Select User</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($row[2]." ".$row[3] == $sname) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$staff_options .= '<option value="'.$row[2].' '.$row[3].'"'.$selected.'>'.$row[2].' '.$row[3].'</option>';
}

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());


$query = "select * from members where member_id = ".$cluid;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$mindustry = $industry_id;
$moccupation = $occupation;
$mposition = $position;
$mfirstname = $firstname;
$mmiddlename = $middlename;
$mpreferredname = $preferredname;
$mlastname = $lastname;
$mdob = split('-',$dob);
$md = $mdob[2];
$mm = $mdob[1];
$my = $mdob[0];
$mgender = $gender;
$mtitle = $title;
$mage = $age;
$mdepot = $depot;
$mgeneric = $generics;
$mch = $checked;



// populate title list
    $arr = array('Mr', 'Mrs', 'Ms', 'Miss', 'Master', 'Dr', 'Professor', 'Reverend');
	$mtitle_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $mtitle) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$mtitle_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate day list
    $arr = array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');
	$mday_options = "<option value=\"00\">00</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $md) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$mday_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate month list
    $arr = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
	$mmonth_options = "<option value=\"00\">00</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $mm) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$mmonth_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate year list
	$thisyear = date("Y");
	if ($my == '0000') {
		$myear_options = "";
		for($i = $thisyear - 120; $i < $thisyear - 50; $i++)	{
			$myear_options .= '<option value="'.$i.'">'.$i.'</option>';
		}
		$myear_options .= "<option value=\"0000\" selected>0000</option>";
		for($i = $thisyear - 50; $i < $thisyear + 1; $i++)	{
			$myear_options .= '<option value="'.$i.'">'.$i.'</option>';
		}
	} else {
		$myear_options = "<option value=\"0000\">0000</option>";
		for($i = $thisyear - 120; $i < $thisyear + 1; $i++)	{
				if ($i == $my) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}	
			$myear_options .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
 		}
	}

// populate gender list
    $arr = array('Male', 'Female');
	$mgender_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $mgender) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$mgender_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate generic list
    $arr = array('No', 'Yes');
	$generic_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $mgeneric) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$generic_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate checked list
    $arr = array('No', 'Yes');
	$mchecked_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $mch) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$mchecked_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
$moduledb = $_SESSION['s_prcdb'];
mysql_select_db($moduledb) or die(mysql_error());
	
// populate depot drop down
$query = "select * from depots";
$result = mysql_query($query) or die(mysql_error());
$depot_options = "<option value=\"0\">Select Depot</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($depot_id == $mdepot) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$depot_options .= '<option value="'.$depot_id.'"'.$selected.'>'.$depot.', '.$stown.'</option>';
}
	
	
	

$_SESSION['s_memberid'] = $cluid;


$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Supplier</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>
<script>

window.name = "editsuppliers";
var dialog;
var subid = <?php echo $subscriber; ?>;
var memid = <?php echo $cluid; ?>;

$(document).ready(function(){
	
   $dialog = $('<div></div>').dialog({autoOpen: false,title: 'Aide Memoir'});

 });



/**
 * DHTML date validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */
// Declaring valid date character, minimum year and maximum year
var dtCh= "/";
var minYear=1900;
var maxYear=2100;

function aidememoir(aide) {
	
	$dialog.dialog('close');
	$dialog.html(aide);
	$dialog.dialog('open');
	// prevent the default action, e.g., following a link
	return false;	
}

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strDay=dtStr.substring(0,pos1)
	var strMonth=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("The date format should be : dd/mm/yyyy")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Please enter a valid day")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Please enter a valid date")
		return false
	}
return true
}

function ValidateDate(dt){
	if (isDate(dt)==false){
		dt.focus()
		return false
	}
    return true
 }

// function hideAll()
//  hides a bunch of divs
//

function hideAllm() {
	changeObjectVisibility("saddresses","hidden");
    changeObjectVisibility("scommunications","hidden");
    changeObjectVisibility("sfinancials","hidden");
}

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}


function editad(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('editsupaddress.php?uid='+uid,'eddad','toolbar=0,scrollbars=1,height=430,width=990,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function delad(uid) {
  if (confirm("Are you sure you want to delete this address")) {
			$.get("includes/ajaxdeladdress.php", {tid: uid}, function(data){$("#madlist2").trigger("reloadGrid")});
	  }
}

function addad(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		var clientid = uid;
		window.open('addsupaddress.php?uid='+uid,'addad','toolbar=0,scrollbars=1,height=430,width=990,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editcomm(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('editsupcomm.php?uid='+uid,'edcom','toolbar=0,scrollbars=1,height=200,width=700,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function delcomm(uid) {
  if (confirm("Are you sure you want to delete this communication")) {
			$.get("includes/ajaxdelcomm.php", {tid: uid}, function(data){$("#mcommslist").trigger("reloadGrid")});
	  }
}

function addcomm(ms) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('addsupcomm.php','adcom','toolbar=0,scrollbars=1,height=200,width=700,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function mapad(address) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('google.php?address='+address,'goog','toolbar=0,scrollbars=1,height=360,width=560,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function exit() {
	var x = confirm('You are about to exit without saving this record. Is that what you want to do?');
	if (x == true) {
		this.close();
	} else {
		return false;
	}
}


// function getStyleObject(string) -> returns style object
//  given a string containing the id of an object
//  the function returns the stylesheet of that object
//  or false if it can't find a stylesheet.  Handles
//  cross-browser compatibility issues.
//
function getStyleObject(objectId) {
  // checkW3C DOM, then MSIE 4, then NN 4.
  //
  if(document.getElementById && document.getElementById(objectId)) {
	return document.getElementById(objectId).style;
   }
   else if (document.all && document.all(objectId)) {  
	return document.all(objectId).style;
   } 
   else if (document.layers && document.layers[objectId]) { 
	return document.layers[objectId];
   } else {
	return false;
   }
}


function changeObjectVisibility(objectId, newVisibility) {
    // first get a reference to the cross-browser style object 
    // and make sure the object exists
    var styleObject = getStyleObject(objectId);
    if(styleObject) {
	styleObject.visibility = newVisibility;
	return true;
    } else {
	// we couldn't find the object, so we can't change its visibility
	return false;
    }
}

function switchDivm(div_id,cell)
{

  var style_sheet = getStyleObject(div_id);
  
  if (style_sheet)
  {

	hideAllm();
    changeObjectVisibility(div_id,"visible");

	if (div_id == "scommunications") {	
		jQuery("#mcommslist").setGridParam({url:"getsupcomms.php"}).trigger("reloadGrid"); 
	}	

	if (div_id == "saddresses") {	
		jQuery("#madlist2").setGridParam({url:"getsupaddress.php"}).trigger("reloadGrid"); 
	}	

	if (div_id == "sfinancials") {	
		jQuery("#mfinancials").setGridParam({url:"getsupfinancials.php"}).trigger("reloadGrid"); 
	}			

} else {
    alert("sorry, this only works in browsers that do Dynamic HTML - Supplier");
  }
}

function editfinancials(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('editfinancials.php?uid='+uid,'edfin','toolbar=0,scrollbars=1,height=300,width=800,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
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
		document.getElementById('edmem').submit();
	}
	
	
}



</script>
<!-- Deluxe Tabs -->
<noscript>
<a href="http://deluxe-tabs.com">Javascript Tabs Menu by Deluxe-Tabs.com</a>
</noscript>
<script type="text/javascript" src="tabs/client_tabs.files/dtabs.js"></script>
<!-- (c) 2009, http://deluxe-tabs.com -->
<style type="text/css">
<!--
.style1 {
	font-size: large
}
.star {
	color: #F00;
}
-->
</style>
</head>


<body>
<form name="edmem" id="edmem" method="post">
 	<input type="hidden" name="savebutton" id="savebutton" value="N">
  <div id="htop1" style="position:absolute;visibility:visible;top:1px;left:1px;height:160px;width:960px;border-width:thin thin thin thin; border-color:#999; border-style:solid;">
    <table width="950" border="0" cellspacing="0" align="left" bgcolor="<?php echo $bgcolor; ?>">
      <tr>
        <td colspan="2" align="left" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>">Supplier:- <?php echo $mlastname.' ('.$cluid.')'; ?></label></td>
        <td align="left" bgcolor="#FF0000"><input type="button" value="Save" name="save" onclick="post()">
          &nbsp;&nbsp;&nbsp;
          <input type="button" name="out" id="out" value="Exit" onClick="exit()"></td>
        <td bgcolor="<?php echo $bghead; ?>"  class="boxlabel"><span style="color: <?php echo $thfont; ?>">Checked &nbsp;</span><select name="mchecked" id="mchecked">
            <?php echo $mchecked_options;?>
          </select></td>
        <td align="center" bgcolor="<?php echo $bghead; ?>">&nbsp;</td>
	  	  <td bgcolor="<?php echo $bghead; ?>" class="boxlabel">&nbsp;</td>
      </tr>
      <tr >
        <td class="boxlabel" ><label style="color: <?php echo $tdfont; ?>">Title</label></td>
        <td align="left"><select name="mtitle" id="mtitle">
            <?php echo $mtitle_options;?>
          </select></td>
        <td class="boxlabel">&nbsp;</td>
        <td colspan="3" class="boxlabelleft">&nbsp;</td>
      </tr>
      <tr>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Contact  Name</label></td>
        <td align="left"><input name="mfirstname" id="mfirstname" type="text" size="25" maxlength="45" value="<?php echo $mfirstname; ?>" tabindex="1" onfocus="this.select();"></td>
        <td class="boxlabel">&nbsp;</td>
        <td colspan="3" align="left"><label style="color: <?php echo $tdfont; ?>">&nbsp;</label></td>
      </tr>
      <tr>
        <td class="boxlabel">&nbsp;</td>
        <td align="left">&nbsp;</td>
        <td class="boxlabel">&nbsp;</td>
        <td align="left">&nbsp;</td>
        <td  class="boxlabel">&nbsp;</td>
        <td align="left">&nbsp;</td>
      </tr>
      <tr>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Company Name</label></td>
        <td align="left"><input name="mlastname" id="mlastname" type="text" size="45" maxlength="45" value="<?php echo $mlastname; ?>" tabindex="3" onfocus="this.select();"></td>
        <td class="boxlabel">&nbsp;</td>
        <td colspan="3" align="left">&nbsp;</td>
      </tr>
      <tr>
        <td class="boxlabel">&nbsp;</td>
        <td align="left">&nbsp;</td>
        <td class="boxlabel">&nbsp;</td>
        <td colspan="3" align="left">&nbsp;</td>
      </tr>
    </table>
  </div>
  <div id="hbot2" style="position:absolute;visibility:visible;top:168px;left:1px;height:210px;width:960px;border-width:thin thin thin thin; border-color:#999; border-style:solid;">
    <table width="950" border="0" align="left">
      <tr>
        <td><script type="text/javascript" src="tabs/supplier_tabsm.js"></script></td>
      </tr>
      <tr>
      <td>
          <div id="scommunications" >
            <table>
              <tr>
                <td><?php include "getsupcomms.php" ?></td>
              </tr>
            </table>
          </div>
           <div id="saddresses">
            <table>
              <tr>
                <td><?php include "getsupaddress.php" ?></td>
              </tr>
            </table>
          </div>
         <div id="sfinancials">
            <table>
              <tr>
                <td><?php include "getfinancials.php" ?></td>
              </tr>
            </table>
          </div>
      </td>
      </tr>
    </table>
  </div>
  <script>
		//hideAllm();
		switchDivm('scommunications','co');
    </script>
  <script>document.onkeypress = stopRKey;</script>
</form>
<?php

	if($_REQUEST['savebutton'] == "Y") {
		include_once("../includes/cltadmin.php");
		$oCn = new cltadmin;	
		
		$oCn->sub_id = $subid;
		$oCn->firstname = ucwords($_REQUEST['mfirstname']);
		$oCn->lastname = ucwords($_REQUEST['mlastname']);
		$oCn->middlename = '';
		$oCn->preferredname = '';
		$oCn->title = '';
		$oCn->occupation = '';
		$mdob = '0000-00-00';
		$oCn->dob = $mdob;
		$oCn->position = '';
		$oCn->gender = '';
		$oCn->age = 0;
		$oCn->checked = $_REQUEST['mchecked'];
		$oCn->depot = 0;
		$oCn->generic = '';
		
		$oCn->uid = $cluid;
		
		$oCn->EditMember();
		
		$hdate = date('Y-m-d');
		$ttime = strftime("%H:%M", mktime());
		
		$query = "insert into audit (ddate,ttime,user_id,uname,sub_id,member_id,action) values ";
		$query .= "('".$hdate."',";
		$query .= "'".$ttime."',";
		$query .= $user_id.",";
		$query .= "'".$uname."',";
		$query .= $sub_id.",";
		$query .= $cluid.",";
		$query .= "'Edit Member')";
		
		$result = mysql_query($query) or die(mysql_error().$query);

		echo '<script>';
		echo 'window.open("","updtsuppliers").jQuery("#suplist2").trigger("reloadGrid");';
		echo 'this.close();';
		echo '</script>';
			
	}

?>
</body>
</html>
