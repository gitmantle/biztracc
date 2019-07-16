<?php
session_start();
$usersession = $_SESSION['usersession'];

$id = $_REQUEST['id'];

if ($id == 0) {
	echo '<script>';
	echo 'alert("Please select a user in the top grid");';
	echo '</script>';
	return;
}

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$muser = $row['usergroup'];

$admdb = $_SESSION['s_admindb'];

// build menu group list
$group_options = '';
for ($n = 1; $n < $muser+1; $n = $n+1) {
	if ($n == $muser) {
		$selected = 'selected="selected"';
	} else {
		$selected = "";
	}
	$group_options .= '<option value="'.$n.'"'.$selected.'>'.$n.'</option>';
}

// populate company drop down
$db->query("select coyname from ".$admdb.".companies where coysubid = ".$coyid);
$rows = $db->single();
extract($row);

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



require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit User Access</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
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
      <td colspan="2"><div align="center" class="style1"><u>Edit User Access</u></div></td>
    </tr>
    <tr><td colspan="2"> <hr>  </td></tr>
      <tr>
      <td class="boxlabel">Company </td>
      <td><div align="left">
        <select name="company" id="company" ><?php echo $coyname; ?></select>
      </div></td>
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
      <td class="boxlabel">Associate with Branch</td>
      <td><select name="branches" id="branches">
      </select></td>
    </tr>
   

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
		
		$coyid = $_REQUEST['company'];
		$usergroup = $_REQUEST['menugroup'];
		$branch = $_REQUEST['branches'];
									
		$db->query("insert into ".$admdb.".access (staff_id,subid,coyid,module,usergroup,branch) values (:staff_id,:subid,:coyid,:module,:usergroup,:branch)");
		$db->bind(':staff_id', $id);
		$db->bind(':subid', $subid);
		$db->bind(':coyid', $coyid);
		$db->bind(':module', 'clt');
		$db->bind(':usergroup', $usergroup);
		$db->bind(':branch', $branch);
		$db->execute();

		$db->closeDB();

?>
	<script>
	  window.open("","hs_users").jQuery("#accesslist").trigger("reloadGrid");
	  this.close();
	</script>
<?php
	
	}

?>
 
 

</body>
</html>

