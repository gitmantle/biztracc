<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subscriber = $subid;
?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Client Status</title>
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
  <table width="490" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Add Client Status </u></div></td>
    </tr>
    <tr>
      <td width="120" class="boxlabel">Client Status</td>
      <td width="270"><input name="ctype" type="text" id="ctype"  size="45" maxlength="45"></td>
      </tr>
	<tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" ></td>
      </tr>
  </table>
</form>

<script>
	document.getElementById('ctype').focus();
	document.onkeypress = stopRKey;
</script> 

</div>

<?php

	if(isset($_POST['save'])) {

		if ($_REQUEST['ctype'] == '') {
			echo '<script>';
			echo 'alert("Please enter a client status.")';
			echo '</script>';	
		} else {			

				include_once("../includes/cltadmin.php");
				$oIn = new cltadmin;	
				
				$oIn->clientstatus = $_REQUEST['ctype'];
				$oIn->sub_id = $subscriber;
		
				$oIn->AddClientStatus();
	
				?>
				<script>
				window.open("","updtclientstatus").jQuery("#clientstatuslist").trigger("reloadGrid");
				this.close();
				</script>
				<?php
		
			
		}
	}

?>


</body>
</html>
