<?php
session_start();

$uid = 36;

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select users.ufname,users.ulname,users.uemail from users where users.uid = ".$uid); 
$row = $db->single();
extract($row);


	
$db->closeDB();

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Users</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

<script>
function changepassword() {
		document.getElementById('password').style.visibility = 'visible';
		document.getElementById('chgpassword').value = 'Y';
}

function changeuser() {
		document.getElementById('un').style.visibility = 'visible';
		document.getElementById('chguser').value = 'Y';
}

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function checkup() {
	var um = document.getElementById('un').value;
	var pd = document.getElementById('password').value;
	$.get("includes/ajaxCheckup.php", {um: um, pd: pd}, function(data){
	  if (data == 'Y') {
		  alert('This username/password combination is already in use. Please choose another.');
		  return false;
	  } else {
		  return true;
	  }
  });
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
	<input type="hidden" name="chgpassword" id="chgpassword" value="N">
	<input type="hidden" name="chguser" id="chguser" value="N">

  <table width="590" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Edit Users </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">First Name </td>
      <td><div align="left">
        <input name="firstname" type="text" id="firstname"  value="<?php echo $ufname; ?>" size="50" maxlength="45">
      </div></td>
    </tr>
    <tr>
      <td class="boxlabel">Last Name </td>
      <td><div align="left">
        <input name="lastname" type="text" id="lastname"  value="<?php echo $ulname; ?>"  size="50" maxlength="45">
      </div></td>
    </tr>
    <tr>
      <td class="boxlabel">Email</td>
      <td align="left"><input name="email" type="text" id="email" value="<?php echo $uemail; ?>" size="50" maxlength="70"></td>
    </tr>
    <tr>
      <td><div align="right">
        <input type="button" name="chgpwd" value="Change Password" onClick="changepassword()">
      </div></td>
      <td><div align="left">
        <input name="password" type="password" id="password" value="" size="50" onBlur="checkup()">
      </div></td>
    </tr>

    <tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" id="save" ></td>
    </tr>
  </table>
</form>

	<script>document.onkeypress = stopRKey;</script> 
</div>
<?php
	if(isset($_POST['save'])) {
	
		include_once("../includes/DBClass.php");
		$db = new DBClass();
	
		if ($_REQUEST['chguser'] == "Y") {
			$db->query("update users set uemail = :uemail where uid = :uid");
			$db->bind(':uemail', $_REQUEST['email']);
			$db->bind(':uid', $uid);
			$db->execute();
		}
		
		if ($_REQUEST['chgpassword'] == "Y") {
			$db->query("update users set upwd = :uname, usalt = :usalt where uid = :uid");
			$salt = base64_encode(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
			$pwd = $_REQUEST['password'];
			$hash = hash('sha256',$salt.$pwd);
			$db->bind(':uname', $hash);
			$db->bind(':usalt', $salt);
			$db->bind(':uid', $uid);
			$db->execute();
		}

		$db->closeDB();

?>
	<script>
	  this.close()
	</script>
<?php
	
	}

?>
 
 
 <script>
		document.getElementById('password').style.visibility = 'hidden';
		document.getElementById('un').style.visibility = 'hidden';

 </script>

</body>
</html>

