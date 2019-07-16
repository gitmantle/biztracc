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

$q = "select concat(ufname,' ',ulname) as fname, notifyruc from users where sub_id = ".$subscriber." and notifyruc <> ''";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$rucname = $fname;
$rucemail = $notifyruc;

$q = "select concat(ufname,' ',ulname) as fname, notifyincident from users where sub_id = ".$subscriber." and notifyincident <> ''";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$incname = $fname;
$incemail = $notifyincident;


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

window.name = "hs_setup";

</script>
</head>
<body>
<form name="form1" method="post" >
  <input type="hidden" name="savebutton" id="savebutton" value="N">
          <table width="700" border="0" align="center" cellpadding="3" cellspacing="1">
            <tr>
              <td class="boxlabel">Company name</td>
              <td colspan="2"><input type="text" name="coyname" id="coyname" value="<?php echo $coyname; ?>" size="45" maxlength="45" readonly>
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
              <td><input name="cofname" type="text" size="30" value="<?php echo $coyofname; ?>" readonly></td>
              <td class="boxlabel">Owner Last Name</td>
              <td><input name="colname" type="text" size="30" value="<?php echo $coyolname; ?>" readonly></td>
            </tr>
            <tr>
              <td class="boxlabel">Street address</td>
              <td><input name="csad1" type="text" size="30" value="<?php echo $coysad1; ?>" readonly></td>
              <td class="boxlabel">PO Box</td>
              <td><input name="cpad1" type="text" size="15" value="<?php echo $coypad1; ?>" readonly></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input name="csad2" type="text" size="30" value="<?php echo $coysad2; ?>" readonly></td>
              <td class="boxlabel">Post Office</td>
              <td><input name="cpad2" type="text" size="15" value="<?php echo $coypad2; ?>" readonly></td>
            </tr>
            <tr>
              <td class="boxlabel">Town</td>
              <td><input name="cstown" type="text" size="30" value="<?php echo $coysad3; ?>" readonly></td>
              <td class="boxlabel">Town</td>
              <td><input name="cptown" type="text" size="15" value="<?php echo $coyptown; ?>" readonly></td>
            </tr>
            <tr>
              <td class="boxlabel">Post Code</td>
              <td><input name="cspc" type="text" size="30" value="<?php echo $coyspostcode; ?>" readonly></td>
              <td class="boxlabel">Post Code</td>
              <td><input name="cppc" type="text" size="15" value="<?php echo $coyppostcode; ?>" readonly></td>
            </tr>
            <tr>
              <td class="boxlabel">Country</td>
              <td><input name="country" type="text" size="30" value="<?php echo $coycountry; ?>" readonly></td>
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
              <td><input name="email" type="text" size="40" value="<?php echo $coyemail; ?>" readonly></td>
              <td class="boxlabel">Phone</td>
              <td><input name="telno" type="text" size="15" value="<?php echo $coyphone; ?>" readonly></td>
            </tr>
            <tr>
              <td class="boxlabel">Notify for RUCs</td>
              <td><input type="text" name="truc" id="truc" size="40" value="<?php echo $rucname; ?>" readonly></td>
              <td colspan="2"><input type="text" name="eruc" id="eruc" size="40" value="<?php echo $rucemail; ?>" readonly></td>
            </tr>
            <tr>
              <td class="boxlabel">Notify for Incidents</td>
              <td><input type="text" name="tinc" id="tinc" size="40" value="<?php echo $incname; ?>" readonly></td>
              <td colspan="2"><input type="text" name="einc" id="einc" size="40" value="<?php echo $incemail; ?>" readonly></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td align="right"><input type="button" value="Edit" name="save" onclick="hs_edit()"></td>
            </tr>
          </table>
</form>

</body>
</html>
