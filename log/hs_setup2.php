<?php
session_start();
$coyid = $_SESSION['s_coyid'];

require_once("../db.php");

$moduledb = $_SESSION['s_admindb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$q = "select uid,concat(ufname,' ',ulname) as fname, notifyruc from users where sub_id = ".$subscriber;
$r = mysql_query($q) or die(mysql_error());
$ruc_options = "<option value=\"0\">Select Staff Member</option>";
while ($row = mysql_fetch_array($r)) {
	extract($row);
		if ($notifyruc != '') {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
	$ruc_options .= '<option value="'.$uid.'"'.$selected.'>'.$fname.'</option>';
}

$q = "select uid,concat(ufname,' ',ulname) as fname, notifyincident from users where sub_id = ".$subscriber;
$r = mysql_query($q) or die(mysql_error());
$inc_options = "<option value=\"0\">Select Staff Member</option>";
while ($row = mysql_fetch_array($r)) {
	extract($row);
		if ($notifyincident != '') {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
	$inc_options .= '<option value="'.$uid.'"'.$selected.'>'.$fname.'</option>';
}

$query = "select coyname,coyofname,coyolname,coypad1,coypad2,coypad3,coyptown,coyppostcode,coysad1,coysad2,coysad3,coystown,coyspostcode,coyphone,coyemail,coycountry from companies where coyid = ".$coyid;
$compdetails = mysql_query($query) or die(mysql_error());
$globals = mysql_fetch_array($compdetails); 
extract($globals);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<title>Company Details Setup</title>
<script type="text/javascript">

window.name = "hs_setup2";

function post() {
	//add validation here if required.
	var lname = document.getElementById('coyname').value;
	var ok = "Y";
	if (lname == "") {
		alert("Please enter a Company name.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('sup').submit();
	}
}

</script>
</head>
<body>
<form name="sup"  id="sup" method="post" >
  <input type="hidden" name="savebutton" id="savebutton" value="N">
          <table width="800" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="<?php echo $bgcolor; ?>">
            <tr>
              <td class="boxlabel">Company name</td>
              <td colspan="2"><input type="text" name="coyname" id="coyname" value="<?php echo $coyname; ?>" size="45" maxlength="45">
                <span class="star">*</span></td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td class="boxlabel">Owner First Name</td>
              <td><input name="cofname" type="text" size="30" value="<?php echo $coyofname; ?>"></td>
              <td class="boxlabel">Owner Last Name</td>
              <td><input name="colname" type="text" size="30" value="<?php echo $coyolname; ?>"></td>
            </tr>
            <tr>
              <td class="boxlabel">Street address</td>
              <td><input name="csad1" type="text" size="30" value="<?php echo $coysad1; ?>"></td>
              <td class="boxlabel">PO Box</td>
              <td><input name="cpad1" type="text" size="15" value="<?php echo $coypad1; ?>"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input name="csad2" type="text" size="30" value="<?php echo $coysad2; ?>"></td>
              <td class="boxlabel">Post Office</td>
              <td><input name="cpad2" type="text" size="15" value="<?php echo $coypad2; ?>"></td>
            </tr>
            <tr>
              <td class="boxlabel">Town</td>
              <td><input name="cstown" type="text" size="30" value="<?php echo $coysad3; ?>"></td>
              <td class="boxlabel">Town</td>
              <td><input name="cptown" type="text" size="15" value="<?php echo $coyptown; ?>"></td>
            </tr>
            <tr>
              <td class="boxlabel">Post Code</td>
              <td><input name="cspc" type="text" size="30" value="<?php echo $coyspostcode; ?>"></td>
              <td class="boxlabel">Post Code</td>
              <td><input name="cppc" type="text" size="15" value="<?php echo $coyppostcode; ?>"></td>
            </tr>
            <tr>
              <td class="boxlabel">Country</td>
              <td><input name="country" type="text" size="30" value="<?php echo $coycountry; ?>"></td>
              <td class="boxlabel">&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td class="boxlabel">&nbsp;</td>
              <td>&nbsp;</td>
              <td class="boxlabel">&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td class="boxlabel">Main email</td>
              <td><input name="email" type="text" size="40" value="<?php echo $coyemail; ?>"></td>
              <td class="boxlabel">Phone</td>
              <td><input name="telno" type="text" size="15" value="<?php echo $coyphone; ?>"></td>
            </tr>
            <tr>
              <td class="boxlabel">Notify for RUCs</td>
              <td><select name="ruc_options" id="ruc_options">
				<?php echo $ruc_options; ?>
              </select></td>
              <td colspan="2"><input type="text" name="rucemail" id="rucemail" size="50"></td>
            </tr>
            <tr>
              <td class="boxlabel">Notify for Incidents</td>
              <td><select name="inc_options" id="inc_options">
				<?php echo $inc_options; ?>
              </select></td>
              <td colspan="2"><input type="text" name="incemail" id="incemail" size="50"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td align="right"><input type="button" value="Save" name="save" onclick="post()"></td>
            </tr>
          </table>
</form>
</div>
<?php
	if($_REQUEST['savebutton'] == "Y") {
		$cname = $_REQUEST['coyname'];
		$cofname = $_REQUEST['cofname'];
		$colname = $_REQUEST['colname'];
		$csad1 = $_REQUEST['csad1'];
		$csad2 = $_REQUEST['csad2'];
		$cstown = $_REQUEST['cstown'];
		$cpad1 = $_REQUEST['cpad1'];
		$cpad2 = $_REQUEST['cpad2'];
		$cptown = $_REQUEST['cptown'];
		$cspc = $_REQUEST['cspc'];
		$cppc = $_REQUEST['cppc'];
		$country = $_REQUEST['country'];
		$cemail = $_REQUEST['email'];
		$phone = $_REQUEST['telno'];
		
		$rn = $_REQUEST['ruc_options'];
		$in = $_REQUEST['inc_options'];
		$rmail = $_REQUEST['rucemail'];
		$imail = $_REQUEST['incemail'];
		
		
		$q = "update companies set ";
		$q .= "coyname = '".$cname."',";
		$q .= "coyofname = '".$cofname."',";
		$q .= "coyolname = '".$colname."',";
		$q .= "coypad1 = '".$cpad1."',";
		$q .= "coypad3 = '".$cpad2."',";
		$q .= "coyptown = '".$cptown."',";
		$q .= "coysad1 = '".$csad1."',";
		$q .= "coysad2 = '".$csad2."',";
		$q .= "coystown = '".$cstown."',";
		$q .= "coyppostcode = '".$cppc."',";
		$q .= "coyspostcode = '".$cspc."',";
		$q .= "coycountry = '".$country."',";
		$q .= "coyemail = '".$cemail."',";
		$q .= "coyphone = '".$phone."'";
		$q .= " where coyid = ".$coyid;
		
		$r = mysql_query($q) or die (mysql_error().' '.$q);
		
		$q = "update users set ";
		$q .= "notifyruc = '' where sub_id = ".$subscriber;
		$r = mysql_query($q) or die (mysql_error().' '.$q);
		$q = "update users set ";
		$q .= "notifyruc = '".$rmail."' where uid = ".$rn;
		$r = mysql_query($q) or die (mysql_error().' '.$q);
		
		$q = "update users set ";
		$q .= "notifyincident = '' where sub_id = ".$subscriber;
		$r = mysql_query($q) or die (mysql_error().' '.$q);
		$q = "update users set ";
		$q .= "notifyincident = '".$imail."' where uid = ".$in;
		$r = mysql_query($q) or die (mysql_error().' '.$q);
		
		echo '<script>';
		echo 'this.close();';
		echo '</script>';
		
					
}


?>

</body>
</html>
