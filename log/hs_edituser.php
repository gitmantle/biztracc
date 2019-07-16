<?php
session_start();
$dbs = $_SESSION['s_admindb'];
$uid = $_SESSION['s_userid'];
$ubranch = $_SESSION['s_ubranch'];
$usersession = $_SESSION['usersession'];
$coyid = $_SESSION['s_coyid'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$muser = $usergroup;
$madm = $admin;

$query = "select users.ufname,users.ulname,users.coyowner,users.uadmin,users.uemail,users.uphone,users.umobile,users.ug_user,users.ug_pwd,users.uadmin,users.logsuser,users.logspwd,access.usergroup from users,access where users.uid = access.staff_id and users.uid = ".$uid; 
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
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

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate branch drop down
$query = "select * from branch";
$result = mysql_query($query) or die(mysql_error());
$branch_options = "<option value=\"0\">Select Branch</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($branch == $ubranch) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$branch_options .= '<option value="'.$branch.'"'.$selected.'>'.$branchname.'</option>';
}

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
	var pd = document.getElementById('password').value;
	$.get("../ajax/ajaxCheckup.php", {um: um, pd: pd}, function(data){
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
        <input name="un" type="text" id="un"  value="" size="50" >
      </div></td>
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
      <td class="boxlabel">iPad Username</td>
      <td><input type="text" name="ipuser" id="ipuser" value="<?php echo $logsuser; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">iPad Password</td>
      <td><input type="text" name="ippwd" id="ippwd" value="<?php echo $logspwd; ?>"></td>
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
		if ($_REQUEST['chguser'] == "Y") {
			$username = $_REQUEST['un'];
		}
		if ($_REQUEST['chgpassword'] == "Y") {
			$password = $_REQUEST['password'];
		}


		$moduledb = $_SESSION['s_admindb'];
		mysql_select_db($moduledb) or die(mysql_error());

		$sSQLString = "update users set ";
		$sSQLString .= "ufname = '".$fname."',";
		if ($username != 'N') {
			$sSQLString .= "username = '".md5($username)."',";
		}
		if ($password != 'N') {
			$sSQLString .= "upwd = '".md5($password)."',";
		}
		$sSQLString .= "uemail = '".$email."',";
		$sSQLString .= "uphone = '".$phone1."',";
		$sSQLString .= "umobile = '".$phone2."',";
		$sSQLString .= "uadmin = '".$admin."',";
		//$sSQLString .= "ug_user = '".$guser."',";
		//$sSQLString .= "ug_pwd = '".$gpwd."',";
		$sSQLString .= "ulname = '".mysql_real_escape_string($lname)."'";
		$sSQLString .= " where uid = ".$userid;
		
		$r = mysql_query($sSQLString) or die (mysql_error().' '.$sSQLString);
		
		$iu = $_REQUEST['ipuser'];
		$ip = $_REQUEST['ippwd'];
		$adm = $_REQUEST['ladministrator'];
		$meng = $_REQUEST['menugroup'];;
		
	
		$ubranch = $_REQUEST['branches'];
		
		$q = "update access set branch = '".$ubranch."', usergroup = ".$meng." where staff_id = ".$userid." and coyid = ".$coyid;
		$r = mysql_query($q) or die (mysql_error().' '.$q);
		
		$_SESSION['s_ubranch'] = $ubranch;
		
		if ($iu == '') {
			$iu = '**';
		}
		if ($ip == '') {
			$ip = '**';
		}
		
		$q = "update users set logsuser = '".$iu."', logspwd = '".$ip."' where uid = ".$userid;
		$r = mysql_query($q) or die (mysql_error().' '.$q);
		
		$q = "update users set uadmin = '".$adm."' where uid = ".$userid;
		$r = mysql_query($q) or die (mysql_error().' '.$q);


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

