<?php
session_start();
//ini_set('display_errors', true);
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$moduledb = 'sub'.$subid;
mysql_select_db($moduledb) or die(mysql_error());
?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Link</title>
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
<div id="mwin">
<form name="form1" method="post" >
  <table width="570" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Add Link </u></div></td>
    </tr>
    <tr>
      <td width="106" class="boxlabel"><div align="right">Link</div></td>
      <td><input name="link" type="text" id="link"  size="80" maxlength="100"></td>
      </tr>
    <tr>
      <td width="106" class="boxlabel"><div align="right">Description</div></td>
      <td><input name="desc" type="text" id="desc"  size="80" maxlength="200"></td>
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

		if ($_REQUEST['link'] == '') {
			echo '<script>';
			echo 'alert("Please enter a link.")';
			echo '</script>';	
		} else {			

			$lnk = $_REQUEST['link'];
			$description = $_REQUEST['desc'];

			$sSQLString = "insert into links (link,description) values ";
			$sSQLString .= "('".$lnk."','";	
			$sSQLString .= $description."')";	
			$result = mysql_query($sSQLString) or die(mysql_error());

	
				?>
				<script>
				window.open("","updtlinks").jQuery("#updtlinklist").trigger("reloadGrid");
				this.close();
				</script>
				<?php
		
			
		}
	}

?>


</body>
</html>
