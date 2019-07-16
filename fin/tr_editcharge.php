<?php
session_start();
$usersession = $_SESSION['usersession'];

$id = $_REQUEST['uid'];
$coyno = $_SESSION['s_coyid'];

$findb = $_SESSION['s_findb'];


include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$chargefile = 'ztmp'.$user_id.'_charges';

$db->query("select * from ".$findb.".".$chargefile." where uid = ".$id);
$row = $db->single();
extract($row);
$txtype = $taxtype;
$cosac = $cosacno;
$cossb = $cossub;
$fxcode = $currency;
$cdescript = $descript;

$db_trd->query("select gsttype as gstinvpay, gstperiod from ".$findb.".globals");
$row = $db_trd->single();
extract($row);

// populate Tax type list
$db->query("select * from ".$findb.".taxtypes");
$rows = $db->resultset();
$tax_options = "<option value=\"\">Select Tax Type</option>";
foreach ($rows as $row) {
	extract($row);
	if ($tax == $txtype) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$tax_options .= '<option value="'.$taxpcent.'#'.$tax.'"'.$selected.'>'.$tax.' - '.$description.'</option>';
}

//populate COS accounts list
$db->query("select account,accountno,sub from ".$findb.".glmast where ctrlacc = 'N' and blocked = 'N' and accountno >= 101 and accountno <= 200");
$rows = $db->resultset();
$cos_options = "<option value=\"\">Select COS Account</option>";
foreach ($rows as $row) {
	extract($row);
	if ($accountno == $cosac && $sub == $cossub) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$cos_options .= '<option value="'.$accountno.'~'.$sub.'"'.$selected.'>'.$account.'</option>';
}

// populate forex list
$db->query("select * from ".$findb.".forex");
$rows = $db->resultset();
$forex_options = '<option value="0">Select</option>';
foreach ($rows as $row) {
	extract($row);
	if ($currency == $fxcode) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$forex_options .= '<option value="'.$currency.'~'.$rate.'"'.$selected.'>'.$descript.'</option>';
}


$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Charge</title>
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
  <input type="hidden" name="gstnt" id="gstnt" value="">
  <table width="580" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Edit Charge for GRN </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Supplier</td>
      <td><input type="text" name="TRaccount" id="TRaccount" size="45" value="<?php echo $supplier; ?>" readonly >
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="crsearch" onclick="transvisiblecr()"></td>
    </tr>
    <tr>
      <td class="boxlabel">Charge Description</td>
      <td><input name="descript" type="text" id="descript"  size="45" maxlength="50" value="<?php echo $cdescript; ?>" ></td>
    </tr>
    <tr>
      <td class="boxlabel" >Currency</td>
      <td ><select name="currency" id="currency" ><?php echo $forex_options;?></select></td>
    <tr>
    <tr>
      <td class="boxlabel">Charge excluding tax</td>
      <td><input type="text" name="amount" id="amount" value="<?php echo $charge; ?>" onFocus="this.select()"></td>
    </tr>
    <tr>
      <td class="boxlabel"> <?php echo $_SESSION['s_tradtax']; ?> rate</td>
       <td><select name="tax" id="tax"><?php echo $tax_options; ?> </select></td>  
    </tr>
    <tr>
      <td class="boxlabel">Debit Account</td>
       <td><select name="cos" id="cos"><?php echo $cos_options; ?> </select></td>  
    </tr>
    <tr>
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
  
     <?php 
		if ($gstperiod == 'Not Registered') {
			echo '<script>';
			echo "document.getElementById('tax').style.visibility = 'hidden';";
			echo "document.getElementById('gstnt').value = 'N_T~0'";
			echo '</script>';
		}
	?> 
  
  
</form>
</div>

<?php
	if($_REQUEST['savebutton'] == "Y") {
		
	  include_once("../includes/DBClass.php");
	  $db = new DBClass();
	  
	  $db->query("select * from sessions where session = :vusersession");
	  $db->bind(':vusersession', $usersession);
	  $row = $db->single();
	  $subid = $row['subid'];
	  $user_id = $row['user_id'];
	  
	  $chargefile = 'ztmp'.$user_id.'_charges';
	  
	  $findb = $_SESSION['s_findb'];
	  $cltdb = $_SESSION['s_cltdb'];
  
	  $amt = $_REQUEST['amount'];
	  $descript = $_REQUEST['descript'];
	  $ac = explode('~',$_REQUEST['TRaccount']);
	  $supplier = $ac[0];
	  $acc = $ac[1];
	  $sub = $ac[2];
	  $tx = explode('#',$_REQUEST['tax']);
	  $tax = $tx[0];
	  $taxtype = $tx[1];
	  $cs = explode('~',$_REQUEST['cos']);
	  $cosacno = $cs[0];
	  $cossub = $cs[1];
	  
	  $fx = explode('~',$_REQUEST['currency']);
	  $fxcode = $fx[0];
	  $fxrate = $fx[1];
  
	  
	  $db->query("update ".$findb.".".$chargefile." set supplier = :supplier,acno = :acno,sbno = :sbno,descript = :descript,charge = :charge,taxpcent = :taxpcent,taxtype = :taxtype,cosacno = :cosacno,cossub = :cossub,currency = :currency,rate = :rate where uid = :uid");
	  $db->bind(':supplier', $supplier);
	  $db->bind(':acno', $acc);
	  $db->bind(':sbno', $sub);
	  $db->bind(':descript', $descript);
	  $db->bind(':charge', $amt);
	  $db->bind(':taxpcent', $tax);
	  $db->bind(':taxtype', $taxtype);
	  $db->bind(':cosacno', $cosacno);
	  $db->bind(':cossub', $cossub);
	  $db->bind(':currency', $fxcode);
	  $db->bind(':rate', $fxrate);
	  $db->bind(':uid', $id);
	  $db->execute();
			
		
	  $db->closeDB();	
			
			
	  ?>
		  <script>
		  window.open("","uncostgrn").jQuery("#chargelist").trigger("reloadGrid");
		  this.close()
		  </script>
	  <?php
	
	}
?>
 

</body>
</html>

