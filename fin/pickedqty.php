<?php
session_start();
$rid = $_REQUEST['uid'];
$usersession = $_SESSION['usersession'];

$trdreturn = $_SESSION['s_tradingreturn'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$tradetable = 'ztmp'.$user_id.'_trading';

$findb = $_SESSION['s_findb'];

// get original quantity
$db->query("select item,quantity,origqty,pay from ".$findb.".".$tradetable." where uid = ".$rid);
$row = $db->single();
extract($row);
$oqty = $origqty

?>

<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Quantity</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

</head>

<body>

<div id="swin">

<form name="form1" method="post" >
  <table width="500" border="0" align="center">
    <tr>
      <td colspan="4"><div align="center" class="style1"><u>Edit a Quantity</u></div></td>
    </tr>
	<tr>
	<td class="boxlabel">&nbsp;</td>
	<td><span class="boxlabel">Item</span></td>
	<td class="boxlabelleft"><?php echo $item; ?></td>
	</tr>
	<tr>
	  <td class="boxlabel">&nbsp;</td>
	  <td>Available for returning</td>
	  <td><input type="text" name="oqty" id="oqty" value="<?php echo $oqty; ?>" readonly></td>
	  </tr>
	<tr>
	  <td class="boxlabel">&nbsp;</td>
	  <td>Quantity to return</td>
	  <td><input type="text" name="retqty" id="retqty" value="0"></td>
	  </tr>	
    <tr>
      <td>&nbsp;</td>
      <td colspan="2" align="right"><input type="submit" value="Save" name="save" ></td>
    </tr>
  </table>
</form>
</div>

<?php

	if(isset($_POST['save'])) {
		$returnqty = $_REQUEST['retqty'];
		
		if ($returnqty > $oqty) {
			echo '<script>';
			echo 'alert("You may not return more than you received.")';
			echo '</script>';	
		} else {	
			$db->query("update ".$findb.".".$tradetable." set quantity = ".$returnqty.", value = ".$returnqty." * price, tax = ".$returnqty." * price * taxpcent / 100 where uid = ".$rid);
			$db->execute();
			$db->query("update ".$findb.".".$tradetable." set tot = value + tax where uid = ".$rid);
			$db->execute();

			if ($trdreturn == 'crn') {
				echo '<script>';
				echo 'window.open("","tr_crn").jQuery("#purchlist").trigger("reloadGrid");';
				echo 'this.close();';
				echo '</script>';
			} else {
				echo '<script>';
				echo 'window.open("","tr_ret").jQuery("#purchlist").trigger("reloadGrid");';
				echo 'this.close();';
				echo '</script>';
			}
		}
	}
	
	$db->closeDB();

?>





</body>
</html>