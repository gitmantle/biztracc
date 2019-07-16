<?php
$usersession = $_COOKIE['usersession'];
$dbs = "ken47109_kenny";
date_default_timezone_set("Pacific/Auckland");

require("../db.php");
mysql_select_db($dbs) or die(mysql_error());
$query = "select * from sessions where session_id = ".$usersession;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$cmuid = $_REQUEST['uid'];
$dbs = "ken47109_kenny";


require("../db.php");
mysql_select_db($dbs) or die(mysql_error());

// get the clients name
$query = "select lastname,firstname from members where member_id = ".$cmuid;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$lname = trim($firstname).' '.trim($lastname);
$head = 'Remove Member '.$lname;


$hdate = date('Y-m-d');
$ttime = strftime("%H:%M", mktime());

$query = "insert into audit (ddate,ttime,user_id,uname,sub_id,member_id,action) values ";
$query .= "('".$hdate."',";
$query .= "'".$ttime."',";
$query .= $user_id.",";
$query .= '"'.$uname.'",';


$query .= $sub_id.",";
$query .= $cmuid.",";
$query .= "'Delete Duplicate')";

$result = mysql_query($query) or die(mysql_error().$query);



?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Delete Member</title>
<link rel="stylesheet" href="../includes/kenny.css" media="screen" type="text/css">



<style type="text/css">
<!--
.style1 {
	font-size: large;
}
-->
</style>

</head>

<body> 

<div id="mwin">

<form name="form1" method="post" action="" >
<br>
  <table width="400" border="0" align="center">
    <tr>
     	 <td><div align="center" class="style1"><u><?php echo $head; ?> </u></div></td>
    </tr>
    	<?php
				echo "<tr>";
				echo '<td><div align="center">';
			    echo '<input type="submit" value="Remove" name="delete" id="delete">';
			    echo "</div></td>";
				echo "</tr>";
		
		?>
  </table>
</form>



</div>


<?php

	if(isset($_POST['delete'])) {

		include_once("includes/cltadmin.php");
		$oCm = new cltadmin;	
		
		$oCm->uid = $cmuid;
		
		$oCm->DelMember();

		echo '<script>';
		echo 'window.open("","duplicatelist").jQuery("#memlistd").trigger("reloadGrid");';
		echo 'this.close();';
		echo '</script>';


	}

?>




</body>
</html>
