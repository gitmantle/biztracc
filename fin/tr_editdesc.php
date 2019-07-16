<?php
session_start();
$usersession = $_SESSION['usersession'];

$rf = $_REQUEST['uid'];

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


$db->query("select item from ".$findb.".invtrans where uid = ".$rf);
$row = $db->single();
extract($row);
$itm = $item;

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Item Description</title>
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
      <td colspan="2"><div align="center" class="style1"><u>Edit Item Description </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Item Description</td>
      <td><input name="itemdesc" type="text" id="itemdesc"  size="60" maxlength="60" value="<?php echo $itm; ?>"></td>
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
		
			$itemdesc = $_REQUEST['itemdesc'];

      include_once("../includes/DBClass.php");
      $db = new DBClass();  

      $db->query("update ".$findb.".invtrans set item = :item where uid = :uid");
		  $db->bind(':item', $itemdesc);
		  $db->bind(':uid', $rf);
	
		  $db->execute();
      $db->closeDB();  
	
		?>
			<script>
			window.open("","rep_viewtradingtrans").jQuery("#tradlist").trigger("reloadGrid");
			this.close()
			</script>
		<?php
	
	}

  
?>
 

</body>
</html>

