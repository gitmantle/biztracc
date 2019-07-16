<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
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

$db->query("select * from ".$cltdb.".workflow where process_id = ".$induid);
$row = $db->single();
extract($row);

$db->closeDB();

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Workflow</title>
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
      <td colspan="2"><div align="center" class="style1"><u>Edit Workflow Stage </u></div></td>
    </tr>
    <tr>
      <td width="120" class="boxlabel">Workflow Stage</td>
      <td width="270"><input name="workflow" type="text" id="workflow"  size="45" maxlength="45" value="<?php echo $process; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabel">Order</td>
      <td><input type="text" name="wforder" id="wforder" value="<?php echo $porder; ?>"></td>
    </tr>
      <tr>
        <td class="boxlabel">Aide Memoire</td>
        <td align="left"><textarea name="taide" id="taide" cols="45" rows="5"><?php echo $aide_memoir; ?></textarea></td>
      </tr>
	<tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" ></td>
      </tr>
  </table>
</form>
</div>
<script>
	document.getElementById('workflow').focus();
	document.onkeypress = stopRKey;
</script> 

<?php

	if(isset($_POST['save'])) {

		if ($_REQUEST['workflow'] == '') {
			echo '<script>';
			echo 'alert("Please enter a workflow stage.")';
			echo '</script>';	
		} else {			

				include_once("../includes/cltadmin.php");
				$oIn = new cltadmin;	
				
				$oIn->uid = $induid;
				$oIn->workflow = $_REQUEST['workflow'];
				if ($_REQUEST['wforder'] == '') {
					$oIn->wforder = 0;
				} else {
					$oIn->wforder = $_REQUEST['wforder'];
				}
				$oIn->aide = $_REQUEST['taide'];
		
				$oIn->EditWorkflow();
	
				?>
				<script>
				window.open("","updtworkflow").jQuery("#flowlist").trigger("reloadGrid");
				this.close();
				</script>
				<?php
		
			
		}
	}

?>



</body>
</html>
