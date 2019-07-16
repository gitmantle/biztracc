<?php
session_start();
ini_set('display_errors', true);

$usersession = $_COOKIE['usersession'];
$dbase = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../db.php");
mysql_select_db($dbase) or die(mysql_error());
$query = "select * from sessions where session_id = ".$usersession;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subid = $sub_id;

$refid = $_REQUEST['uid'];

$q = "select * from referrals where referral_id = ".$refid;
$r = mysql_query($q) or die(mysql_error().$q);
$row = mysql_fetch_array($r);
extract($row);
$refby = $referred_id;
$staffid = $staff_id;

$q = "select concat_ws(' ',staff.firstname,staff.lastname) as staffname from staff where staff_id = ".$staffid;
$r = mysql_query($q) or die(mysql_error().$q);
$row = mysql_fetch_array($r);
extract($row);

// populate referred drop down
$query = "select * from referred where sub_id = ".$subid." order by referred";
$result = mysql_query($query) or die(mysql_error().$query);
$mreferred_options = "<option value=\"0\">Select Referred By</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($referred_id == $refby) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$mreferred_options .= '<option value="'.$referred_id.'"'.$selected.'>'.$referred.'</option>';
}

// populate title list
    $arr = array('Mr', 'Mrs', 'Ms', 'Miss', 'Master', 'Dr', 'Professor', 'Reverend');
	$mtitle_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $title) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$mtitle_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate gender list
    $arr = array('Male', 'Female');
	$mgender_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $gender) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$mgender_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}


// populate comms type drop down
$query = "select * from comms_type";
$result = mysql_query($query) or die(mysql_error());
$commtype_options = "<option value=\"\">Select Communication Type</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($comms_type_id == $commtype) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$commtype_options .= "<option value=\"".$comms_type_id."\" ".$selected.">".$comm_type."</option>";
}

// populate comms type drop down
$query = "select * from comms_type";
$result = mysql_query($query) or die(mysql_error());
$commtype2_options = "<option value=\"\">Select Communication Type</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($comms_type_id == $commtype2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$commtype2_options .= "<option value=\"".$comms_type_id."\" ".$selected.">".$comm_type."</option>";
}


// populate address type drop down
$query = "select * from address_type";
$result = mysql_query($query) or die(mysql_error());
$adtype_options = "<option value=\"\">Select Address Type</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($address_type_id == $addresstype) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$adtype_options .= "<option value=\"".$address_type_id."\" ".$selected.">".$address_type."</option>";
}


$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");




?>
<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit New Lead</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="includes/ajaxGetmhPostCode.js"></script>
<script type="text/javascript" src="includes/ajaxGetphPostCode.js"></script>
<script>

window.name = "editreferral";


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
		document.edreferral.mprefname.focus();
	}
	if (x == 3) {
		document.getElementById('mfirstname').value = changeCase(nm[0]);
		document.getElementById('mmiddlename').value = changeCase(nm[1]);
		document.getElementById('mlastname').value = nm[2];
		document.edreferral.mprefname.focus();
	}
}



