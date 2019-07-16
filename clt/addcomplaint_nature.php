<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$db->closeDB();
?>

<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Complaint Nature</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

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
      <td colspan="2"><div align="center" class="style1"><u>Add Complaint Nature </u></div></td>
    </tr>
    <tr>
      <td width="106" class="boxlabel"><div align="right">Nature</div></td>
      <td><input name="nature" type="text" id="nature"  size="45" maxlength="50"></td>
      </tr>
	<tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" ></td>
      </tr>
  </table>
</form>

<script>
	document.getElementById('nature').focus();
	document.onkeypress = stopRKey;
</script> 


</div>

<?php

	if(isset($_POST['save'])) {

		if ($_REQUEST['nature'] == '') {
			echo '<script>';
			echo 'alert("Please enter a nature.")';
			echo '</script>';	
		} else {			

				include_once("../includes/cltadmin.php");
				$oIn = new cltadmin;	
				
				$oIn->nature = $_REQUEST['nature'];
				$oIn->sub_id = $subscriber;
		
				$oIn->AddComplaintNature();
	
				?>
				<script>
				window.open("","updtcomplaint_nature").jQuery("#naturelist").trigger("reloadGrid");
				this.close();
				</script>
				<?php
		
			
		}
	}

?>


</body>
</html>
