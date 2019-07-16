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

$q = "select * from params";
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
<title>Edit RUC Parameters</title>
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
 <table width="600" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Edit RUC Parameters </strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Off-road Customer Number</label></td>
      <td ><input type="text" name="custno" id="custno" value="<?php echo $offroad_custno; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabel">Method used</td>
      <td ><input type="text" name="method" id="method" value="<?php echo $method; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel"><span style="color: <?php echo $tdfont; ?>">GPS type</span></td>
      <td><input type="text" name="type" id="type" value="<?php echo $type; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabel">Brief description of off-road travel</td>
      <td><textarea name="desc" id="desc" cols="45" rows="5" ><?php echo $description; ?></textarea></td>
    </tr>
    <tr>
      <td class="boxlabel">What records are you able to supply to validate claims?</td>
      <td><textarea name="rec" id="rec" cols="45" rows="5" ><?php echo $records; ?></textarea></td>
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
		$custno = $_REQUEST['custno'];
		$method = $_REQUEST['method'];
		$private = $_REQUEST['private'];
		$desc = $_REQUEST['desc'];
		$rec = $_REQUEST['rec'];
		
		$q = "select * from params";
		$r = mysql_query($q) or die(mysql_error().$q);
		if (mysql_num_rows($r) == 0) {
			$qi = "insert into params (reason_code) values (11)";
			$ri = mysql_query($qi) or die(mysql_error().$qi);
		}

		$q = "update params set ";
		$q .= "offroad_custno = '".$custno."',";
		$q .= "method = '".$method."',";
		$q .= "type = '".$type."',";
		$q .= "reason_code = 11,";
		$q .= "description = '".$desc."',";
		$q .= "records = '".$rec."'";

		$r = mysql_query($q) or die(mysql_error().$q);

	  ?>
	  <script>
	  window.open("","rucdetails");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
