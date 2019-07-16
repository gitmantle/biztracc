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


$db_trd->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Charge</title>
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

function transvisiblecr() {
		var source = 'grn';
		$.get("../ajax/ajaxUpdtSource.php", {source: source}, function(data){
		});
		document.getElementById('crselect').style.visibility = 'visible';											
		document.getElementById('searchcr').value = "";
		document.getElementById('searchcr').focus();
}

function gridReload1cr(){ 
	var cr_mask = jQuery("#searchcr").val(); 
	cr_mask = cr_mask.toUpperCase();
	jQuery("#selectcrlist").setGridParam({url:"selectcr.php?cr_mask="+cr_mask}).trigger("reloadGrid"); 
} 

function doSearchcr(){ 
		var timeoutHnd = setTimeout(gridReload1cr,500); 
	} 

function sboxhidecr() {
	document.getElementById('crselect').style.visibility = 'hidden';											
}

function settrdselect(acc,ledger) {
	var trdtype = 'grn';
	tradingtype = trdtype;
	var a = acc.split('~');
	var ac = a[0];
	var br = a[1];
	var sb = a[2];
	var acname = a[3];
	var cid = a[4];
	priceband = a[5];
	var prefname = a[6];
	memberid = cid;
	

	var acc = acname+'~'+ac+'~'+sb+'~'+br;
	if (ledger == 'dr') {
	 	document.getElementById('TRaccount').value = acc;
		document.getElementById('drselect').style.visibility = 'hidden';
		document.getElementById('TRaccount').style.visibility = 'visible';
	}
	if (ledger == 'cr') {
	 	document.getElementById('TRaccount').value = acc;
		document.getElementById('crselect').style.visibility = 'hidden';
		document.getElementById('TRaccount').style.visibility = 'visible';
	}

}

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
  <table width="580" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Add Charge to GRN </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Supplier</td>
      <td><input type="text" name="TRaccount" id="TRaccount" size="45" readonly >
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="crsearch" onclick="transvisiblecr()"></td>
    </tr>
    <tr>
      <td class="boxlabel">Charge Description</td>
      <td><input name="descript" type="text" id="descript"  size="45" maxlength="50" ></td>
    </tr>
    <tr>
      <td class="boxlabel">Charge excluding GST</td>
      <td><input type="text" name="amount" id="amount" value="0" onFocus="this.select()"></td>
    </tr>
	  <td colspan="2" align="right"><input type="button" value="Save" name="save"  onClick="post()"  ></td>
	    </tr>
  </table>
  
   <div id="crselect" style="position:absolute;visibility:hidden;top:30px;left:30px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchcr" size="50" onkeypress="doSearchcr()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="crclose" onclick="sboxhidecr()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selecttrdcr.php"; ?></td>
      </tr>
    </table>
  </div>
  
</form>
</div>

<?php
	if($_REQUEST['savebutton'] == "Y") {
		
			include_once("../includes/DBClass.php");
			$db_trd = new DBClass();
			
			$findb = $_SESSION['s_findb'];
			$cltdb = $_SESSION['s_cltdb'];
		
			$amt = $_REQUEST['amount'];
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
			
			$db_trd->closeDB();
			
			?>
				<script>
				window.open("","costs").jQuery("#uncostlist").trigger("reloadGrid");
				this.close()
				</script>
			<?php
	
	}
?>
 

</body>
</html>

