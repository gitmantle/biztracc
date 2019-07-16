<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);
$induid = $_REQUEST['uid'];

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$cluid = $_SESSION['s_memberid'];

$moduledb = $_SESSION['s_prcdb'];
mysql_select_db($moduledb) or die(mysql_error());


$q = "select * from requirements where req_id = ".$induid;
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract ($row);
$med = $medicineid;
$dos = $dosage;
$quant = $qty;
$ed = explode('-',$expdate);
$d = $ed[2];
$m = $ed[1];
$y = $ed[0];
$edate = $d.'/'.$m.'/'.$y;

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());


// populate medicine drop down
$query = "select itemid,item from stkmast";
$result = mysql_query($query) or die(mysql_error());
$medicine_options = "<option value=\"0\">Select Medicine</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($itemid == $med) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$medicine_options .= '<option value="'.$itemid.'"'.$selected.'>'.$item.'</option>';
}
	

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Medicine</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>

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
      <td colspan="3"><div align="center" class="style1"><u>Edit Medicine </u></div></td>
    </tr>
    <tr>
      <td width="106" class="boxlabel">Medicine</td>
      <td colspan="2"><select name="med" id="med">
            <?php echo $medicine_options;?>
          </select></td>
      </tr>
    <tr>
      <td class="boxlabel">Quantity</td>
      <td colspan="2"><input type="text" name="req" id="req" value="<?php echo $quant; ?>"></td>
    </tr>
    <tr>
      <td rowspan="2" class="boxlabel">Per</td>
      <td rowspan="2"><p>
        <label>
          <input type="radio" name="dosage" value="Day" id="Day">
          Day</label>
        <br>
        <label>
          <input type="radio" name="dosage" value="Week" id="Week">
          Week</label>
        <br>
        <label>
          <input type="radio" name="dosage" value="28 Days" id="Month">
          28 Days</label>
        <br>
      </p></td>
      <td>Prescription expires on</td>
    </tr>
    <tr>
      <td><input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edate; ?>" onChange="ajaxCheckTransDate();"><a href="javascript:NewCal('edate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
    </tr>
    <tr>
      <td class="boxlabel">Instructions</td>
      <td colspan="2"><textarea name="tinst" id="tinst" cols="45" rows="5"><?php echo $instructions; ?></textarea></td>
    </tr>
	<tr>
      <td>&nbsp;</td>
	  <td colspan="2" align="right"> <input type="button" value="Save" name="save" onClick="post()"  ></td>
      </tr>
  </table>
  
<script>
	document.getElementById('<?php echo $dos; ?>').checked = true;
</script> 
  
</form>
</div>

<?php

	if($_REQUEST['savebutton'] == "Y") {
		
		$ed = explode('/',$_REQUEST['edate']);
		$d = $ed[0];
		$m = $ed[1];
		$y = $ed[2];
		$edt = $y.'-'.$m.'-'.$d;
		
		$medicine = $_REQUEST['med'];
		$required = $_REQUEST['req'];

		$moduledb = $_SESSION['s_findb'];
		mysql_select_db($moduledb) or die(mysql_error());

		
		$q = "select pcent from stkpricepcent where uid = 1";
		$r = mysql_query($q) or die(mysql_error());
		$row = mysql_fetch_array($r);
		extract($row);	
		$spcent = $pcent;
		
		$markup = 1 + $spcent/100;
				
		$query = "select xref from stkmast where itemid = ".$medicine;
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		extract($row);
		$xr = $xref;
			
		if ($xref > 0) {
			$medicine = $xr;
		}
		
		$query = "select item, noinunit from stkmast where itemid = ".$medicine;
		$result = mysql_query($query) or die(mysql_error().' '.$query);
		$row = mysql_fetch_array($result);
		extract($row);
		$med = $item;
		
		$dosage = $_REQUEST['dosage'];
	
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
				
		// calculate unit/packs required
		$unitsreq = ceil($mqty/$noinunit);
		
		$moduledb = $_SESSION['s_prcdb'];;
		mysql_select_db($moduledb) or die(mysql_error());
		
		$sSQLString = "update requirements set ";
		$sSQLString .= 'medicineid = '.$_REQUEST['med'].',';
		$sSQLString .= 'dosage = "'.$_REQUEST['dosage'].'",';
		$sSQLString .= 'expdate = "'.$edt.'",';
		$sSQLString .= 'instructions = "'.$_REQUEST['tinst'].'",';
		$sSQLString .= 'qty = '.$_REQUEST['req'].',';
		$sSQLString .= 'periodqty = '.$unitsreq;
		$sSQLString .= ' where req_id = '.$induid;
		
		$result = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
	
		?>
		<script>
		window.open("","editmembers").jQuery("#reqlist").trigger("reloadGrid");
		this.close();
		</script>
		<?php
		
			
	}

?>



</body>
</html>
