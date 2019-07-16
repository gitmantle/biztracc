<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);
$induid = $_REQUEST['uid'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$cltdb = $_SESSION['s_cltdb'];

$db->query("select * from ".$cltdb.".client_types where client_type_id = ".$induid);
$row = $db->single();
extract($row);

$db->closeDB();

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Client Type</title>
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
  <table width="500" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Edit Client Type </u></div></td>
    </tr>
    <tr>
      <td width="120" class="boxlabel">Client Type</td>
      <td width="270"><input name="ctype" type="text" id="ctype"  size="45" maxlength="45" value="<?php echo $client_type; ?>"></td>
      </tr>
	<tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" ></td>
      </tr>
  </table>
</form>
</div>

<script>
	document.getElementById('ctype').focus();
	document.onkeypress = stopRKey;
</script> 

<?php

	if(isset($_POST['save'])) {

		if ($_REQUEST['ctype'] == '') {
			echo '<script>';
			echo 'alert("Please enter a client type.")';
			echo '</script>';	
		} else {			

				include_once("../includes/cltadmin.php");
				$oIn = new cltadmin;	
				
				$oIn->uid = $induid;
				$oIn->clienttype = $_REQUEST['ctype'];
		
				$oIn->EditClientType();
	
				?>
				<script>
				window.open("","updtclienttype").jQuery("#clienttypelist").trigger("reloadGrid");
				this.close();
				</script>
				<?php
		
			
		}
	}

?>



</body>
</html>
