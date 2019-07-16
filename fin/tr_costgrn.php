<?php
session_start();
$usersession = $_SESSION['usersession'];

$id = $_REQUEST['uid'];
$coyno = $_SESSION['s_coyid'];

include_once("../includes/DBClass.php");
$db_trd = new DBClass();

$db_trd->query("select * from sessions where session = :vusersession");
$db_trd->bind(':vusersession', $usersession);
$row = $db_trd->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];

//ini_set('display_errors', true);

$db_trd->query("select ".$findb.".invtrans.uid,".$findb.".invtrans.ref_no,".$findb.".invtrans.currency,".$findb.".invtrans.rate,".$findb.".invhead.ddate,".$findb.".invhead.client,".$findb.".invhead.accountno,".$findb.".invhead.sub,".$findb.".invtrans.itemcode,".$findb.".stkmast.item,".$findb.".invtrans.quantity,".$findb.".invtrans.taxpcent,".$findb.".invtrans.grnlineno,".$findb.".stkmast.purchacc,".$findb.".stkmast.purchsub,invtrans.value from ".$findb.".invtrans,".$findb.".invhead,".$findb.".stkmast where (".$findb.".invhead.ref_no = ".$findb.".invtrans.ref_no) and (".$findb.".stkmast.itemcode = ".$findb.".invtrans.itemcode) and (".$findb.".invtrans.uid = ".$id.")");
$row = $db_trd->single();
extract($row);
$supplierac = $accountno;
$suppliersb = $sub;
$lineno = $grnlineno;
$lcurrency = $currency;
$fcurrency = $currency.'~'.$rate;


// populate forex list
$db_trd->query("select * from ".$findb.".forex");
$rows = $db_trd->resultset();
$forex_options = '<option value="0">Select</option>';
foreach ($rows as $row) {
	extract($row);
	if ($currency == $lcurrency) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$forex_options .= '<option value="'.$row['currency'].'~'.$row['rate'].'"'.$selected.'>'.$row['descript'].'</option>';
}

// set local currency
$db_trd->query("select currency from ".$findb.".forex where def_forex = 'Yes'");
$row = $db_trd->single();
extract($row);
$_SESSION['s_localcurrency'] = $currency;

$db_trd->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Cost uncosted GRN item</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.menu.js"></script>
<script type="text/javascript" src="../includes/jquery/external/jquery.bgiframe-2.1.1.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script>

