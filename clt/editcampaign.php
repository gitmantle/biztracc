<?php
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

$campid = $_REQUEST['uid'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$cltdb = $_SESSION['s_cltdb'];

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$db->query("select * from ".$cltdb.".campaigns where campaign_id = ".$campid);
$row = $db->single();
extract($row);
$sname = trim($staff);

// populate staff drop down
$db->query("select concat(ufname,' ',ulname) as staffname from users where sub_id = ".$subscriber." order by ulname");
$rows = $db->resultset();
$staff_options = "<option value=\"0\">Select Staff</option>";
foreach ($rows as $row) {
	extract($row);
	if (trim($staffname) == $sname) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$staff_options .= '<option value="'.$staffname.'"'.$selected.'>'.$staffname.'</option>';
}

$dt = explode('-',$startdate);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$fdt = mktime(0,0,0,$m,$d,$y);
$stdate = date("d/m/Y",$fdt);

date_default_timezone_set($_SESSION['s_timezone']);
$hdate = date("Y-m-d");
$ddate = date("d/m/Y");

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

$db->closeDB();

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Campaign</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function post() {

	//add validation here if required.
	var nm = document.getElementById('name').value;
	var startdt = document.getElementById('start').value;
	var ok = "Y";
	if (nm == "") {
		alert("Please enter a campaign name.");
		ok = "N";
		return false;
	}
	if (startdt == " ") {
		alert("Please select a start date.");
		ok = "N"
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
<div id="bwin">
<form name="form1" id="form1" method="post" action="">
 <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="900" border="0">
    <tr>
      <td colspan="4" align="center"><u>Edit Campaign </u></td>
      </tr>
    <tr>
      <td class="boxlabel">Name</td>
      <td><input type="text" name="name" id="name" value="<?php echo $name; ?>"></td>
      <td class="boxlabelleft">Description</td>
      <td class="boxlabelleft">Goals</td>
    </tr>
    <tr>
      <td class="boxlabel">Start Date</td>
    <td><input type="Text" id="start" name="start" maxlength="25" size="25" value="<?php echo $hdate; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
      <td rowspan="4"><textarea name="descript" id="descript" cols="40" rows="5"><?php echo $description; ?></textarea></td>
      <td rowspan="4"><textarea name="goal" id="goal" cols="40" rows="5"><?php echo $goals; ?></textarea></td>
    </tr>
    <tr>
      <td class="boxlabel">Represented by</td>
      <td><select name="staff" id="staff">
        <?php echo $staff_options;?>
      </select></td>
      </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      </tr>
    <tr>
      <td><input type="button" value="Save and Exit" name="save" onclick="post()" ></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td align="right">&nbsp;</td>
    </tr>
  </table>
</form>
</div>
	<script>document.onkeypress = stopRKey;</script> 
 <script>
 	document.getElementById("start").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>    
    
<?php

	if($_REQUEST['savebutton'] == "Y") {
	
		include_once("../includes/cltadmin.php");
		$oAct = new cltadmin;	
		
		$oAct->uid = $campid;
		$oAct->campname = $_REQUEST['name'];
		  $odt = $_REQUEST['start'];
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
		  $oAct->campstart = $ddate;
		
		$oAct->campadvisor = $_REQUEST['staff'];
		$oAct->campdescript = $_REQUEST['descript'];
		$oAct->campgoal = $_REQUEST['goal'];

		$oAct->EditCampaign();

	  ?>
	  <script>
	  window.open("","updtcampaigns").jQuery("#campaignlist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
