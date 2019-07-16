<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

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

$table = 'ztmp'.$user_id.'_trans';

$findb = $_SESSION['s_findb'];

$sql = "drop table if exists ".$findb.".".$table;
$db_trd->query($sql);
$db_trd->execute();

$sql = "create table ".$findb.".".$table." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, acc2dr int(11) default 0,subdr int(11) default 0,brdr char(4) default '',drindex int(10) default 0,acc2cr int(11) default 0,subcr int(11) default 0,brcr char(4)default '',crindex int(10) default 0,ddate date default '0000-00-00',descript1 varchar(60),reference char(9) default '',refindex int(10) default 0,amount double(16,2) default 0,depdr int(11),depbrdr char(4),depcr int(11),depbrcr char(4),nallocate int(11),tax double(16,2),taxtype char(3),taxpcent double(5,2),applytax char(1),total double(16,2) default 0, done int(11) default 0,type char(1),grn char(10),inv char(10),a2d varchar(45),a2c varchar(45),taxindex int(10),drgst char(1) default 'N', crgst char(1) default 'N', currency char(3) default '', rate decimal(7,3) default 1, your_ref varchar(30) default '')  engine myisam";
$db_trd->query($sql);
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

// set local currency
$db_trd->query("select currency from ".$findb.".forex where def_forex = 'Yes'");
$row = $db_trd->single();
extract($row);
$_SESSION['s_localcurrency'] = $currency;

$_SESSION['s_drac'] = 0;
$_SESSION['s_drsb'] = 0;

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

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


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Receipts</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script type="text/javascript" src="includes/ajaxgetref.js"></script>
<script type="text/javascript" src="includes/ajaxCheckTransDate.js"></script>
<script>

	window.name = "tr_rec";
