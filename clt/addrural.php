<?php
session_start();
$dbase = $_SESSION['s_admindb'];
$selected_db = $_REQUEST['sdb'];
require("../db.php");
mysql_select_db($dbase) or die(mysql_error());

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add NZ Rural Post Code</title>
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
      <td colspan="2"><div align="center" class="style1"><u>Add NZ Rural Post Code </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">RD</td>
      <td><input type="text" name="rd" id="rd" value="RD"></td>
    </tr>
    <tr>
      <td class="boxlabel">Town/City</td>
      <td><input type="text" name="city" id="city"></td>
    </tr>
    <tr>
      <td class="boxlabel">Post Code</td>
      <td><input name="postcode" type="text" id="postcode" size="4" maxlength="4" ></td>
    </tr>
	<tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" ></td>
      </tr>
  </table>
</form>
</div>
	<script>document.onkeypress = stopRKey;</script> 

<?php

	if(isset($_POST['save'])) {
		$ok = 'Y';

		if ($_REQUEST['rd'] == '') {
			echo '<script>';
			echo 'alert("Please enter RD + Number.")';
			echo '</script>';	
			$ok = 'N';
		}
		if ($_REQUEST['city'] == '') {
			echo '<script>';
			echo 'alert("Please enter a town/city.")';
			echo '</script>';	
			$ok = 'N';
		}
		if ($_REQUEST['postcode'] == '') {
			echo '<script>';
			echo 'alert("Please enter a post code.")';
			echo '</script>';	
			$ok = 'N';
		}
		if ($ok == 'Y') {	
	
				include_once("../includes/mantleadmin.php");
				$oBx = new mantleadmin;	
				
				$oBx->rd = $_REQUEST['rd'];
				$oBx->city = $_REQUEST['city'];
				$oBx->postcode = $_REQUEST['postcode'];
		
				$oBx->AddRural();
	
				?>
				<script>
				window.open("","updtrural").jQuery("#rurallist").trigger("reloadGrid");
				this.close();
				</script>
				<?php
		
			
		}
	}

?>


</body>
</html>
