<?php
session_start();

$refid = $_REQUEST['refid'];
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);
$induid = $_REQUEST['uid'];

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subscriber = $subid;

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "select * from referred where referred_id = ".$refid;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);



?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Source</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

</script>

<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="swin">
<form name="form1" method="post" >
  <table width="400" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Edit Source </u></div></td>
    </tr>
    <tr>
      <td width="106" class="boxlabel"><div align="right">Source</div></td>
      <td><input name="source" type="text" id="source"  size="25" maxlength="25" value="<?php echo $referred; ?>"></td>
      </tr>
	<tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" ></td>
      </tr>
  </table>
</form>
</div>
<script>
	document.getElementById('source').focus();
	document.onkeypress = stopRKey;
</script> 

<?php

	if(isset($_POST['save'])) {

		if ($_REQUEST['source'] == '') {
			echo '<script>';
			echo 'alert("Please enter a source.")';
			echo '</script>';	
		} else {			

				include_once("../includes/cltadmin.php");
				$oIn = new cltadmin;	
				
				$oIn->uid = $refid;
				$oIn->source = $_REQUEST['source'];
		
				$oIn->EditSource();
	
				?>
				<script>
				window.open("","updtreferredby").jQuery("#rblist").trigger("reloadGrid");
				this.close();
				</script>
				<?php
		
			
		}
	}

?>



</body>
</html>
