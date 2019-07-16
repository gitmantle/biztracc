<?php
session_start();


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

date_default_timezone_set($_SESSION['s_timezone']);


$rtid = $_REQUEST['uid'];
$q = "select * from routes where uid = ".$rtid;
$r = mysql_query($q);
$row = mysql_fetch_array($r);
extract($row);

$_SESSION['s_route'] = $rtid;

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Route</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script>

window.name = "editroute";

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function addhazard() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addhazard.php','adhz','toolbar=0,scrollbars=1,height=550,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function edithazard(id) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +50;
	window.open('edithazard.php?hid='+id,'edhz','toolbar=0,scrollbars=1,height=700,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}



function post() {

	//add validation here if required.
	var route = document.getElementById('route').value;
	
	var ok = "Y";
	if (route == "") {
		alert("Please enter a route.");
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
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Edit Route </strong></label></td>
    </tr>
    <tr>
      <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Route</label>
        <input name="route" type="text" id="route" size="100" value="<?php echo $route; ?>" ></td>
      </tr>
    <tr>
      <td class="boxlabel">Private Road Kms</td>
      <td><input name="private" type="text" id="private" value="<?php echo $private; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Compartment</td>
      <td><input type="text" name="compt" id="compt" value="<?php echo $compartment; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Forest</td>
      <td><input type="text" name="forest" id="forest" value="<?php echo $forest; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Rate per Tonne for the route</td>
      <td><input type="text" name="rate" id="rate" value="<?php echo $rate; ?>"></td>
    </tr>
	<tr>
      <td align="right" colspan="2">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
    <tr>
      <td colspan="2"><?php include "getHazards.php"; ?></td>
    </tr>
  	</table>
</form>

<?php

	if($_REQUEST['savebutton'] == "Y") {
		$route = $_REQUEST['route'];
		$private = $_REQUEST['private'];
		$compt = $_REQUEST['compt'];
		$forest = $_REQUEST['forest'];
		$rate = $_REQUEST['rate'];

		$q = "update routes set ";
		$q .= "route = '".$route."',";
		$q .= "private = ".$private.',';
		$q .= "compartment = ".$compt.',';
		$q .= "forest = '".$forest."',";
		$q .= "rate = ".$rate;
		$q .= " where uid = ".$rtid;

		$r = mysql_query($q) or die(mysql_error().$q);

	  ?>
	  <script>
	  window.open("","routes").jQuery("#routelist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
