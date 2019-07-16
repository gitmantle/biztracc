<?php

session_start();


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Stock Group</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="mwin">
<form name="form1" method="post" >
  <table width="600" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Add Stock Group </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Group Name</td>
      <td><input name="grname" type="text" id="grname"  size="30" maxlength="30"></td>
      </tr>
	<tr>
	  <td>&nbsp;</td>
	  <td align="right"><input type="submit" value="Save" name="save" ></td>
	  </tr>
  </table>
</form>
</div>

<?php
	if(isset($_POST['save'])) {
		
		if ($_REQUEST['grname'] == '') {
			echo '<script>';
			echo 'alert("Please enter a group name.")';
			echo '</script>';	
		} else {	
		
			$grname = strtoupper($_REQUEST['grname']);
			include_once("includes/accaddacc.php");
			$oAcc = new accaddacc;
				
			$oAcc->groupname = strtoupper($_REQUEST['grname']);
			//$oAcc->stock = $_REQUEST['grstock'];
									
			$oAcc->AddGroup();
	
			?>
				<script>
				window.open("","updtstkgroups").jQuery("#stkgrouplist").trigger("reloadGrid");
				this.close()
				</script>
			<?php
		}
	
	}
?>
 

</body>
</html>

