<?php
session_start();
$induid = $_REQUEST['uid'];
$dbase = $_SESSION['s_admindb'];

require("../db.php");
mysql_select_db($dbase) or die(mysql_error());

$query = "select * from streets where street_id = ".$induid;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit NZ Street Post Code</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

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
<div id="swin">
<form name="form1" method="post" >
  <table width="400" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Edit NZ Street Post code </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Street</td>
      <td><input type="text" name="street" id="street" value="<?php echo $street; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Suburb/Town</td>
      <td><input type="text" name="sub" id="sub" value="<?php echo $suburb; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Area</td>
      <td><input type="text" name="area" id="area" value="<?php echo $area; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Post Code</td>
      <td><input name="postcode" type="text" id="postcode" size="4" maxlength="4" value="<?php echo $postcode; ?>" ></td>
    </tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" ></td>
      </tr>
  </table>
</form>
</div>

	<script>document.onkeypress = stopRKey;</script> 

<?php

	if(isset($_POST['save'])) {
		$ok = 'Y';

		if ($_REQUEST['street'] == '') {
			echo '<script>';
			echo 'alert("Please enter a street.")';
			echo '</script>';	
			$ok = 'N';
		}
		if ($_REQUEST['sub'] == '') {
			echo '<script>';
			echo 'alert("Please enter a suburb/town.")';
			echo '</script>';	
			$ok = 'N';
		}
		if ($_REQUEST['area'] == '') {
			echo '<script>';
			echo 'alert("Please enter an area.")';
			echo '</script>';	
			$ok = 'N';
		}
		if ($_REQUEST['postcode'] == '') {
			echo '<script>';
			echo 'alert("Please enter a post code.")';
			echo '</script>';	
			$ok = 'N';
		}
		if ($ok == 'Y') {	
	
				include_once("../includes/mantleadmin.php");
				$oBx = new mantleadmin;	
				
				$oBx->uid = $induid;
				$oBx->street = $_REQUEST['street'];
				$oBx->suburb = $_REQUEST['sub'];
				$oBx->area = $_REQUEST['area'];
				$oBx->postcode = $_REQUEST['postcode'];
		
				$oBx->EditStreet();
	
				?>
				<script>
				window.open("","updtstreets").jQuery("#streetlist").trigger("reloadGrid");
				this.close();
				</script>
				<?php
		
			
		}
	}

?>



</body>
</html>
