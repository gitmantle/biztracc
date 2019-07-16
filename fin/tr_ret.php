<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

$_SESSION['s_tradingreturn'] = 'ret';

$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db_trd = new DBClass();

$db_trd->query("select * from sessions where session = :vusersession");
$db_trd->bind(':vusersession', $usersession);
$row = $db_trd->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$tradetable = 'ztmp'.$user_id.'_trading';
$serialtable = 'ztmp'.$user_id.'_serialnos';

$findb = $_SESSION['s_findb'];

$sql = "drop table if exists ".$findb.".".$tradetable;
$db_trd->query($sql);
$db_trd->execute();
$sql = "drop table if exists ".$findb.".".$serialtable;
$db_trd->query($sql);
$db_trd->execute();

$db_trd->query("create table ".$findb.".".$tradetable." (uid int(11) default 0,itemcode varchar(30) default '',item varchar(100) default '',price decimal(16,2) default 0, unit varchar(20) default '',quantity decimal(16,2) default 0,tax decimal(16,2) default 0, value decimal(16,2) default 0, discount decimal(16,2) default 0, disctype char(1) default '', discamount decimal(16,2) default 0, tot decimal(16,2) default 0, taxindex int(11) default 0, taxtype char(3) default '', taxpcent decimal(5,2) default 0, sellacc int(11) default 0, sellbr char(4) default '', sellsub int(4) default 0, purchacc int(11) default 0, purchbr char(4) default '', purchsub int(4) default 0, groupid int(11) default 0, catid int(11) default 0, trackserial char(3) default 'No', loc int(11) default 0, pay char(1) default 'Y',ref char(15) default '', stock char(3) default '', avcost decimal(16,2) default 0,origqty decimal(16,2) default 0, forex decimal(16,2) default 0, currency char(3) default '', rate decimal(7,3) default 1, your_ref varchar(30) default '' )  engine myisam");
$db_trd->execute();

$db_trd->query("create table ".$findb.".".$serialtable." ( uid int(11) primary key, itemcode varchar(30) default '',item varchar(100) default '',serialno varchar(50) default '', locationid int(11) default 0, location varchar(30) default '', ref_no varchar(15) default '', selected char(1) default 'N')  engine myisam");
$db_trd->execute();

$coyid = $_SESSION['s_coyid'];
$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$country = $_SESSION['country'];

$db_trd->query('select * from '.$findb.'.branch order by branchname');
$rows = $db_trd->resultset();
// populate branches list
$branch_options = "<option value=\"\">Select Branch</option>";
foreach ($rows as $row) {
	extract($row);
	$branch_options .= "<option value=\"".$branch."\">".$branchname."</option>";
}

$db_trd->query('select * from '.$findb.'.stklocs order by location');
$rows = $db_trd->resultset();
// populate location list
$loc_options = "<option value=\"\">Select Location</option>";
foreach ($rows as $row) {
	extract($row);
	$loc_options .= "<option value=\"".$uid."\">".$location."</option>";
}

$db_trd->query("select gsttype as gstinvpay from ".$findb.".globals");
$row = $db_trd->single();
extract($row);

// populate Tax type list
$db_trd->query("select * from ".$findb.".taxtypes");
$rows = $db_trd->resultset();
$tax_options = "<option value=\"\">Select Tax Type</option>";
foreach ($rows as $row) {
	extract($row);
	$tax_options .= "<option value=\"".$taxpcent."#".$tax."\">".$tax.' - '.$description."</option>";
}

