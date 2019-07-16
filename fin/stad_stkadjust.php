<?php
session_start();

$stkcd = $_REQUEST['id'];
$i = explode('~',$stkcd);
$itemcd = $i[0];
$itemlc = $i[1];
					 
$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("SELECT stktrans.itemcode,stktrans.item,stktrans.locid,stklocs.location, stkmast.onhand,stkmast.unit FROM ".$findb.".stktrans,".$findb.".stklocs,".$findb.".stkmast WHERE stktrans.itemcode = stkmast.itemcode and stktrans.itemcode = '".$itemcd."' and stklocs.uid = ".$itemlc);
$row = $db->single();
extract($row);
$lid = $locid;

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Adjust stock quantity</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />

<script>

function post() {

	//add validation here if required.
	var add = document.getElementById('add').value;
	var subtract = document.getElementById('subtract').value;
	var reason = document.getElementById('reason').value;
	var onhand = document.getElementById('onhand').value;
	
	var ok = "Y";
	if (add == "" && subtract == "") {
		alert("Please enter an amount to add or subtract.");
		ok = "N";
		return false;
	}
	if (parseFloat(add) + parseFloat(subtract) == 0 ) {
		alert("Please enter an amount to either add or subtrac, not both.");
		ok = "N";
		return false;
	}
	if (reason == "") {
		alert("Please enter a reason.");
		ok = "N";
		return false;
	}
	if (onhand < 0 && subtract > 0) {
		alert("You may not decrease a negative stock balance")
		ok = "N";
		return false;
	}
	
	if ((subtract > onhand) && (onhand > 0)) {
		alert("You may not decrease stock by more than is on hand.");
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
<div id="swin">
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="460" border="0" align="center">
    <tr>
      <td colspan="8"><div align="center" class="style1"><u>Adjust Stock Quantity </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Item Code</td>
      <td colspan="5"><input type="text" name="itemcode" id="itemcode" readonly value="<?php echo $itemcode; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabel">Item</td>
      <td colspan="3"><input type="text" name="Item" id="Item" readonly value="<?php echo $item; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Location</td>
      <td colspan="3"><input type="text" name="location" id="location" readonly value="<?php echo $location; ?>"></td>
    </tr>
    <tr>
      <td colspan="4" class="boxlabelleft">Current stock shows how many items are currently on hand and does not include uncosted items.</td>
      </tr>
    <tr>
      <td class="boxlabel">Current stock</td>
      <td><input type="text" name="onhand" id="onhand" size="10" readonly value="<?php echo $onhand; ?>"></td>
      <td class="boxlabelleft">Unit</td>
      <td><input type="text" name="unit" id="unit" size="10" readonly value="<?php echo $unit; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Add</td>
      <td><input type="text" name="add" id="add" size="10" value="0" onFocus="this.select();"></td>
      <td class="boxlabelleft">Subtract</td>
      <td><input type="text" name="subtract" id="subtract" size="10" value="0" onFocus="this.select();"></td>
    </tr>
    <tr>
      <td class="boxlabel">Reason</td>
      <td colspan="3"><input type="text" name="reason" id="reason" size="50"></td>
    </tr>
	  <td>&nbsp;</td>
	  <td colspan="7" align="right"><input type="button" value="Save" name="save" onClick="post()"  >
	  </tr>
  </table>
  
  
</form>
</div>

<?php
	if($_REQUEST['savebutton'] == "Y") {
		$add = $_REQUEST['add'];
		$subtract = $_REQUEST['subtract'];
		$reason = $_REQUEST['reason'];
		$icode = $_REQUEST['itemcode'];
		$loc = $_REQUEST['location'];
		$ddate = date("Y-m-d");
		$reason = $_REQUEST['reason'];
		
		$add = (float)$add;
		$subtract = (float)$subtract;
		
		// update stktrans and stkmast
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$db->query("select adj from ".$findb.".numbers");
		$row = $db->single();
		extract($row);
		$refno = $adj + 1;
		$db->query("update ".$findb.".numbers set adj = :refno");
		$db->bind(':refno', $refno);
		$db->execute();

		$db->query("select groupid,catid,item,avgcost from ".$findb.".stkmast where itemcode = '".$icode."'");
		$row = $db->single();
		extract($row);
		
		if ($add > 0 ) {
			$value = $add * $avgcost;
		}
		if ($subtract > 0 ) {
			$value = $subtract * $avgcost;
		}
		
		$db->query("insert into ".$findb.".stktrans (groupid,catid,itemcode,item,locid,ddate,increase,decrease,ref_no,transtype,amount,note) values (:groupid,:catid,:itemcode,:item,:locid,:ddate,:increase,:decrease,:ref_no,:transtype,:amount,:note)");
		$db->bind(':groupid', $groupid);
		$db->bind(':catid', $catid);
		$db->bind(':itemcode',$icode);
		$db->bind(':item', $item);
		$db->bind(':locid', $lid);
		$db->bind(':ddate', $ddate);
		$db->bind(':increase', $add);
		$db->bind(':decrease', $subtract);
		$db->bind(':ref_no', 'ADJ'.$refno);
		$db->bind(':transtype', 'ADJ');
		$db->bind(':amount', $value);
		$db->bind(':note', $reason);
		
		$db->execute();
		
		if ($add > 0) {
			$db->query("update ".$findb.".stkmast set onhand = onhand + ".$add." where itemcode = '".$icode."'");
			$db->execute();
		}
		if ($subtract > 0) {
			$db->query("update ".$findb.".stkmast set onhand = onhand - ".$subtract." where itemcode = '".$icode."'");
			$db->execute();
		}
		
		$db->closeDB();	
	
			?>
				<script>
				window.open("","stad_adj").jQuery("#stkadjlist").trigger("reloadGrid");
				window.open("","stad_adj").jQuery("#stklqlist").trigger("reloadGrid");
				this.close()
				</script>
			<?php
		}
	
?>
 

</body>
</html>