function addreferred(mp) {
	if (mp == 'm') {
		var m = document.getElementById('mreferred');
		var listlengthm = document.getElementById("mreferred").length;
		var newref = document.getElementById('mref').value;
	}
	if (newref != '') {
		$.get("addreferred.php", {newref: newref}, function(data){
			document.getElementById('mreferred').add(new Option(newref,data), null);
			document.getElementById('mref').value = '';
		});
	}

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


function post() {

	//add validation here if required.
	var lname = document.getElementById('mlastname').value;
	var commtype = document.getElementById('mcomm_type').value;
	var comm = document.getElementById('mcomm').value;
	var streetno = document.getElementById('mstreetno').value;
	var street = document.getElementById('mstreet').value;
	var suburb = document.getElementById('msuburb').value;
	var town = document.getElementById('mtown').value;
	
	var ok = "Y";
	if (lname == "") {
		alert("Please enter a last name.");
		ok = "N";
		return false;
	}
	if ((commtype == 0 || comm == "") && (streetno == "" || street == "" || suburb == "" || town == "")) {
		alert("Please enter a complete communication item or address");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('edreferral').submit();
	}
	
	
}

function mgetpc() {
	var adloc = "Street";
	var adtype = 1;
	var stno = document.getElementById('mstreetno').value;
	var sad1 = document.getElementById('mstreet').value;
	
	ajaxGetmhPostCode(adloc,adtype,stno,sad1," "," "," "," "," ");

}


function mpopads(ad) {
	var addr = ad.split('~');
	document.getElementById('mstreet').value = addr[1];
	document.getElementById('msuburb').value = addr[2];
	document.getElementById('mtown').value = addr[3];
	document.getElementById('mpostcode').value = addr[4];
}




</script>
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
<form name="edreferral" id="edreferral" method="post">
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <div id="htop41" style="position:absolute;visibility:visible;top:1px;left:1px;height:160px;width:1000px;border-width:thin thin thin thin; border-color:#999; border-style:solid;">
    <table width="1000" border="0" align="left" bgcolor="<?php echo $bgcolor; ?>">
      <tr>
        <td colspan="2" align="left" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size: 14px;">Lead</label></td>
        <td align="left" bgcolor="#FF0000"><input type="button" value="Save" name="save" id="save" onClick="post()">
          &nbsp;&nbsp;
          <input type="button" name="out" id="out" value="Exit" onClick="exit()"></td>
        <td align="left" bgcolor="<?php echo $bghead; ?>">&nbsp;</td>
      </tr>
      <tr>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Title</label></td>
        <td align="left"><select name="mtitle" id="mtitle" tabindex="1">
            <?php echo $mtitle_options;?>
          </select></td>
        <td class="boxlabel">Advisor</td>
        <td><input type="text" value="<?php echo $staffname; ?>" readonly></td>
      </tr>
      <tr>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">First Name</label></td>
        <td align="left"><input name="mfirstname" id="mfirstname" type="text" size="25" maxlength="45"tabindex="2" value="<?php echo $firstname; ?>" onfocus="this.select();" onBlur="mbreakname(this.value)"  ></td>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Gender</label></td>
        <td align="left"><select name="mgender" id="mgender">
            <?php echo $mgender_options;?>
          </select></td>
      </tr>
      <tr>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Middle Name</label></td>
        <td align="left"><input name="mmiddlename" id="mmiddlename" type="text" size="25" maxlength="45" tabindex="3"  value="<?php echo $middlename; ?>"onfocus="this.select();"></td>
        <td class="boxlabel">&nbsp;</td>
        <td align="left">&nbsp;</td>
      </tr>
      <tr>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Last Name</label></td>
        <td align="left"><input name="mlastname" id="mlastname" type="text" size="25" maxlength="45" tabindex="4"  value="<?php echo $lastname; ?>"onfocus="this.select();">
          <label class="star" >*</label></td>
        <td class="boxlabel">Referred By</td>
        <td align="left"><select name="mreferred" id="mreferred">
          <?php echo $mreferred_options;?>
        </select>
        <input type="text" name="mref" id="mref">
        <input type="button" name="bmreferred" id="bmreferred" value="Add" onClick="addreferred('m')"></td>
      </tr>
      <tr>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Preferred Name</label></td>
        <td align="left"><input name="mprefname" id="mprefname" type="text" size="25" maxlength="45" tabindex="5"  value="<?php echo $preferred; ?>"onfocus="this.select();"></td>
        <td class="boxlabel">&nbsp;</td>
        <td align="left" >&nbsp;</td>
      </tr>
    </table>
  </div>
  <div id="hbot42" style="position:absolute;visibility:visible;top:171px;left:1px;height:260px;width:1000px;border-width:thin thin thin thin; border-color:#999; border-style:solid;background-color: <?php echo $bgcolor; ?>">
    <table width="490" border="0" align="left">
      <tr>
        <td colspan="3"><label style="font-size: 14px;"><u>Add Communication Item </u></label></td>
      </tr>
      <tr>
        <td class="boxlabel">Type</td>
        <td colspan="2"><select name="mcomm_type" id="mcomm_type">
            <?php echo $commtype_options; ?>
          </select>
          <span class="star"> *</span></td>
      </tr>
      <tr>
        <td class="boxlabel">Country Code</td>
        <td colspan="2"><input name="mcountry_code" type="text" id="mcountry_code"  size="15" maxlength="15" value="<?php echo $country; ?>" ></td>
      </tr>
      <tr>
        <td class="boxlabel">Area Code</td>
        <td colspan="2"><input name="marea_code" type="text" id="marea_code"  size="15" maxlength="15" value="<?php echo $area; ?>"></td>
      </tr>
      <tr>
        <td class="boxlabel">Number/Details</td>
        <td colspan="2"><input name="mcomm" type="text" id="mcomm"  size="55" maxlength="75" onfocus="this.select();" value="<?php echo $comm; ?>">
          <span class="star">*</span></td>
      </tr>
      <tr>
        <td class="boxlabel">Type</td>
        <td colspan="2"><select name="mcomm_type2" id="mcomm_type2">
            <?php echo $mcommtype2_options; ?>
          </select>
      </tr>
      <tr>
        <td class="boxlabel">Country Code</td>
        <td colspan="2"><input name="mcountry_code2" type="text" id="mcountry_code2"  size="15" maxlength="15" value="<?php echo $country2; ?>" ></td>
      </tr>
      <tr>
        <td class="boxlabel">Area Code</td>
        <td colspan="2"><input name="marea_code2" type="text" id="marea_code2"  size="15" maxlength="15" value="<?php echo $area2; ?>"></td>
      </tr>
      <tr>
        <td class="boxlabel">Number/Details</td>
        <td colspan="2"><input name="mcomm2" type="text" id="mcomm2"  size="55" maxlength="75" onfocus="this.select();" value="<?php echo $comm2; ?>">
      </tr>
    </table>
    <table width="500" border="0" align="right">
      <tr>
        <td align="left"><label style="font-size: 14px;"><u>Physical  Address</u></label></td>
        <td align="left"><label style="font-size: 14px;">Select type</label>
          <select name="mhw" id="mhw">
          <?php echo $adtype_options;?>
        </select></td>
      </tr>
      <tr>
        <td class="boxlabel">Street No/Building</td>
        <td><input name="mstreetno" type="text" id="mstreetno" size="60" onfocus="this.select();" value="<?php echo $streetno; ?>"></td>
      </tr>
      <tr>
        <td class="boxlabel">Street</td>
        <td><input name="mstreet" type="text" id="mstreet" onfocus="this.select();"  value="<?php echo $ad1; ?>" size="60"></td>
      </tr>
      <tr>
        <td class="boxlabel">Suburb</td>
        <td><input name="msuburb" type="text" id="msuburb" size="60" onfocus="this.select();" value="<?php echo $suburb; ?>"></td>
      </tr>
      <tr>
        <td class="boxlabel">Town</td>
        <td><input name="mtown" type="text" id="mtown" size="60" onfocus="this.select();" value="<?php echo $town; ?>"></td>
      </tr>
      <tr>
        <td class="boxlabel">Post Code</td>
        <td><input name="mpostcode" type="text" id="mpostcode" size="60" value="<?php echo $postcode; ?>"></td>
      </tr>
      <tr id="mpc">
        <td class="boxlabel"><input type="button" name="pcode" id="pcode" value="Get Post Code" onClick="mgetpc(); return false;"></td>
        <td><select name="mpclist" id="mpclist" onChange="mpopads(this.value);">
          </select></td>
      </tr>
      <tr>
        <td class="boxlabel">Note</td>
        <td><textarea name="tnote" id="tnote" cols="45" rows="2"><?php echo $note; ?></textarea></td>
      </tr>
    </table>
  </div>
</form>
<script>document.onkeypress = stopRKey;</script>
<?php
	if($_REQUEST['savebutton'] == "Y") {
		
		include_once("includes/cltadmin.php");
		$oCn = new cltadmin;	
		
		$oCn->firstname = $_REQUEST['mfirstname'];
		$oCn->lastname = $_REQUEST['mlastname'];
		$oCn->middlename = $_REQUEST['mmiddlename'];
		$oCn->preferredname = $_REQUEST['mprefname'];
		$oCn->title = $_REQUEST['mtitle'];
		$oCn->gender = $_REQUEST['mgender'];
		$oCn->referred_id = $_REQUEST['mreferred'];
		$oCn->comms_type_id = $_REQUEST['mcomm_type'];
		$oCn->country_code = $_REQUEST['mcountry_code'];
		$oCn->area_code = $_REQUEST['marea_code'];
		$oCn->comm = $_REQUEST['mcomm'];
		$oCn->loc = 'Street';
		$oCn->address_type_id = $_REQUEST['mhw'];
		$oCn->street_no = $_REQUEST['mstreetno'];
		if ($_REQUEST['mstreet'] == "Enter the street then press Get Post Code") {
			$oCn->ad1 = "";
		} else {
			$oCn->ad1 = $_REQUEST['mstreet'];
		}
		$oCn->suburb = ucwords(strtolower($_REQUEST['msuburb']));
		$oCn->town = ucwords(strtolower($_REQUEST['mtown']));
		$oCn->postcode = $_REQUEST['mpostcode'];
		$oCn->country = $_REQUEST['mcountry'];
		$oCn->referralid = $refid;
		$oCn->note = $_REQUEST['tnote'];

		$x = $oCn->EditReferral();

		echo '<script>';
		echo 'window.open("","updtreferrals").jQuery("#referrallist").trigger("reloadGrid");';
		echo 'this.close();';
		echo '</script>;';
			
	}

?>
</body>
</html>