function post() {

	//add validation here if required.
	var amt = document.getElementById('amount').value;
	
	var ok = "Y";
	if (amt == 0) {
		alert("Please enter an amount.");
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
<div id="mwin">
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
  <input type="hidden" name="fcurrency" id="fcurrency" value="<?php echo $fcurrency; ?>">
 <table width="600" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Cost Uncosted GRN Item </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">GRN</td>
      <td><input type="text" name="refno" id="refno" readonly value="<?php echo $ref_no; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Date</td>
      <td><input type="text" name="date" id="date" readonly value="<?php echo $ddate; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Item Code</td>
      <td><input name="stkcode" type="text" id="stkcode"  size="30" maxlength="30" readonly value="<?php echo $itemcode; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Item Description</td>
      <td><input name="stkname" type="text" id="stkname"  size="60" maxlength="100" readonly value="<?php echo $item; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Supplier</td>
      <td><input type="text" name="client" id="client" size="60" readonly value="<?php echo $client; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Quantity</td>
      <td><input type="text" name="qty" id="qty" readonly value="<?php echo $quantity; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabel" >Currency</td>
      <td ><input type="text" name="currency" id="currency" readonly value="<?php echo $lcurrency; ?>"></td>
    <tr>
      <td class="boxlabel">Total cost excluding tax</td>
      <td><input type="text" name="amount" id="amount" value="<?php echo $value; ?>" onFocus="this.select()"></td>
    </tr>
	  <td colspan="2" align="right"><input type="button" value="Save" name="save"  onClick="post()"  ></td>
	    </tr>
  </table>
  
 
</form>
</div>

<?php
	if($_REQUEST['savebutton'] == "Y") {
		
	  include_once("../includes/DBClass.php");
	  $db = new DBClass();
	  
	  $findb = $_SESSION['s_findb'];
	  $cltdb = $_SESSION['s_cltdb'];
	  
	  $fx = explode('~',$_REQUEST['fcurrency']);
	  $fxcode = $fx[0];
	  $fxrate = $fx[1];
  
	  $amt = $_REQUEST['amount'] / $fxrate;
			
	  $db->query("update ".$findb.".invtrans set tempcost = ".$amt.", currency = '".$fxcode."', rate = ".$fxrate.", value = ".$amt." where uid = ".$id);
	  $db->execute();
	  
	  $db->query("select ref_no from ".$findb.".invtrans where uid = ".$id);
	  $row = $db->single();
	  extract($row);
			
	  $db->query("update ".$findb.".invhead set currency = '".$fxcode."', rate = ".$fxrate." where ref_no = '".$ref_no."'");
	  $db->execute();
			
			
			
			
			
			
			
			
			
			
/*			
			
			
			$gstamt = round($amt*$taxpcent/100,2);
			$amtinc = $amt + $gstamt;
			
			$db_trd->query("select quantity from ".$findb.".invtrans where uid = ".$id);
			$row = $db_trd->single();
			extract($row);
			$pr = round($amt/$quantity,2);
			
			$db_trd->query("update ".$findb.".invtrans set value = ".$amt.", price = ".$pr.", tax = ".$gstamt." where uid = ".$id);
			$db_trd->execute();
			
			$db_trd->query("update ".$findb.".stktrans set amount = ".$amt." where itemcode = '".$itemcode."' and ref_no = '".$ref_no."'");
			$db_trd->execute();
			
			$db_trd->query("update ".$findb.".invhead set totvalue = totvalue + ".$amt.", tax = tax + ".$gstamt." where ref_no = '".$ref_no."'");
			$db_trd->execute();

			// reduce uncosted in stkmast
			$db_trd->query("update ".$findb.".stkmast set uncosted = uncosted - ".$quantity." where itemcode = '".$itemcode."'");
			$db_trd->execute();
			
			// recalculate average cost
			$db_trd->query("select avgcost,onhand-uncosted as tqty from ".$findb.".stkmast where itemcode = '".$itemcode."'");
			$row = $db_trd->single();
			extract($row);
			$newtotval = ($avgcost*$tqty) + $amt;
			$newtotqty = $tqty ;
			$newavgcost = $newtotval/$newtotqty;
			$db_trd->query("update ".$findb.".stkmast set avgcost = ".$newavgcost." where itemcode = '".$itemcode."'");
			$db_trd->execute();
	
			// get gsttype
			$db_trd->query("select gsttype from ".$findb.".globals");
			$row = $db_trd->single();
			extract($row);
	
			// update GRN in trmain.
			
			$db_trd->query("update ".$findb.".trmain set debit = debit + ".$amt." where reference = '".$ref_no."' and accountno = 825");
			$db_trd->execute();

			$db_trd->query("update ".$findb.".trmain set credit = credit + ".$amt." where reference = '".$ref_no."' and accountno = 187");
			$db_trd->execute();

			$db_trd->query("update ".$findb.".trmain set credit = credit + ".$amtinc." where reference = '".$ref_no."' and accountno = ".$supplierac." and sub = ".$suppliersb);
			$db_trd->execute();
			
			$db_trd->query("update ".$findb.".trmain set credit = credit + ".$amtinc." where reference = '".$ref_no."' and accountno = 851");
			$db_trd->execute();
			
			$db_trd->query("update ".$findb.".trmain set debit = debit + ".$amt." where reference = '".$ref_no."' and grnlineno = ".$lineno." and accountno = ".$purchacc." and sub = ".$purchsub);
			$db_trd->execute();
			
			if ($gsttype == 'Invoice') {
				$db_trd->query("update ".$findb.".trmain set debit = debit + ".$gstamt." where reference = '".$ref_no."' and accountno = 870 and grnlineno = ".$lineno);
				$db_trd->execute();
			} else {
				$db_trd->query("update ".$findb.".trmain set debit = debit + ".$gstamt." where reference = '".$ref_no."' and accountno = 871 and grnlineno = ".$lineno);
				$db_trd->execute();
			}
			
			// update balance in client_company_xref
			// work out current,d30,d60,d90,d120
			$today = date('Y-m-d');
			$date1 = new DateTime($ddate);
			$date2 = new DateTime($today);
			$interval = $date1->diff($date2);
			$days = $interval->days;
				
			if ($days < 31) {
				$aged = 'Current';
			}
			if ($days > 30 && $days < 61) {
				$aged = 'D30';
			}
			if ($days > 60 && $days < 91) {
				$aged = 'D60';
			}
			if ($days > 90) {
				$aged = 'D120';
			}
			
			if ($aged == 'Current') {
				$db_trd->query("update ".$cltdb.".client_company_xref set current = current - ".$amtinc." where company_id = ".$coyno." and crno = ".$supplierac." and crsub = ".$suppliersb);
			}
			if ($aged == 'D30') {
				$db_trd->query("update ".$cltdb.".client_company_xref set d30 = d30 - ".$amtinc." where company_id = ".$coyno." and crno = ".$supplierac." and crsub = ".$suppliersb);
			}
			if ($aged == 'D60') {
				$db_trd->query("update ".$cltdb.".client_company_xref set d60 = d60 - ".$amtinc." where company_id = ".$coyno." and crno = ".$supplierac." and crsub = ".$suppliersb);
			}
			if ($aged == 'D90') {
				$db_trd->query("update ".$cltdb.".client_company_xref set d90 = d90 - ".$amtinc." where company_id = ".$coyno." and crno = ".$supplierac." and crsub = ".$suppliersb);
			}
			if ($aged == 'D120') {
				$db_trd->query("update ".$cltdb.".client_company_xref set d120 = d120 - ".$amtinc." where company_id = ".$coyno." and crno = ".$supplierac." and crsub = ".$suppliersb);
			}
			
			$db_trd->execute();		
*/			
			$db_trd->closeDB();
			
			?>
				<script>
				window.open("","uncostgrn").jQuery("#UncostGRNitems").trigger("reloadGrid");
				this.close()
				</script>
			<?php
	
	}
?>
 

</body>
</html>

