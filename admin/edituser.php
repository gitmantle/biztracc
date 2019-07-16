<?php
session_start();
$dbs = $_SESSION['s_admindb'];
$usersession = $_SESSION['usersession'];

require("../db.php");
mysql_select_db($dbs) or die(mysql_error());
$uid = $_SESSION['s_userid'];
$cid = $_SESSION['s_coyid'];
$subid = $_SESSION['s_subid'];

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);


$query = "select users.ufname,users.ulname,users.coyowner,users.uadmin,users.uemail,users.uphone,users.umobile,users.ug_user,users.ug_pwd from users where users.uid = ".$uid; 


$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

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
	if ($admin == 'Y') {
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

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Users</title>
<link rel="stylesheet" href="../includes/kenny.css" media="screen" type="text/css">
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
    <tr>
      <td class="boxlabel">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel">Google Calendar Username</td>
      <td><input name="guser" type="text" id="guser" value="<?php echo $g_user; ?>"  size="50" maxlength="45"></td>
    </tr>
    <tr>
      <td class="boxlabel">Google Calendar Password</td>
      <td><input name="gpwd" type="text" id="gpwd" value="<?php echo $g_pwd; ?>"  size="50" maxlength="45"></td>
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

	<script>document.onkeypress = stopRKey;</script> 

<?php
	if(isset($_POST['save'])) {
	
		include_once("subadmin.php");
		$oAcc = new subadmin;
	
		$oAcc->uid = $uid;
		$oAcc->fname = trim($_REQUEST['firstname']);
		$oAcc->lname = trim($_REQUEST['lastname']);
		$oAcc->email = $_REQUEST['email'];
		$oAcc->admin = 'Y';
		$oAcc->phone1 = $_REQUEST['phone1'];
		$oAcc->phone2 = $_REQUEST['phone2'];
		$oAcc->guser = $_REQUEST['guser'];
		$oAcc->gpwd = $_REQUEST['gpwd'];
		if ($_REQUEST['chguser'] == "Y") {
			$oAcc->username = $_REQUEST['un'];
		}
		if ($_REQUEST['chgpassword'] == "Y") {
			$oAcc->password = $_REQUEST['password'];
		}
		$oAcc->menugroup = 20;
								
		$oAcc->EditStaff();



?>
	<script>
	  window.open("","spupdtstaff").jQuery("#stafflist2").trigger("reloadGrid");
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

