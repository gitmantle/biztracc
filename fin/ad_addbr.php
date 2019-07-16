<?php

session_start();

//ini_set('display_errors', true);

$coyid = $_SESSION['coyid'];
require_once("../db.php");
mysql_select_db($coyid) or die(mysql_error());

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Branch</title>
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
      <td colspan="2"><div align="center" class="style1"><u>Add Branch </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel"><div align="right">Branch Name</div></td>
      <td><div align="left"><input name="branchname" type="text" id="branchname"  size="25" maxlength="25"></div></td>
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
		
		if ($_REQUEST['branchname'] == '') {
			echo '<script>';
			echo 'alert("Please enter a branch name.")';
			echo '</script>';	
		} else {	
		
				include_once("includes/accaddacc.php");
				$oAcc = new accaddacc;
				
				$oAcc->coy = $coyid;
				$oAcc->branchname = strtoupper($_REQUEST['branchname']);
									
				$oAcc->AddBranch();
	
			?>
				<script>
				window.open("","updtbr").jQuery("#brlist").trigger("reloadGrid");
				this.close()
				</script>
			<?php
		}
	
	}
?>
 

</body>
</html>

