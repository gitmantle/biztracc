<?php
session_start();

if ($_SESSION['s_stkgroup'] > 0) {
	$stkgroup = $_SESSION['s_stkgroup'];
}else {
	echo '<script>';
	echo 'alert("Please highlight the relevant Stock Group for this category");';
	echo 'this.close();';
	echo '</script>';
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Stock Category</title>
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
      <td colspan="2"><div align="center" class="style1"><u>Add Stock Category </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Category Name</td>
      <td><input name="catname" type="text" id="catname"  size="30" maxlength="30"></td>
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
			include_once("includes/addmed.php");
			$oAcc = new addmed;
				
			$oAcc->groupid = $stkgroup;
			$oAcc->category = $catname;
									
			$oAcc->AddCategory();
	
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

