<?php
session_start();
//ini_set('display_errors', true);

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Workflow Stage</title>
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
      <td colspan="2"><div align="center" class="style1"><u>Add Workflow Stage </u></div></td>
    </tr>
    <tr>
      <td width="120" class="boxlabel">Workflow Stage</td>
      <td width="270"><input name="workflow" type="text" id="workflow"  size="45" maxlength="45"></td>
      </tr>
    <tr>
      <td class="boxlabel">Order</td>
      <td><input type="text" name="wforder" id="wforder"></td>
    </tr>
      <tr>
        <td class="boxlabel">Aide Memoire</td>
        <td align="left"><textarea name="taide" id="taide" cols="45" rows="5"></textarea></td>
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
				
				$oIn->workflow = $_REQUEST['workflow'];
				if ($_REQUEST['wforder'] == '') {
					$oIn->wforder = 0;
				} else {
					$oIn->wforder = $_REQUEST['wforder'];
				}
				$oIn->aide = $_REQUEST['taide'];
		
				$oIn->AddWorkflow();
	
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