// populate forex list
$db_trd->query("select * from ".$findb.".forex");
$rows = $db_trd->resultset();
$forex_options = '<option value="0">Select</option>';
foreach ($rows as $row) {
	extract($row);
	if ($row['def_forex'] == 'Yes') {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$forex_options .= '<option value="'.$row['currency'].'~'.$row['rate'].'"'.$selected.'>'.$row['descript'].'</option>';
}

// get accounts to debit dependant on payment method.
$db_trd->query("select cashacc,cashsb,trdbankacc,trdbanksub,credcardacc,credcardsub from ".$findb.".globals");
$row = $db_trd->single();
extract($row);
$cshac = $cashacc;
$cshsb = $cashsb;
$chqac = $cashacc;
$chqsb = $cashsb;
$eftac = $trdbankacc;
$eftsb = $trdbanksub;
$crdac = $credcardacc;
$crdsb = $credcardsub;

$db_trd->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Goods Returned</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script type="text/javascript" src="includes/ajaxgetref.js"></script>
<script type="text/javascript" src="includes/ajaxCheckTransDate.js"></script>
<script>

	window.name = "tr_ret";

</script>
</head>
<body>
<form name="crn" id="crn" method="post" >
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <input type="hidden" name="paytype" id="paytype" value="">
  <input type="hidden" name="purchref" id="purchref" value="">
 <input type="hidden" name="sacc" id="sacc" value="">
  <input type="hidden" name="pacc" id="pacc" value="">
  <input type="hidden" name="grp" id="grp" value="0">
  <input type="hidden" name="cat" id="cat" value="0">
  <input type="hidden" name="trading" id="trading" value="ret">
  <input type="hidden" name="clientid" id="clientid" value="0">
  <input type="hidden" name="priceband" id="priceband" value="0">
  <input type="hidden" name="setsell" id="setsell" value="0">
  <input type="hidden" name="stock" id="stock" value="">
  <input type="hidden" name="avcost" id="avcost" value="">
  <input type="hidden" name="trackserial" id="trackserial" value="No">
  <input type="hidden" name="noselected" id="noselected" value="0">
  <input type="hidden" name="loc" id="loc" value="0">
  <input type="hidden" name="TRaccount" id="TRaccount" value="0">
  <input type="hidden" name="gstinvpay" id="gstinvpay" value=<?php echo $gstinvpay; ?>>
  <input type="hidden" name="cshac" id="cshac" value="<?php echo $cshac; ?>">
  <input type="hidden" name="cshsb" id="cshsb" value="<?php echo $cshsb; ?>">
  <input type="hidden" name="chqac" id="chqac" value="<?php echo $chqac; ?>">
  <input type="hidden" name="chqsb" id="chqsb" value="<?php echo $chqsb; ?>">
  <input type="hidden" name="eftac" id="eftac" value="<?php echo $eftac; ?>">
  <input type="hidden" name="eftsb" id="eftsb" value="<?php echo $eftsb; ?>">
  <input type="hidden" name="crdac" id="crdac" value="<?php echo $crdac; ?>">
  <input type="hidden" name="crdsb" id="crdsb" value="<?php echo $crdsb; ?>">
  <table  width="960" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="7"><label style="color: <?php echo $thfont; ?>"><strong>Goods Returned </strong></label></td>
    </tr>
    <tr>
      <td  class="boxlabel" >Date</td>
      <td ><div align="left">
          <input type="Text" id="newdate" name="newdate" maxlength="25" size="25" value="<?php echo $ddateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y">
        </div></td>
      <td  class="boxlabel">Description</td>
      <td ><div align="left">
          <input name="description" id="description" type="text" onFocus="this.select()" size="50" maxlength="60">
        </div></td>
      <td class="boxlabel">Reference</td>
      <td ><input name="ref" id="ref" type="text" size="5" value="RET" readonly></td>
      <td><input name="refno" type="text" id="refno" size="10" value="0" readonly onFocus="nexttrdref('ret','N')"></td>	
    </tr>
    <tr>
      <td class="boxlabel" >&nbsp;</td>
      <td class="boxlabel" >Currency</td>
      <td ><select name="currency" id="currency"><?php echo $forex_options;?></select></td>
      <td  class="boxlabel">Purchasing document (GRN or C_P) reference</td>
      <td align="right"><input type="text" name="purchreference" id="purchreference"></td>
      <td  colspan="2" align="right"><input type="button" value="Find Details" name="save"  onClick="finddetails('ret')" ></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="2"><label style="color: <?php echo $thfont; ?>"><strong>Details </strong></label></td>
      <td>&nbsp;</td>
      <td colspan="2" align="right"><input type="button" name="bselectall" id="bselectall" value="Select all for refund" onclick="refundselectall()"></td>
      <td colspan="2" align="right"><input type="button" name="bdeselect" id="bdeselect" value="De-select all" onclick="refunddeselectall()"></td>
    </tr>
    <tr>
      <td colspan="7"><?php include "getpurchdetails.php"; ?></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td><img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
      <td>&nbsp;</td>
      <td colspan="5" class="boxlabel"><input name="Submit" id="Submit" type="button" value="Post" onClick="getpaymethod()"></td>
    </tr>
    
  </table>

  
  <div id="drselect" style="position:absolute;visibility:hidden;top:98px;left:342px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
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
 
  <div id="stkselect" style="position:absolute;visibility:hidden;top:156px;left:80px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchstk" size="50" onkeypress="doSearchstk()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="stkclose" onclick="sboxhidestk()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectstk.php"; ?></td>
      </tr>
    </table>
  </div>
  
	<div id="printpage" style="position:absolute;visibility:hidden;top:200px;left:400px;height:80px;width:250px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bghead; ?>; border-style:outset;">
	<table width="250">
    <tr bgcolor="<?php echo $bghead; ?>">
    <td colspan="3" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Print Goods Returned Note</strong></label></td>
    <tr>
      <td><input type="button" name="cancel" id="cancel" value="Close" onClick="hideprint('ret')"></td>
      <td align="center"><input type="button" name="email" id="email" value="Email" onClick="emailtrading('ret')"></td>
      <td align="right"><input type="button" name="print" id="print" value="Print" onClick="printtrading('ret')"></td>
    </tr>
    </table>
  
	</div>  

	<div id="paymethod" style="position:absolute;visibility:hidden;top:1px;left:200px;height:150px;width:150px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bghead; ?>; border-style:outset;">
    <table width="145">
    	<tr bgcolor="<?php echo $bghead; ?>">
        	<td>Payment method</td>
    	</tr>
    	<tr>
        	<td><p>
        	  <label>
        	    <input type="radio" name="paymethod" value="eftpos" id="eft">
        	    EFTPOS</label>
        	  <br>
        	  <label>
        	    <input type="radio" name="paymethod" value="creditcard" id="crd">
        	    Credit Card</label>
        	  <br>
        	  <label>
        	    <input type="radio" name="paymethod" value="cash" id="csh">
        	    Cash</label>
        	  <br>
        	  <label>
        	    <input type="radio" name="paymethod" value="cheque" id="chq">
        	    Cheque</label>
        	  <br>
      	  </p></td>
    	</tr>
        <tr>
      		<td align="right"><input type="button" name="pmeth" id="pmeth" value="OK" onClick="postflow('ret')"></td>
        </tr>
    </table>
    </div>


   <div id="sellserial" style="position:absolute;visibility:hidden;top:200px;left:400px;height:280px;width:320px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bghead; ?>; border-style:outset;">
	<table width="310">
    <tr>
        <td colspan="2"><?php include "selectpurchserials.php"; ?></td>
    </tr>
    <tr>
      <td><input type="button" name="bcancel" id="bcancel" value="Cancel" onClick="sellserialclose()"></td>
      <td align="right"><input type="button" name="bserial" id="bserial" value="Save" onClick="addpurchserialnos()"></td>
    </tr>
    </table>
  
	</div>  

 <script>
 	document.getElementById("newdate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
			ajaxCheckTransDate();	
		}
	});
 </script>
   
</form>
</body>
</html>
