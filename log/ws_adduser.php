<?php
session_start();
$dbs = $_SESSION['s_admindb'];
$usersession = $_SESSION['usersession'];
$ubranch = $_SESSION['s_ubranch'];
$coyid = $_SESSION['s_coyid'];
$ws = $_SESSION['s_wsstaff'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$muser = $usergroup;
$sid = $subid;
$madm = $admin;

// build menu group list
$group_options = '';
for ($n = 5; $n < 7; $n = $n+1) {
	$group_options .= "<option value=\"".$n."\">".$n."</option>";
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

$moduledb = $_SESSION['s_admindb'];
mysql_select_db($moduledb) or die(mysql_error());


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
      <td><input name="phone1" type="text" id="phone1"  size="50" maxlength="45"></td>
    </tr>
    <tr>
      <td class="boxlabel">Mobile</td>
      <td><input name="phone2" type="text" id="phone2"  size="50" maxlength="45"></td>
    </tr>
    <tr>
      <td class="boxlabel">Menu Group</td>
      <td><div align="left">
        <select name="menugroup">
              <?php echo $group_options; ?>
   	     </select>
      </div></td>
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
	
		$ladministrator = 'N';
		$sub_id = $sid;
		$coy_id = $coyid;
		$module = 'prc';
		$fname = trim($_REQUEST['firstname']);
		$lname = trim($_REQUEST['lastname']);
		$email = $_REQUEST['email'];
		$phone1 = $_REQUEST['phone1'];
		$phone2 = $_REQUEST['phone2'];
		if ($_REQUEST['chguser'] == "Y") {
			$username = $_REQUEST['un'];
		}
		if ($_REQUEST['chgpassword'] == "Y") {
			$password = $_REQUEST['password'];
		}
		$menugroup = $_REQUEST['menugroup'];
									
		$moduledb = $_SESSION['s_admindb'];
		mysql_select_db($moduledb) or die(mysql_error());


		$sSQLString = "insert into users (ufname,ulname,uemail,uphone,umobile,uadmin,username,upwd,sub_id,workshop_id) values ";
		$sSQLString .= "('".mysql_real_escape_string($fname)."','";
		$sSQLString .= mysql_real_escape_string($lname)."','";
		$sSQLString .= $email."','";
		$sSQLString .= $phone1."','";
		$sSQLString .= $phone2."','";
		$sSQLString .= $admin."','";
		$sSQLString .= md5($username)."','";
		$sSQLString .= md5($password)."',";
		$sSQLString .= $sid.",";	
		$sSQLString .= $ws.")";	
		
		$r = mysql_query($sSQLString) or die (mysql_error().' '.$sSQLString);
		
		$suid = mysql_insert_id();
		
		
		$ubranch = '0001';
		
		$q = "insert into access (staff_id,subid,coyid,module,usergroup,branch) values (";
		$q .= $suid.",";
		$q .= $sid.",";
		$q .= $coyid.",";
		$q .= "'prc',";
		$q .= $menugroup.",";
		$q .= "'".$ubranch."')";
		
		$r = mysql_query($q) or die (mysql_error().' '.$q);
		



?>
	<script>
	  window.open("","wsstaff").jQuery("#wsstafflist").trigger("reloadGrid");
	  this.close()
	</script>
<?php
	
	}

?>
 
 

</body>
</html>

