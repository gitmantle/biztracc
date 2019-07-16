<?php
session_start();
$usersession = $_SESSION['usersession'];
$dbs = $_SESSION['s_admindb'];
$cid = $_SESSION['s_coyid'];

require("../db.php");
mysql_select_db($dbs) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subid = $_SESSION['s_subid'];

// populate menugroup list
	$group_options = '';
   for($i = 0; $i < 21; $i++)	{
		$group_options .= '<option value="'.$i.'">'.$i.'</option>';
 	  }
	  
// get subscriber name
$q = 'select coyname from companies where coyid = '.$cid;
$result = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Users</title>
<link rel="stylesheet" href="../includes/kenny.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

<script>
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
  <table width="590" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Add a User to <?php echo $coyname; ?></u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">First Name </td>
      <td><div align="left">
        <input name="firstname" type="text" id="firstname"  size="50" maxlength="45">
      </div></td>
    </tr>
    <tr>
      <td class="boxlabel">Last Name</td>
      <td><div align="left">
        <input name="lastname" type="text" id="lastname"  size="50" maxlength="45">
      </div></td>
    </tr>
    <tr>
      <td class="boxlabel">Email</td>
      <td align="left"><input name="email" type="text" id="email" size="50" maxlength="70"></td>
    </tr>
    <tr>
      <td class="boxlabel">User Name </td>
      <td><div align="left">
        <input name="un" type="text" id="un" size="50" >
      </div></td>
    </tr>
    <tr>
      <td class="boxlabel">Password</td>
      <td><div align="left">
        <input name="password" type="password" id="password" size="50" onBlur="checkup()">
        </div></td>
    </tr>
    <tr>
      <td class="boxlabel">Company Owner</td>
      <td><select name="lowner" id="lowner">
        <option value="N">No</option>
        <option value="Y">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Administrator</td>
      <td><select name="ladmin" id="ladmin">
        <option value="N">No</option>
        <option value="Y">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Menu Access Group</td>
      <td><div align="left">
	    <select name="menugroup" id="menugroup">
	      <?php echo $group_options;?>
        </select>
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
      <td class="boxlabel">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel">Google Calendar Username</td>
      <td><input name="guser" type="text" id="guser"  size="50" maxlength="45"></td>
    </tr>
    <tr>
      <td class="boxlabel">Google Calendar Password</td>
      <td><input name="gpwd" type="text" id="gpwd"  size="50" maxlength="45"></td>
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
	
	
		$oAcc->sub_id = $subid;
		$oAcc->coy_id = $cid;
		$oAcc->fname = trim($_REQUEST['firstname']);
		$oAcc->lname = trim($_REQUEST['lastname']);
		$oAcc->email = $_REQUEST['email'];
		$oAcc->username = $_REQUEST['un'];
		$oAcc->password = $_REQUEST['password'];
		$oAcc->menugroup = $_REQUEST['menugroup'];
		$oAcc->phone = $_REQUEST['phone1'];
		$oAcc->mobile = $_REQUEST['phone2'];
		$oAcc->guser = $_REQUEST['guser'];
		$oAcc->gpwd = $_REQUEST['gpwd'];
		$oAcc->coyowner = $_REQUEST['lowner'];
		$oAcc->admin = $_REQUEST['ladmin'];
		
		$oAcc->AddStaff();
		
?>
	<script>
	window.open("","spupdtstaff").jQuery("#stafflist2").trigger("reloadGrid");
	this.close()
	</script>
<?php
	
	}
?>
 

</body>
</html>

