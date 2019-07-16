<?php
session_start();
$dbs = $_SESSION['s_admindb'];
$uid = $_SESSION['s_userid'];
$ubranch = $_SESSION['s_ubranch'];
$usersession = $_SESSION['usersession'];
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

$db->query("select users.ufname,users.ulname,users.coyowner,users.uadmin,users.uemail,users.uphone,users.umobile,users.ug_user,users.ug_pwd,users.uadmin,users.logsuser,users.logspwd,access.usergroup from users,access where users.uid = access.staff_id and users.uid = ".$uid); 
$row = $db->single();
extract($row);
$userid = $uid;
$uadm = $uadmin;

// build menu group list
$group_options = '';
for ($n = 1; $n < $muser+1; $n = $n+1) {
	if ($n == $usergroup) {
		$group_options .= "<option value=\"".$n."\"selected=\"selected\">".$n."</option>";
	} else {
		$group_options .= "<option value=\"".$n."\">".$n."</option>";
	}
}

// populate admin list
    $arr = array('No', 'Yes');
	if ($uadm == 'Y') {
		$adm = 'Yes';
	} else {
		$adm = 'No';
	}
	$admin_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $adm) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$admin_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

$findb = $_SESSION['s_findb'];

// populate branch drop down
$db->query("select * from ".$findb.".branch");
$rows = $db->resultset();
$branch_options = "<option value=\"0\">Select Branch</option>";
foreach ($rows as $row) {
	extract($row);
	if ($branch == $ubranch) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$branch_options .= '<option value="'.$branch.'"'.$selected.'>'.$branchname.'</option>';
}


$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

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
	$.get("../ajax/ajaxCheckup.php", {um: um}, function(data){
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
	<input type="hidden" name="chgpassword" id="chgpassword" value="N">
	<input type="hidden" name="chguser" id="chguser" value="N">

  <table width="590" border="0" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Edit User </u></div></td>
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
        <input type="button" name="chgpwd" value="Change User Name" onClick="changeuser()">
      </div></td>      <td><div align="left">
        <input name="un" type="text" id="un"  value="" size="50"  onBlur="checkup()">
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
      <td class="boxlabel">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel">Phone</td>
      <td><input name="phone1" type="text" id="phone1" value="<?php echo $uphone; ?>"  size="50" maxlength="45"></td>
    </tr>
    <tr>
      <td class="boxlabel">Mobile</td>
      <td><input name="phone2" type="text" id="phone2" value="<?php echo $umobile; ?>"  size="50" maxlength="45"></td>
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
      <td class="boxlabel">Menu Group</td>
      <td><div align="left">
        <select name="menugroup">
              <?php echo $group_options; ?>
   	     </select>
      </div></td>
    </tr>
    <tr>
      <td class="boxlabel">Associate with Branch</td>
      <td><select name="branches" id="branches"><?php echo $branch_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" id="save" ></td>
    </tr>
  </table>
</form>

	<script>
		document.onkeypress = stopRKey;
		document.getElementById('password').style.visibility = 'hidden';
		document.getElementById('un').style.visibility = 'hidden';
    </script> 

<?php
	if(isset($_POST['save'])) {
	
		$username = 'N';
		$password = 'N';
		$fname = trim($_REQUEST['firstname']);
		$lname = trim($_REQUEST['lastname']);
		$email = $_REQUEST['email'];
		$admin = $_REQUEST['ladministrator'];
		$phone1 = $_REQUEST['phone1'];
		$phone2 = $_REQUEST['phone2'];
		//$guser = $_REQUEST['guser'];
		//$gpwd = $_REQUEST['gpwd'];

		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$db->query("update users set ufname = :ufname, uemail = :uemail, uphone = :uphone, umobile = :umobile, uadmin = :uadmin, ulname = :ulname where uid = :uid");
		$db->bind(':ufname', $fname);
		$db->bind(':uemail', $email);
		$db->bind(':uphone', $phone1);
		$db->bind(':umobile', $phone2);
		$db->bind(':uadmin', $admin);
		$db->bind(':ulname', $lname);
		$db->bind(':uid', $userid);
		
		$db->execute();
		
		if ($_REQUEST['chguser'] == "Y") {
			$username = $_REQUEST['un'];
			
			$db->query("update users set username = :username where uid = :uid");
			$db->bind(':username', md5($username));
			$db->bind(':uid', $userid);
			$db->execute();
			
		}
		if ($_REQUEST['chgpassword'] == "Y") {
			$password = $_REQUEST['password'];
			$salt = base64_encode(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
			$hash = hash('sha256',$salt.$password);
			
			$db->query("update users set upwd = :uname, usalt = :usalt where uid = :uid");
			$db->bind(':uname', $hash);
			$db->bind(':usalt', $salt);
			$db->bind(':uid', $userid);
			$db->execute();	
		}

		$adm = $_REQUEST['ladministrator'];
		$meng = $_REQUEST['menugroup'];;
	
		$ubranch = $_REQUEST['branches'];
		
		$db->query("update access set branch = '".$ubranch."', usergroup = ".$meng." where staff_id = ".$userid." and coyid = ".$coyid);
		$db->execute();
		
		$_SESSION['s_ubranch'] = $ubranch;
		
		$db->query("update users set uadmin = '".$adm."' where uid = ".$userid);
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

