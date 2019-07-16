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
$q = "select * from driverlog where uid = ".$rtid;
$r = mysql_query($q);
$row = mysql_fetch_array($r);
extract($row);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Drive Log</title>
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
	
	var ok = "Y";
	
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
 <table width="590" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Edit Driver Log </strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Driver</label></td>
      <td><input name="driver" type="text" id="driver" value="<?php echo $driver; ?>" readonly ></td>
      </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Date</label></td>
      <td><input name="date" type="text" id="date" value="<?php echo $date; ?>"  readonly></td>
      </tr>
    <tr>
      <td class="boxlabel">Vehicle</td>
      <td><input name="truckno" type="text" id="truckno" value="<?php echo $truckno; ?>" readonly></td>
    </tr>
    <tr>
      <td class="boxlabel">Logged Activity</td>
      <td><input type="text" name="log" id="log" value="<?php echo $log; ?>" readonly></td>
    </tr>
    <tr>
      <td class="boxlabel">Time</td>
      <td><input type="text" name="time" id="time" value="<?php echo $time; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Hubodometer</td>
      <td><input type="text" name="hubodometer" id="hubodometer" value="<?php echo $hubodometer; ?>"></td>
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
		$time = $_REQUEST['time'];
		$hubo = $_REQUEST['hubodometer'];

		$q = "update driverlog set ";
		$q .= "time = '".$time."',";
		$q .= "hubodometer = ".$hubo;
		$q .= " where uid = ".$rtid;

		$r = mysql_query($q) or die(mysql_error().$q);

	  ?>
	  <script>
	  window.open("","dloggrid").jQuery("#dloglist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
