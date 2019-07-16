<?php
session_start();
$dbs = $_SESSION['s_admindb'];
$usersession = $_SESSION['usersession'];
$ubranch = $_SESSION['s_ubranch'];
$coyid = $_SESSION['s_coyid'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];
$muser = $row['usergroup'];
$sid = $subid;
$madm = $row['admin'];

// populate admin list
    $arr = array('No', 'Yes');
	$admin_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		$selected = '';
		$admin_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

$findb = $_SESSION['s_findb'];


$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Users</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function checkup() {
	var um = document.getElementById('un').value;
	$.get("../includes/ajaxCheckup.php", {um: um}, function(data){
	  if (data == 'Y') {
		  alert('This username is already in use. Please choose another.');
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

<form name="form1" method="post" >

  <table width="590" border="0" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Add User </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">First Name </td>
      <td><div align="left">
        <input name="firstname" type="text" id="firstname"  size="50" maxlength="45">
      </div></td>
    </tr>
    <tr>
      <td class="boxlabel">Last Name </td>
      <td><div align="left">
        <input name="lastname" type="text" id="lastname"   size="50" maxlength="45">
      </div></td>
    </tr>
    <tr>
      <td class="boxlabel">Email</td>
      <td align="left"><input name="email" type="text" id="email" size="50" maxlength="70"></td>
    </tr>
     <tr>
      <td class="boxlabel">User Name</td>
      <td align="left"><input name="un" type="text" id="un"  value="" size="50" ></td>
    </tr>
      <tr>
      <td class="boxlabel">Password</td>
      <td align="left"><input name="password" type="password" id="password" value="" size="50" onBlur="checkup()"></td>
    </tr>
    <tr>
      <td class="boxlabel">Phone</td>
      <td><input name="phone1" type="text" id="phone1"  size="50" maxlength="45"></td>
    </tr>
    <tr>
      <td class="boxlabel">Mobile</td>
      <td><input name="phone2" type="text" id="phone2"  size="50" maxlength="45"></td>
    </tr>
    <?php
	if ($madm == 'Y' && $muser == 20) {
		echo '<tr>';
		  echo '<td class="boxlabel">Administrator</td>';
		  echo '<td><select name="ladministrator" id="ladministrator">';
		  echo $admin_options;
		  echo '</select></td>';
		echo '</tr>';
	}
	?>
    <tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" id="save" ></td>
    </tr>
  </table>
</form>

	<script>
		document.onkeypress = stopRKey;
    </script> 

<?php
	if(isset($_POST['save'])) {
	
		$ladministrator = 'N';
		$sub_id = $sid;
		$coy_id = $coyid;
		$fname = trim($_REQUEST['firstname']);
		$lname = trim($_REQUEST['lastname']);
		$email = $_REQUEST['email'];
		if (isset($_REQUEST['ladministrator'])) {
			$admin = $_REQUEST['ladministrator'];
		} else {	
			$admin = 'N';
		}
		$phone1 = $_REQUEST['phone1'];
		$phone2 = $_REQUEST['phone2'];
		$username = $_REQUEST['un'];
		$password = $_REQUEST['password'];
									
		include_once("../includes/DBClass.php");
		$db = new DBClass();

		$db->query("insert into users (ufname,ulname,uemail,uphone,umobile,uadmin,username,upwd,usalt,sub_id) values (:ufname,:ulname,:uemail,:uphone,:umobile,:uadmin,:username,:upwd,:usalt,:sub_id)");
		$db->bind(':ufname', $fname);
		$db->bind(':ulname', $lname);
		$db->bind(':uemail', $email);
		$db->bind(':uphone', $phone1);
		$db->bind(':umobile', $phone2);
		$db->bind(':uadmin', $admin);
		$db->bind(':username', md5($username));
		$salt = base64_encode(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
		$hash = hash('sha256',$salt.$password);
		$db->bind(':upwd', $hash);
		$db->bind(':usalt', $salt);
		$db->bind(':sub_id', $sid);	
		
		$db->execute();
		$lastnum = $db->lastInsertId();
		
		$db->query("insert into access (staff_id,subid,coyid,module,usergroup) values (:staff_id,:subid,:coyid,:module,:usergroup)");
		$db->bind(':staff_id', $lastnum);
		$db->bind(':subid', $sid);
		$db->bind(':coyid', $coyid);
		$db->bind(':module', 'fin');
		$db->bind(':usergroup', 1);
		
		$db->execute();
		
		$db->query("insert into access (staff_id,subid,coyid,module,usergroup) values (:staff_id,:subid,:coyid,:module,:usergroup)");
		$db->bind(':staff_id', $lastnum);
		$db->bind(':subid', $sid);
		$db->bind(':coyid', $coyid);
		$db->bind(':module', 'clt');
		$db->bind(':usergroup', 1);
		
		$db->execute();

		$db->closeDB();

?>
	<script>
	  window.open("","hs_users").jQuery("#userslist").trigger("reloadGrid");
	  this.close()
	</script>
<?php
	
	}

?>
 
 

</body>
</html>