</script>
</head>
<body>
<form name="inv" id="inv" method="post" >
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <input type="hidden" name="paytype" id="paytype" value="">
  <input type="hidden" name="trading" id="trading" value="rec">
  <input type="hidden" name="clientid" id="clientid" value="0">
  <input type="hidden" name="cshac" id="cshac" value="<?php echo $cshac; ?>">
  <input type="hidden" name="cshsb" id="cshsb" value="<?php echo $cshsb; ?>">
  <input type="hidden" name="chqac" id="chqac" value="<?php echo $chqac; ?>">
  <input type="hidden" name="chqsb" id="chqsb" value="<?php echo $chqsb; ?>">
  <input type="hidden" name="eftac" id="eftac" value="<?php echo $eftac; ?>">
  <input type="hidden" name="eftsb" id="eftsb" value="<?php echo $eftsb; ?>">
  <input type="hidden" name="crdac" id="crdac" value="<?php echo $crdac; ?>">
  <input type="hidden" name="crdsb" id="crdsb" value="<?php echo $crdsb; ?>">
  <input type="hidden" name="partpay" id="partpay" value="0">
  <input type="hidden" name="lid" id="lid" value="0">
  <input type="hidden" name="lref" id="lref" value="">
  <input type="hidden" name="gstinvpay" id="gstinvpay" value=<?php echo $gstinvpay; ?>>
  <table  width="960" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="10"><label style="color: <?php echo $thfont; ?>"><strong>Receipt </strong></label></td>
    </tr>
    <tr>
      <td width="77" class="boxlabel" >Date</td>
      <td width="147"><div align="left">
          <input type="Text" id="newdate" name="newdate" maxlength="25" size="25" value="<?php echo $ddateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y">
        </div></td>
      <td width="82" class="boxlabel">Description</td>
      <td colspan="3" ><div align="left">
          <input name="description" id="description" type="text" onFocus="this.select()" size="40" maxlength="60">
        </div></td>
      <td width="120"  class="boxlabel">Reference</td>
      <td width="72"><input name="ref" id="ref" type="text" size="5" value="REC" readonly></td>
      <td>&nbsp;</td>	
      <td colspan="2"><input name="refno" type="text" id="refno" size="10" value="0" readonly onFocus="nexttrdref('rec','N')"></td>	
    </tr>
    <tr>
      <td class="boxlabel" >Debtor</td>
      <td colspan="3" class="boxlabelleft" ><input type="text" name="TRaccount" id="TRaccount" size="35" readonly ><img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onclick="transvisibledr()"></td>
      <td colspan="6" class="boxlabel" ><label> Paid by
        <input type="radio" name="paymethod" value="eft" id="eft">
        Eftpos/Direct</label>
        <label>
          <input type="radio" name="paymethod" value="crd" id="crd">
          Credit Card</label>
        <label>
          <input type="radio" name="paymethod" value="csh" id="csh">
          Cash</label>
        <label>
          <input type="radio" name="paymethod" value="chq" id="chq">
      Cheque</label></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td class="boxlabel">Currency</td>
      <td><select name="currency" id="currency" onchange="setfx();"><?php echo $forex_options;?></select></td>
      <td class="boxlabel"><input type="text" name="localcode" id="localcode" size="7" value="Received" readonly></td>
      <td colspan="2" class="boxlabel"><input type="text" name="price" id="price" value="0" onFocus="this.select()" onChange="calcPriceForex();"></td>
      <?php
		if ($gstinvpay == 'Payment') {
		  echo '<td id="taxlabel" class="boxlabel" >Tax</td>';
		  echo '<td id="taxbox" colspan="2" ><select name="taxtype" id="taxtype">'.$tax_options.'</select></td>';
      	} else {
		  echo '<td>&nbsp;</td>';	
		  echo '<td>&nbsp;</td>';	
		}
      ?> 
        <td class="boxlabel">To Branch</td>
        <td  colspan="2"><select name="branch" id="branch">
          <?php echo $branch_options; ?>
        </select></td>
        <td colspan="2" align="right"><input name="Submit" id="bpostrec" type="button" value="Post" onClick="postrec()"></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="2">&nbsp;</td>	
      <td class="boxlabel"><input type="text" name="fxcode" id="fxcode" size="5" value = "" readonly></td>
      <td colspan="2" class="boxlabel"><input type="text" name="fxamt" id="fxamt" size="10" value = "" onFocus="this.select()" onChange="calcLocalForex();"></td>
      <td colspan="6"></td>
    </tr>
  </table>
  
  <div id="allocation" style="visibility:hidden;" >
  <table  width="960" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr >
      <td>&nbsp;</td>
      <td><input type="button" name="ballocate" id="ballocate" value="Allocate against Sales" onClick="allocate()"></td>
      <td class="boxlabel" >Unallocated</td>
      <td><input type="text" name="unallocated" id="unallocated" readonly></td>
      </tr>
    <tr>
      <td colspan="4"><?php include "getoutstandinginvs.php"; ?></td>
    </tr>
    <tr>
      <td colspan="4"><?php include "getinvlines.php"; ?></td>
    </tr>
  
  </table>
  </div>

  
  <div id="drselect" style="position:absolute;visibility:hidden;top:98px;left:342px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchdr" size="50" onkeypress="doSearchdr()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="crclose" onclick="sboxhidedr()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectdrallocate.php"; ?></td>
      </tr>
    </table>
  </div>
 
  <div id="printpage" style="position:absolute;visibility:hidden;top:200px;left:400px;height:75px;width:250px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bghead; ?>; border-style:outset;">
	<table width="250">
    <tr bgcolor="<?php echo $bghead; ?>">
    <td colspan="3" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Print Receipt</strong></label></td>
    </tr>
    <tr>
      <td><input type="button" name="cancel" id="cancel" value="Close" onClick="hideprint()"></td>
      <td align="center"><input type="button" name="email" id="email" value="Email" onClick="emailrec('REC')"></td>
      <td align="right"><input type="button" name="print" id="print" value="Print" onClick="printrec('REC')"></td>
    </tr>
    </table>
  
	</div>  

  <div id="paypart" style="position:absolute;visibility:hidden;top:400px;left:500px;height:75px;width:250px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bghead; ?>; border-style:outset;">
	<table width="250">
    <tr bgcolor="<?php echo $bghead; ?>">
    <td colspan="3" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Part Payment</strong></label></td>
    <tr>
      <td>Amount<input type="text" name="partamount" id="partamount" value="0" onFocus="this.select()"></td>
      <td align="right"><input type="button" name="pamount" id="pamount" value="Save" onClick="partpayment()"></td>
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
