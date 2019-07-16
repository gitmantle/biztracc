<?php
session_start();

$incid = $_SESSION['s_incidentid'];
$admindb = $_SESSION['s_admindb'];
$id = $_REQUEST['uid'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select * from incpeople where uid = ".$id;
$r = mysql_query($q) or die (mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$id = $uid;
$ishift = $shift;
$iinvolve = $involvment;

// populate shift list
    $arr = array('None','Morning','Afternoon','Night');
	$shift_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $ishift) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
		$shift_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate involvment list
    $arr = array('Injured','Operator','Witness');
	$involve_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $iinvolve) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
		$involve_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}


date_default_timezone_set($_SESSION['s_timezone']);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit People Involved</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>

<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function post() {

	//add validation here if required.
	var name = document.getElementById('name').value;
	
	var ok = "Y";
	if (name == "") {
		alert("Please enter a name.");
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
 <table width="690" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="3" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Edit Person Involved </strong></label></td>
    </tr>
    <tr>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Name</label></td>
      <td colspan="2"><input name="name" type="text" id="name" size="50" value="<?php echo $name; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabelleft">Involvment</td>
      <td colspan="2"><select name="involve" id="involve">
      	<?php echo $involve_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Shift</label></td>
      <td><select name="shift" id="shift">
                  <?php echo $shift_options; ?>
                </select></td>
      <td class="boxlabelleft">Start time
      <input type="text" name="stime" id="stime" size="6" value="<?php echo $starttime; ?>">
      hh:mm</td>
      </tr>
	<tr>
	  <td class="boxlabelleft">Operation</td>
	  <td colspan="2"><input type="text" name="operation" id="operation" size="50" value="<?php echo $operation; ?>"></td>
    </tr>
	<tr>
	  <td class="boxlabelleft">Applicable Qualifications</td>
	  <td colspan="2"><textarea name="qualifications" id="qualifications" cols="50" rows="5"><?php echo $qualifications; ?></textarea></td>
    </tr>
	<tr>
	  <td class="boxlabelleft">Experience in industry</td>
	  <td colspan="2" class="boxlabelleft"><input type="text" name="indy" id="indy" size="3" value="<?php echo $indexpy; ?>">
	    Years
	      <input type="text" name="indm" id="indy" size="3" value="<?php echo $indexpm; ?>">
	      Months
	      <input type="text" name="indd" id="indd" size="3" value="<?php echo $indexpd; ?>">
      Days</td>
    </tr>
	<tr>
	  <td class="boxlabelleft">Experience in job</td>
	  <td colspan="2" class="boxlabelleft"><input type="text" name="joby" id="joby" size="3" value="<?php echo $jobexpy; ?>">
	    Years
	      <input type="text" name="jobm" id="joby" size="3" value="<?php echo $jobexpm; ?>">
	      Months
	      <input type="text" name="jobd" id="jobd" size="3" value="<?php echo $jobexpd; ?>">
      Days</td>
    </tr>
	<tr>
      <td colspan="3" align="right">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  	</table>
</form>

<?php

	if($_REQUEST['savebutton'] == "Y") {
		$name = $_REQUEST['name'];
		$shift = $_REQUEST['shift'];
		$involve = $_REQUEST['involve'];
		$stime = $_REQUEST['stime'];
		$operation = $_REQUEST['operation'];
		$quals = $_REQUEST['qualifications'];
		if ($_REQUEST['indy'] == "") {
			$indy = 0;
		}else {
			$indy = $_REQUEST['indy'];
		}
		if ($_REQUEST['indm'] == "") {
			$indm = 0;
		}else {
			$indm = $_REQUEST['indm'];
		}
		if ($_REQUEST['indd'] == "") {
			$indd = 0;
		}else {
			$indd = $_REQUEST['indd'];
		}
		if ($_REQUEST['joby'] == "") {
			$joby = 0;
		}else {
			$joby = $_REQUEST['joby'];
		}
		if ($_REQUEST['jobm'] == "") {
			$jobm = 0;
		}else {
			$jobm = $_REQUEST['jobm'];
		}
		if ($_REQUEST['jobd'] == "") {
			$jobd = 0;
		}else {
			$jobd = $_REQUEST['jobd'];
		}

		$q = "update incpeople set ";
		$q .= 'name = "'.$name.'",';
		$q .= 'shift = "'.$shift.'",';
		$q .= 'involvment = "'.$involve.'",';
		$q .= 'starttime = "'.$stime.'",';
		$q .= 'operation = "'.$operation.'",';
		$q .= 'qualifications = "'.$quals.'",';
		$q .= 'indexpy = '.$indy.',';
		$q .= 'indexpm = '.$indm.',';
		$q .= 'indexpd = '.$indd.',';
		$q .= 'jobexpy = '.$joby.',';
		$q .= 'jobexpm = '.$jobm.',';
		$q .= 'jobexpd = '.$jobd;
		$q .= ' where uid = '.$id;

		$r = mysql_query($q) or die(mysql_error().$q);

	  ?>
	  <script>
	  window.open("","editincident").jQuery("#ipeoplelist").trigger("reloadGrid");
	  //alert('If you do not see the edited changes, click the Reload Grid icon on the bottom bar of the grid');
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
