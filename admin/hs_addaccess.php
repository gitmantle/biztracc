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

// build menu group list
$group_options = '';
for ($n = 1; $n < $muser+1; $n = $n+1) {
	$group_options .= "<option value=\"".$n."\">".$n."</option>";
}

$findb = $_SESSION['s_findb'];
$admdb = $_SESSION['s_admindb'];


// populate company drop down
$db->query("select * from ".$admdb.".companies where coysubid = ".$sid);
$rows = $db->resultset();
$company_options = "<option value=\"0\">Select Company</option>";
foreach ($rows as $row) {
	extract($row);
	$selected = '';
	$company_options .= '<option value="'.$coyid.'"'.$selected.'>'.$coyname.'</option>';
}

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add User Access</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function getbranches() {
	var coy = document.getElementById('company').value;
	$.get("ajaxgetbranches.php", {coy: coy}, function(data){


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
      <td colspan="2"><div align="center" class="style1"><u>Add User Access</u></div></td>
    </tr>
    <tr><td colspan="2"> <hr>  </td></tr>
    <tr>
    	<td class="boxlabel">Client Relationship Management</td>
        <td><input type="checkbox" name="cltbox" id="cltbox" value="clt"></td>
    </tr>
    <tr>
      <td class="boxlabel">Menu Group</td>
      <td><div align="left">
        <select name="menugroup_clt">
              <?php echo $group_options; ?>
   	     </select>
      </div></td>
    </tr>  
    <tr><td colspan="2"> <hr>  </td></tr>
    
    <tr>
    	<td class="boxlabel">Financial Management</td>
        <td><input type="checkbox" name="finbox" id="finbox" value="fin"></td>
    </tr>
      <tr>
      <td class="boxlabel">Company </td>
      <td><div align="left">
        <select name="company" id="company" onChange="getbranches()"><?php echo $company_options; ?></select>
      </div></td>
    </tr>
     <tr>
      <td class="boxlabel">Menu Group</td>
      <td><div align="left">
        <select name="menugroup_fin">
              <?php echo $group_options; ?>
   	     </select>
      </div></td>
    </tr>  
    <tr>
      <td class="boxlabel">Associate with Branch</td>
      <td><select name="branches" id="branches"><?php echo $branch_options; ?>
      </select></td>
    </tr>
   
    
    <tr><td colspan="2"> <hr>  </td></tr>
    <tr>
    	<td class="boxlabel">Processes</td>
        <td><input type="checkbox" name="prcbox" id="prcbox" value="prc"></td>
    </tr>
      <tr>
      <td class="boxlabel">Company </td>
      <td><div align="left">
        <select name="company" id="company" onChange="getprocesses()"><?php echo $company_options; ?></select>
      </div></td>
    </tr>
    <tr>
      <td class="boxlabel">Process</td>
      <td><select name="process" id="process"><?php echo $process_options; ?>
      </select></td>
    </tr>
     <tr>
      <td class="boxlabel">Menu Group</td>
      <td><div align="left">
        <select name="menugroup_prc">
              <?php echo $group_options; ?>
   	     </select>
      </div></td>
    </tr>  
    
    <tr><td colspan="2"> <hr>  </td></tr>
    
    
    
    
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

		$db->query("insert into users (ufname,ulname,uemail,uphone,umobile,uadmin,username,upwd,sub_id) values (:ufname,:ulname,:uemail,:uphone,:umobile,:uadmin,:username,:upwd,:sub_id)");
		$db->bind(':ufname', $fname);
		$db->bind(':ulname', $lname);
		$db->bind(':uemail', $email);
		$db->bind(':uphone', $phone1);
		$db->bind(':umobile', $phone2);
		$db->bind(':uadmin', $admin);
		$db->bind(':username', md5($username));
		$db->bind(':upwd', md5($password));
		$db->bind(':sub_id', $sid);	
		
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

