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

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Contractor</title>
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
	var contractor = document.getElementById('contractor').value;
	var crew = document.getElementById('crew').value;
	
	var ok = "Y";
	if (contractor == "") {
		alert("Please enter a contractor.");
		ok = "N";
		return false;
	}
	if (crew == "") {
		alert("Please enter a crew.");
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
 <table width="500" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Add Contractor </strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Contractor</label></td>
      <td><input name="contractor" type="text" id="contractor" size="50" ></td>
      </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Crew</label></td>
      <td><input name="crew" type="text" id="crew" size="50" ></td>
      </tr>
	<tr>
      <td colspan="2" align="right">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  	</table>
</form>

<?php

	if($_REQUEST['savebutton'] == "Y") {
		$contractor = $_REQUEST['contractor'];
		$crew = $_REQUEST['crew'];

		$q = "insert into contractors (contractor,crew) values (";
		$q .= '"'.$contractor.'",';
		$q .= $crew.')';

		$r = mysql_query($q) or die(mysql_error().$q);

	  ?>
	  <script>
	  window.open("","contractors").jQuery("#contractorlist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
