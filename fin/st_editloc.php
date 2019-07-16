<?php
session_start();

$locid = $_REQUEST['uid'];
					 
$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from ".$findb.".stklocs where uid = ".$locid);
$row = $db->single();
extract($row);
$sbr = $branch;

// populate branch drop down
$db->query("select * from ".$findb.".branch");
$rows = $db->resultset();
$branch_options = "<option value=\"0\">Select Branch</option>";
foreach ($rows as $row) {
	extract($row);
	if ($branch == $sbr) {
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
<title>Edit Stock Location</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />


<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="swin">
<form name="form1" method="post" >
  <table width="460" border="0" align="center">
    <tr>
      <td colspan="6"><div align="center" class="style1"><u>Edit Stock Location </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Location Description</td>
      <td colspan="3"><input name="locname" type="text" id="locname"  size="40" maxlength="40" value="<?php echo $location; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabel">Associate with Branch</td>
      <td><select name="branches" id="branches"><?php echo $branch_options; ?>
      </select></td>
    </tr>
	  <td>&nbsp;</td>
	  <td colspan="5" align="right"><input type="submit" value="Save" name="save" ></td>
	  </tr>
  </table>
  
 <script>
	document.getElementById('pcentname').focus();
 </script>
  
  
</form>
</div>

<?php
	if(isset($_POST['save'])) {
		
		if ($_REQUEST['locname'] == '') {
			echo '<script>';
			echo 'alert("Please enter a location name.")';
			echo '</script>';	
		} else {	
		
			$loc = ucwords(trim($_REQUEST['locname']));
			$br = $_REQUEST['branches'];
			
			include_once("../includes/DBClass.php");
			$db = new DBClass();
			
			$db->query("update ".$findb.".stklocs set location = '".$loc."', branch = '".$br."' where uid = ".$locid);
			$db->execute();
			
			$db->closeDB();

			?>
				<script>
				window.open("","updtlocs").jQuery("#stkloclist").trigger("reloadGrid");
				this.close()
				</script>
			<?php
		}
	
	}
?>
 

</body>
</html>

