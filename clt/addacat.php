<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

include_once("../includes/DBClass.php");
$db = new DBClass();

$cluid = $_SESSION['s_memberid'];

$cltdb = $_SESSION['s_cltdb'];

// populate industry drop down
$db->query("select * from ".$cltdb.".acccats order by acccat");
$rows = $db->resultset();
$acat_options = "<option value=\"\">Select Category</option>";
foreach ($rows as $row) {
	extract($row);
	$acat_options .= '<option value="'.$acccat.'">'.$acccat.'</option>';
}

$db->closeDB();
?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Account Catgegory</title>
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
  <table width="490" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Add Account Category </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Account Category</td>
      <td align="left"><select name="acat" id="acat">
            <?php echo $acat_options;?>
          </select></td>
      </tr>
	<tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" ></td>
      </tr>
  </table>
</form>

<script>
	document.getElementById('ctype').focus();
	document.onkeypress = stopRKey;
</script> 

</div>

<?php

	if(isset($_POST['save'])) {

		if ($_REQUEST['acat'] == '') {
			echo '<script>';
			echo 'alert("Please select a category.")';
			echo '</script>';	
		} else {			

				include_once("../includes/cltadmin.php");
				$oIn = new cltadmin;	
				
				$oIn->acat = $_REQUEST['acat'];
				$oIn->member_id = $cluid;
		
				$oIn->Addacat();
	
				?>
				<script>
				window.open("","editmembers").jQuery("#acatlist").trigger("reloadGrid");
				this.close();
				</script>
				<?php
		
			
		}
	}

?>


</body>
</html>
