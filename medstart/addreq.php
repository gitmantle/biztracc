<?php
session_start();
error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);
$sessionid = session_id();

include_once("../includes/DBClass.php");
$db = new DBClass();

$prcdb = $_SESSION['s_prcdb'];
$findb = $_SESSION['s_findb'];

$rectable = $_SESSION['s_rectable'];
$gen = $_REQUEST['generic'];

// populate medicine drop down
$db->query("select itemid,item from ".$findb.".stkmast where generic = 'N'");
$rows = $db->resultset();
$medicine_options = "<option value=\"0\">Select Medicine</option>";
foreach ($rows as $row) {
	extract($row);
	$selected = '';
	$medicine_options .= '<option value="'.$itemid.'"'.$selected.'>'.$item.'</option>';
}
	
$db->closeDB();

?>

<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Medicine</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>
function post() {

	//add validation here if required.
	var med = document.getElementById('med').value;
	var qty = document.getElementById('req').value;
	
	var ok = "Y";
	if (med == 0) {
		alert("Please enter a medicine.");
		ok = "N";
		return false;
	}
	if (qty == 0) {
		alert("Please enter a quantity.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
	
}	 
</script>
	 
</head>


<body>
<div id="swin">
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="400" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Add Medicine </u></div></td>
    </tr>
    <tr>
      <td width="106" class="boxlabel">Medicine</td>
      <td><select name="med" id="med">
            <?php echo $medicine_options;?>
          </select></td>
      </tr>
    <tr>
      <td class="boxlabel">Quantity</td>
      <td><input type="text" name="req" id="req" value="0" onFocus="this.select();"></td>
    </tr>
    <tr>
      <td class="boxlabel">Per</td>
      <td><p>
        <label>
          <input type="radio" name="dosage" value="Day" id="Daily">
          Day</label>
        <br>
        <label>
          <input type="radio" name="dosage" value="Week" id="Weekly">
          Week</label>
        <br>
        <label>
          <input type="radio" name="dosage" value="28 Days" id="Monthly">
          28 Days</label>
        <br>
      </p></td>
    </tr>
	<tr>
	  <td>&nbsp;</td>
	  <td align="right"> <input type="button" value="Save" name="save" onClick="post()"  ></td>
	  </tr>
  </table>
</form>

<script>
	document.getElementById('Monthly').checked = true;
</script> 


</div>

<?php

	if($_REQUEST['savebutton'] == "Y") {
				
		$client_id = $cluid;
		$medicine = $_REQUEST['med'];
		$required = $_REQUEST['req'];
		$dosage = $_REQUEST['dosage'];
		$today = date("Y-m-d");
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
	
		$db->query("select pcent from ".$findb.".stkpricepcent where uid = 1");
		$row = $db->single();
		extract($row);	
		$spcent = $pcent;
		
		$markup = 1 + $spcent/100;
				
		
		if ($gen == 'No') {		
			$db->query("select item, noinunit, case setsell when 0 then (avgcost * ".$markup.") else setsell end as cost, deftax from ".$findb.".stkmast where itemid = ".$medicine);
			$row = $db->single();
			extract($row);
			$med = $item;
			$cst = $cost;
	
			switch ($dosage) {
				case '28 Days';
					$mqty = $required;
				break;
				case 'Week';
					$mqty = ceil($required * 4);
				break;
				case 'Day';
					$mqty = $required * 28;
				break;
			}
			
			// get relevant tax	
		
			$db->query("select taxpcent from ".$findb.".taxtypes where uid = ".$deftax);
			$row = $db->single();
			extract($rowt);
				
			// calculate unit/packs required
				
			$unitsreq = ceil($mqty/$noinunit);
			$mcost = ($unitsreq * $cost) * (1 + ($taxpcent / 100));

			
			$db->query("insert into ".$prcdb.".".$rectable." (guid, itemid, item, dosage, qty, cost, monthqty, totcost, entered) values ('".$sessionid."',".$medicine.",'".$med."','".$dosage."',".$required.",".$cst.",".$mqty.",".$mcost.",'".$today."')");
			
/*			
			$db->query("insert into ".$prcdb.".".$rectable." (guid, itemid, item, dosage, qty, cost, monthqty, totcost, entered) values (:guid, :itemid, :item, :dosage, :qty, :cost, :monthqty, :totcost, :entered)");
			$db->bind(':sessionid', $sessionid);
			$db->bind(':medicine', $medicine);					   
			$db->bind(':med', $med);
			$db->bind(':dosage', $dosage);
			$db->bind(':required', $required);					   
			$db->bind(':cst', $cst);
			$db->bind(':mqty', $mqty);
			$db->bind(':mcost', $mcost);					   
			$db->bind(':today', $today);
*/			
			$db->execute();
																															  
		} else {

			$db->query("select itemid,generic,xref from ".$findb.".stkmast where itemid = ".$medicine);
			$row = $db->single();
			extract($row);
			$gen = $generic;
			$xr = $xref;
			
			if ($xref == 0) {
				$db->query("select item, noinunit,case setsell when 0 then (avgcost * ".$markup.") * ".$tax." else (setsell * ".$tax.") end as cost, deftax from ".$findb.".stkmast where itemid = ".$medicine);
				$row = $db->single();
				extract($row);
				$med = $item;
				$cst = $cost;
			
				switch ($dosage) {
					case '28 Days';
						$mqty = $required;
					break;
					case 'Week';
						$mqty = ceil($required * 4);
					break;
					case 'Day';
						$mqty = $required * 28;
					break;
				}
				
				// get relevant tax	
			
				$db->query("select taxpcent from ".$findb.".taxtypes where uid = ".$deftax);
				$rowt = $db->single();
				extract($rowt);
					
				// calculate unit/packs required
					
				$unitsreq = ceil($mqty/$noinunit);
				$mcost = ($unitsreq * $cost) * (1 + ($taxpcent / 100));
				
			$db->query("insert into ".$prcdb.".".$rectable." (guid, itemid, item, dosage, qty, cost, monthqty, totcost, entered) values ('".$sessionid."',".$medicine.",'".$med."','".$dosage."',".$required.",".$cst.",".$mqty.",".$mcost.",'".$today."')");
				
				$db->execute();
			
			} else {

				$db->query("select item, noinunit,case setsell when 0 then avgcost else setsell end as cost, deftax from ".$findb.".stkmast where itemid = ".$xr);
				$row = $db->single();
				extract($row);
				$med = $item;
				$cst = $cost;
			
				switch ($dosage) {
					case '28 Days';
						$mqty = $required;
					break;
					case 'Week';
						$mqty = ceil($required * 4);
					break;
					case 'Day';
						$mqty = $required * 28;
					break;
				}
				
				// get relevant tax	
			
				$qt = "select taxpcent from ".$findb.".taxtypes where uid = ".$deftax;
				$rowt = $db->single();
				extract($rowt);
					
				// calculate unit/packs required
					
				$unitsreq = ceil($mqty/$noinunit);
				$mcost = ($unitsreq * $cost) * (1 + ($taxpcent / 100));
				
			$db->query("insert into ".$prcdb.".".$rectable." (guid, itemid, item, dosage, qty, cost, monthqty, totcost, entered) values ('".$sessionid."',".$medicine.",'".$med."','".$dosage."',".$required.",".$cst.",".$mqty.",".$mcost.",'".$today."')");
				
				$db->execute();
			
			}
		}
		
		$db->closeDB();
				
		?>
		<script>
		window.open("","onlineapp").jQuery("#reqlist").trigger("reloadGrid");
		this.close();
		</script>
		<?php
			
	}

?>


</body>
</html>
