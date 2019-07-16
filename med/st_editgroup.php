<?php
session_start();
require_once("../db.php");

$uid = $_REQUEST['uid'];

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "select * from stkgroup where groupid = ".$uid;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Stock Group</title>
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
      <td colspan="2"><div align="center" class="style1"><u>Edit Stock Group </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Group Name</td>
      <td><input name="grname" type="text" id="grname"  size="30" maxlength="30" value="<?php echo $groupname; ?>"></td>
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
		
		if ($_REQUEST['grname'] == '') {
			echo '<script>';
			echo 'alert("Please enter a group name.")';
			echo '</script>';	
		} else {	
		
			$grname = strtoupper($_REQUEST['grname']);
			include_once("includes/addmed.php");
			$oAcc = new addmed;
			
			$oAcc->uid = $uid;	
			$oAcc->groupname = strtoupper($_REQUEST['grname']);
									
			$oAcc->EditGroup();
	
			?>
				<script>
				window.open("","updtstkgroups").jQuery("#stkgrouplist").trigger("reloadGrid");
				this.close()
				</script>
			<?php
		}
	
	}
?>
 

</body>
</html>

