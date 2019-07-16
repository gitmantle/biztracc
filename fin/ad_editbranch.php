<?php
session_start();

$uid = $_REQUEST['uid'];

$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from ".$findb.".branch where uid = ".$uid);
$row = $db->single();
extract($row);

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit a Branch</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>


<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>

<body>

<div id="swin">
<form name="form1" method="post" >

  <table width="500" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Edit a Branch </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Branch Code</td>
      <td><input name="code" type="text" id="code"  size="5" maxlength="3" value="<?php echo $branch; ?>" readonly></td>
    </tr>
	<tr>
	<td class="boxlabel">Branch Name</td>
	<td><input type="text" name="desc" id="desc" value="<?php echo $branchname; ?>"
></td>
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
		
		$brname = $_REQUEST['desc'];
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$db->query("update ".$findb.".branch set branchname = '".$brname."' where uid = ".$uid);
		$db->execute();
		
		$db->closeDB();
		
		?>
		<script>
		window.open("","ad_branches").jQuery("#branchlist").trigger("reloadGrid");
		this.close();
		</script>
		<?php
	
	}

?>


</body>
</html>
