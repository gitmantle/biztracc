<?php
session_start();
$rid = $_SESSION['s_route'];
$hid = $_REQUEST['hid'];

$admindb = $_SESSION['s_admindb'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select * from site_hazards where uid = ".$hid;
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$r = $risk;
$s = $strategy;
$l = $local;
$m = $management;
$h = $harm;
$d = $damage;
$r = $reoccur;


// populate risk list
    $arr = array('Low','Moderate','High');
	$risk_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $r) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		$risk_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate strategy list
    $arr = array('Eliminate','Isolate','Minimise');
	$strategy_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $s) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		$strategy_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate local list
    $arr = array('No','Yes');
	$local_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $l) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		$local_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate management list
    $arr = array('No','Yes');
	$management_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $m) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		$management_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate harm list
    $arr = array('None','Insignificant','Minor','Temporary harm','Serious harm','Fatalities');
	$harm_options = "";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $h) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$harm_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate damage list
    $arr = array('None','Under $100','Under $1,000','Under $5,000','Under $50,000','Over $50,000');
	$damage_options = "";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $d) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$damage_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate reocurr list
    $arr = array('Rare','Possible','Moderate','Likely','Certain');
	$reoccur_options = "";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $r) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$reoccur_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

date_default_timezone_set($_SESSION['s_timezone']);

$dt = split('-',$ddate);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$fdt = mktime(0,0,0,$m,$d,$y);
$ddate = date("d/m/Y",$fdt);
$hdate = date("Y-m-d",$fdt);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Hazard</title>
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
	var hazard = document.getElementById('hazard').value;
	
	var ok = "Y";
	if (hazard == "") {
		alert("Please enter a hazard.");
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
-->
</style>
</head>


<body>
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
 <table width="700" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Edit Hazard </strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Date</label></td>
      <td><input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
      </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Hazard</label></td>
      <td><textarea name="hazard" id="hazard" cols="45" rows="5"><?php echo $hazard; ?></textarea></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Risk</label></td>
      <td><select name="risk_options" id="risk_options"><?php echo $risk_options;?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Strategy</label></td>
      <td><select name="strategy_options" id="strategy_options"><?php echo $strategy_options;?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Actions to control hazard</label></td>
      <td><textarea name="action" id="action" cols="45" rows="5"><?php echo $action; ?></textarea></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Local</label></td>
      <td><select name="local_options" id="local_options"><?php echo $local_options;?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Management</label></td>
      <td><select name="management_options" id="management_options"><?php echo $management_options;?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Likelihood of harm to people</td>
      <td><select name="harm" id="harm"><?php echo $harm_options;?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Likelihood of damage to property</td>
      <td><select name="damage" id="damage"><?php echo $damage_options;?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Likelihood of reoccurence</td>
      <td><select name="reoccur" id="reoccur"><?php echo $reoccur_options;?>
      </select></td>
    </tr>
	<tr>
      <td align="right" colspan="2">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  	</table>
</form>

<?php

	if($_REQUEST['savebutton'] == "Y") {
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
		$ddate = $y.'-'.$m.'-'.$d;		  
		$hazard = $_REQUEST['hazard'];
		$risk = $_REQUEST['risk_options'];
		$strategy = $_REQUEST['strategy_options'];
		$action = $_REQUEST['action'];
		$local = $_REQUEST['local_options'];
		$management = $_REQUEST['management_options'];
		$harm = $_REQUEST['harm'];
		$damage = $_REQUEST['damage'];
		$reoccur = $_REQUEST['reoccur'];

		$q = "update site_hazards set ";
		$q .= 'hazard = "'.$hazard.'",';
		$q .= 'risk = "'.$risk.'",';
		$q .= 'strategy = "'.$strategy.'",';
		$q .= 'action = "'.$action.'",';
		$q .= 'local = "'.$local.'",';
		$q .= 'management = "'.$management.'",';
		$q .= 'harm = "'.$harm.'",';
		$q .= 'damage = "'.$damage.'",';
		$q .= 'reoccur = "'.$reoccur.'",';
		$q .= 'ddate = "'.$ddate.'"';
		$q .= ' where uid = '.$hid;

		$r = mysql_query($q) or die(mysql_error().$q);
		

	  ?>
	  <script>
	  window.open("","editroute").jQuery("#hazardlist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
