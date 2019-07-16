<?php
session_start();

$stkid = $_REQUEST['itemid'];
$loc = $_REQUEST['loc'];

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$obaltable = 'ztmp'.$user_id.'_stkobal';
$serialtable = 'ztmp'.$user_id.'_serialnos';

$findb = $_SESSION['s_findb'];

$db->query("select location from ".$findb.".stklocs where uid = ".$loc);
$row = $db->single();
extract($row);
$locname = $location;

$db->query("select * from ".$findb.".".$obaltable." where itemid = ".$stkid);
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
<title>Add Stock Item - Opening Balance</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

<script>
function post() {

	//add validation here if required.
	var quantity = document.getElementById('qty').value;
	var averagecost = document.getElementById('avgcost').value;
	var snos = document.getElementById('serialnos').value;
	var sl = snos.split(',');
	var elems = sl.length;
	var q = parseFloat(document.getElementById('qty').value);
 	var sn = "<?php echo $trackserial; ?>";
	
	var ok = "Y";
	if (quantity == 0) {
		alert("Please enter a quantity.");
		ok = "N";
		return false;
	}
	if (sn == 'Yes' && elems != q) {
		alert("Please enter a serial number for each item.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
	
	
}	
	
	
</script>


<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="lwin">
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="750" border="0" align="center">
    <tr>
      <td colspan="4"><div align="center" class="style1"><u>Add Stock Item - Opening Balance </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Item Code</td>
      <td colspan="3" ><input name="stkcode" type="text" id="stkcode"  size="30" maxlength="30" readonly value="<?php echo $itemcode; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabel">Item Description</td>
      <td colspan="3"><input name="stkname" type="text" id="stkname"  size="60" maxlength="100" readonly value="<?php echo $item; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Location</td>
      <td colspan="3"><input type="text" name="cloc" id="cloc" readonly value="<?php echo $locname; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Quantity</td>
      <td colspan="3"><input type="text" name="qty" id="qty" value="0" onFocus="this.select()"></td>
    </tr>
    <tr>
      <td class="boxlabel">Average  Cost</td>
      <td><input type="text" name="avgcost" id="avgcost" value="0" onFocus="this.select()"></td>
      <td class="boxlabel">per</td>
      <td><input type="text" name="unit" id="unit" readonly value="<?php echo $unit; ?>"></td>
    </tr>
	  <tr id="tserial">
	    <td class="boxlabel">Serial Numbers - separated by commas</td>
	    <td colspan="3"><textarea name="serialnos" id="serialnos" cols="60" rows="5"></textarea></td>
      </tr>
	  <tr>
	    <td>&nbsp;</td>
	  <td colspan="3"  align="right"><input type="button" value="Save" name="save"  onClick="post()"  ></td>
	  </tr>
  </table>
 
 <script>
 	var s = "<?php echo $trackserial; ?>";
	if (s == 'Yes') {
		document.getElementById('tserial').style.visibility = 'visible';
	} else {
		document.getElementById('tserial').style.visibility = 'hidden';
	}
 </script>
  
</form>
</div>

<?php
	if($_REQUEST['savebutton'] == "Y") {
		
			$qty = $_REQUEST['qty'];
			$avgcost = $_REQUEST['avgcost'];
			$snos = $_REQUEST['serialnos'];
			
			include_once("../includes/DBClass.php");
			$db = new DBClass();
			
			$db->query("update ".$findb.".".$obaltable." set quantity = ".$qty.", avgcost = ".$avgcost.", location = ".$loc." where itemid = ".$itemid);
			$db->execute();
			
			if ($snos <> '') {
				$sn = explode(',',$snos);
				foreach ($sn as $value) {
					$db->query("insert into ".$findb.".".$serialtable." (itemcode,item,serialno,locationid,location) values (:itemcode,:item,:serialno,:locationid,:location)");
					$db-bind(':itemcode', $itemcode);
					$db-bind(':item', $item);
					$db-bind(':serialno', $value);
					$db-bind(':locationid', $location);
					$db-bind(':location', $locname);
					$db->execute();
				}
			}
			
			$db->closeDB();

			?>
				<script>
				window.open("","stad_openbal").jQuery("#stkobal").trigger("reloadGrid");
				this.close()
				</script>
			<?php
	
	}
?>
 

</body>
</html>

