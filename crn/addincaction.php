<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];
$coyname = $_SESSION['s_coyname'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;
$compiler = $uname;

// populate driver drop down
$query = "select uid,concat_ws(' ',ufname,ulname) as fname from users where sub_id = ".$subscriber;
$result = mysql_query($query) or die(mysql_error().$query);
$op_options = "<option value=\"\">Select Person</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$op_options .= '<option value="'.$fname.'"'.$selected.'>'.$fname.'</option>';
}



$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());


date_default_timezone_set($_SESSION['s_timezone']);

$ddate = '00/00/00';
$hdate = '0000-00-00';

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Action</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>

<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function post() {

	//add validation here if required.
	var action = document.getElementById('action').value;
	var bywhom = document.getElementById('loperator').value;
	
	var ok = "Y";
	if (action == "") {
		alert("Please enter an action.");
		ok = "N";
		return false;
	}
	if (bywhom == "") {
		alert("Please enter the person responsible.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
	
	
}



</script>

<style type="text/css">
<!--
.style1 {font-size: large}
.italic {
	font-style: italic;
}
-->
</style>
</head>


<body>
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
 <table width="690" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>;font-size:14px"><strong>Add an Action to be taken to prevent reoccurence</strong></label></td>
    </tr>
    <tr>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Action to be undertaken</label></td>
      <td class="boxlabelleft"><textarea name="action" id="action" cols="50" rows="5"></textarea></td>
    </tr>
    <tr>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">By whom</label></td>
      <td class="boxlabelleft"><select name="loperator" id="loperator">
      	<?php echo $op_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Date Completed </label></td>
      <td class="boxlabelleft"><input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
      </tr>
	<tr>
      <td align="right" colspan="2">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  	</table>
</form>
	<script>
	document.onkeypress = stopRKey;
    </script> 

<?php

	if($_REQUEST['savebutton'] == "Y") {
		$action = $_REQUEST['action'];
		$bywhom = $_REQUEST['loperator'];
		$odt = $_REQUEST['ddate'];
		$t = explode('/',$odt);
		$d = $t[0];
		if (strlen($d) == 1) {
			$d = '0'.$d;
		}
		$m = $t[1];
		if (strlen($m) == 1) {
			$m = '0'.$m;
		}
		$y = $t[2];
		$dt = $y.'-'.$m.'-'.$d;		  
		
		$id = $_SESSION['s_incidentid'];
		
		$q = "insert into incactions (incident_id,action,bywhom,date_done) values (";
		$q .= $id.',';
		$q .= '"'.$action.'",';
		$q .= '"'.$bywhom.'",';
		$q .= '"'.$dt.'")';

		$r = mysql_query($q) or die(mysql_error().$q);
		$incid = mysql_insert_id();
		
	  ?>
	  <script>
	  window.open("","editincident").jQuery("#incactionlist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
			
	}

?>


</body>
</html>
