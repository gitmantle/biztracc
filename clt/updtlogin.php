<?php
session_start();
$usersession = $_COOKIE['usersession'];
ini_set('display_errors', true);

$dbase = $_SESSION['s_admindb'];

require("../db.php");
mysql_select_db($dbase) or die(mysql_error());
$query = "select * from sessions where session_id = ".$usersession;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);




?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script type="text/javascript">

	window.name = "updtlogin";
	
</script>
</head>
<body>
<form name="form1" id="form1" method="post" >
  <table align="center">
    <tr>
      <td align="center">Edit login details of <?php echo $uname; ?></td>
    </tr>
    <tr>
      <td align="right"><input type="button" value="Yes" name="save" id="save" onclick="editlogin()"></td>
    </tr>
  </table>
  
</form>

</body>
</html>