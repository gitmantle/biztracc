<?php
session_start();

$faid = $_REQUEST['uid'];
					 
$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from ".$findb.".assetheadings where uid = ".$faid);
$row = $db->single();
extract($row);

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Fixed Asset Heading</title>
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
      <td colspan="6"><div align="center" class="style1"><u>Edit Fixed Asset Heading </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Fixed Asset Group Heading</td>
      <td colspan="3"><input name="fahead" type="text" id="fahead"  size="40" maxlength="40" value="<?php echo $heading; ?>"></td>
      </tr>
	  <td>&nbsp;</td>
	  <td colspan="5" align="right"><input type="submit" value="Save" name="save" ></td>
	  </tr>
  </table>
  
 <script>
	document.getElementById('fahead').focus();
 </script>
  
  
</form>
</div>

<?php
	if(isset($_POST['save'])) {
		
		if ($_REQUEST['fahead'] == '') {
			echo '<script>';
			echo 'alert("Please enter a heading.")';
			echo '</script>';	
		} else {	
		
			$fah = strtoupper(trim($_REQUEST['fahead']));
			
			include_once("../includes/DBClass.php");
			$db = new DBClass();
			
			$db->query("update ".$findb.".assetheadings set heading = '".$fah."' where uid = ".$faid);
			$db->execute();
			$db->closeDB();
	
			?>
				<script>
				window.open("","fahead").jQuery("#faheadlist").trigger("reloadGrid");
				this.close()
				</script>
			<?php
		}
	
	}
?>
 

</body>
</html>

