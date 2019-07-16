<?php
session_start();
$usersession = $_SESSION['usersession'];

$uid = $_REQUEST['uid'];
$stkgroup = $_SESSION['s_stkgroup'];

$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];


$db->query("select * from ".$findb.".stkcategory where catid = ".$uid);
$row = $db->single();
extract($row);
$gpid = $groupid;

// populate groups drop down
$db->query("select * from ".$findb.".stkgroup");
$rows = $db->resultset();
$stkgroup_options = "<option value=\"0\">Select Group</option>";
foreach ($rows as $row) {
	extract($row);
	if ($gpid == $stkgroup) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$stkgroup_options .= '<option value="'.$groupid.'"'.$selected.'>'.$groupname.'</option>';
}

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Stock Catgegory</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="mwin">
<form name="form1" method="post" >
  <table width="600" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Edit Stock Category </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Category Name</td>
      <td><input name="catname" type="text" id="catname"  size="30" maxlength="30" value="<?php echo $category; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabel">Stock Group</td>
      <td><select name="sgroup" id="sgroup"><?php echo $stkgroup_options;?></select></td>
    </tr>  
	<tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" ></td>
      </tr>
  </table>
</form>
</div>

<?php
	if(isset($_POST['save'])) {
		
		if ($_REQUEST['catname'] == '') {
			echo '<script>';
			echo 'alert("Please enter a category name.")';
			echo '</script>';	
		} else {	
		
			$catname = ucwords(trim($_REQUEST['catname']));
			$stkgroup = $_REQUEST['sgroup'];
			include_once("includes/accaddacc.php");
			$oAcc = new accaddacc;
				
			$oAcc->uid = $uid;
			$oAcc->groupid = $stkgroup;
			$oAcc->category = $catname;
									
			$oAcc->EditCategory();
	
			?>
				<script>
				window.open("","updtstkgroups").jQuery("#stkcatslist").trigger("reloadGrid");
				this.close()
				</script>
			<?php
		}
	
	}
?>
 

</body>
</html>

