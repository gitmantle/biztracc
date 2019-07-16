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

$query = "select staff.staff_id from staff where staff.staff_id = ".$user_id; 
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

window.name = "editlogin";

function changepassword() {
		document.getElementById('password').style.visibility = 'visible';
		document.getElementById('chgpassword').value = 'Y';
}

function changeuser() {
		document.getElementById('un').style.visibility = 'visible';
		document.getElementById('chguser').value = 'Y';
}
	
</script>
</head>
<body>
<form name="form1" id="form1" method="post" >
  <input type="hidden" name="chgpassword" id="chgpassword" value="N">
  <input type="hidden" name="chguser" id="chguser" value="N">
  <table align="center">
    <tr>
      <td colspan="2" align="center"><?php echo $uname; ?></td>
    </tr>
    <tr>
      <td><div align="right">
          <input type="button" name="chgpwd" value="Change User Name" onClick="changeuser()">
        </div></td>
      <td><div align="left">
          <input name="un" type="text" id="un"  value="" size="50" >
        </div></td>
    </tr>
    <tr>
      <td><div align="right">
          <input type="button" name="chgpwd" value="Change Password" onClick="changepassword()">
        </div></td>
      <td><div align="left">
          <input name="password" type="password" id="password" value="" size="50">
        </div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" id="save"></td>
    </tr>
  </table>
  
 <script>
		document.getElementById('password').style.visibility = 'hidden';
		document.getElementById('un').style.visibility = 'hidden';
 </script>
  
  
</form>

<?php
	if(isset($_POST['save'])) {
		if ($_REQUEST['chguser'] == "Y") {
			$un = $_REQUEST['un'];
			$q = "update staff set username = '".md5($un)."' where staff_id = ".$user_id;
			$r = mysql_query($q) or die(mysql_error().$q);
		}
		if ($_REQUEST['chgpassword'] == "Y") {
			$pwd = $_REQUEST['password'];
			$q = "update staff set password = '".md5($pwd)."' where staff_id = ".$user_id;
			$r = mysql_query($q) or die(mysql_error().$q);
		}
		echo '<script>';
		echo 'alert("Details changed. Exit Kenny and re-enter for the new details to take effect");';
		echo 'this.close();';
		echo '</script>';
	}
?>

</body>
</html>