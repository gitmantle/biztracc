<?php
session_start();
$cmuid = $_REQUEST['uid'];
$dbase = $_SESSION['s_admindb'];

require("../db.php");
mysql_select_db($dbase) or die(mysql_error());

// get the member name
$query = "select post_office from boxes where box_id = ".$cmuid;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$lname = trim($firstname).' '.trim($lastname);
$head = 'Remove Post Office '.$post_office;



?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Delete NZ Box Post Code</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">



<style type="text/css">
<!--
.style1 {
	font-size: large;
}
-->
</style>

</head>

<body> 

<div id="swin">

<form name="form1" method="post" action="" >
<br>
  <table width="500" border="0" align="center">
    <tr>
     	 <td><div align="center" class="style1"><u><?php echo $head; ?> </u></div></td>
    </tr>

	<td><div align="right">
		 <input type="submit" value="Remove" name="delete" id="delete">
		 </div></td>

  </table>
</form>



</div>


<?php

	if(isset($_POST['delete'])) {

		include_once("../includes/mantleadmin.php");
		$oBx = new mantleadmin;	
		
		$oBx->uid = $cmuid;
		
		$oBx->DelBox();
	
?>

		<script>
			window.open("","updtboxes").jQuery("#boxlist").trigger("reloadGrid");
			this.close();
        </script>

<?php

	}

?>




</body>
</html>
