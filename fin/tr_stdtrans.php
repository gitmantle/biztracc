<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

$_SESSION['s_select'] = '~~';

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

$sql = "create table ".$findb.".".$table." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, acc2dr int(11) default 0,subdr int(11) default 0,brdr char(4) default '',drindex int(10) default 0,acc2cr int(11) default 0,subcr int(11) default 0,brcr char(4)default '',crindex int(10) default 0,ddate date default '0000-00-00',descript1 varchar(60),reference char(9) default '',refindex int(10) default 0,amount double(16,2) default 0,depdr int(11),depbrdr char(4),depcr int(11),depbrcr char(4),nallocate int(11),tax double(16,2),taxtype char(3),taxpcent double(5,2),applytax char(1),total double(16,2) default 0, done int(11) default 0,type char(1),grn char(10),inv char(10),currency char(3) default '', rate double(7,3) default 1,a2d varchar(45),a2c varchar(45),taxindex int(10),drgst char(1) default 'N', crgst char(1) default 'N',your_ref varchar(30) default '')  engine myisam";
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

$db_trd->query("select gsttype as gstinvpay, gstperiod from ".$findb.".globals");
$row = $db_trd->single();
extract($row);


//populate list of assets
$db_trd->query('select hcode,accountno,branch,asset from '.$findb.'.fixassets order by hcode, asset');
$rows = $db_trd->resultset();
$asset_options = "<option value=\"\">Against which asset</option>";
foreach ($rows as $row) {
	extract($row);
	$asset_options .= "<option value=\"".$accountno."#".$branch."\">".$asset."</option>";
}

// populate Tax type list
$db_trd->query("select * from ".$findb.".taxtypes");
$rows = $db_trd->resultset();
$tax_options = "<option value=\"\">Select Tax Type</option>";
foreach ($rows as $row) {
	extract($row);
	$tax_options .= "<option value=\"".$taxpcent."#".$tax."\">".$tax.' - '.$description."</option>";
}

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

$db_trd->closeDB();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Standard Transactions</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script type="text/javascript" src="includes/ajaxgetref.js"></script>
<script type="text/javascript" src="includes/ajaxCheckTransDate.js"></script>
<script>

	window.name = "tr_stdtrans";


</script>
<style type="text/css">
<!--
.style2 {
	font-size: small
}
.style3 {
	font-size: x-small
}
-->
</style>
</head>
<body>
<form name="stdtrans" id="stdtrans" method="post" >
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <input type="hidden" name="transtype" id="transtype" value="std">
  <input type="hidden" name="gstinvpay" id="gstinvpay" value=<?php echo $gstinvpay; ?>>
  <input type="hidden" name="gstnt" id="gstnt" value="">
 <table width="950" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td><label style="color: <?php echo $thfont; ?>"><strong>New Transaction Line </strong></label></td>
      <td>&nbsp;</td>
      <td colspan="2" align="right">Our Reference</td>
      <td><input type="text" name="yourref" id="yourref"></td>
      <td></td>
    </tr>
    <tr>
      <td width="189" class="boxlabel" >Date</td>
      <td><div align="left">
          <input type="Text" id="newdate" name="newdate" maxlength="25" size="25" value="<?php echo $ddateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y">
        </div></td>
      <td colspan="2" class="boxlabel">Reference</td>
      
      <!-- Trading transaction codes "'INV','CRN','GRN','RET','C_S','C_P','PUR','PAY'"  SORT OUT DIFFERENT CODES FOR STANDARD TRANSACTIONS add d_c, d_d, s_o, c_c-->
      
     <td width="151"><select name="ref" id="newref" onchange="ajaxGetRef(this.value); return false;"> 
          <option value="">Select type</option>
          <option value="CHQ">Cheque</option>
          <option value="DEP">Deposit</option>
          <option value="C_S">Cash Sale</option>
          <option value="C_P">Cash Purchase</option>
          <option value="PAY">Payment</option>
          <option value="GRN">Credit Purchase (Non Stock)</option>
          <option value="INV">Credit Sale (Non Stock)</option>
          <option value="CRD">Credit Card</option>
          <option value="EBI">Electronic Banking In</option>
          <option value="EBO">Electronic Banking Out</option>
          <option value="D_C">Debit Card</option>
          <option value="D_D">Direct Debit</option>
          <option value="SPO">Stop Order</option>
          <option value="REC">Receipt</option>
          <option value="CRN">Credit Note</option>
          <option value="RET">Return</option>
          <option value="P_C">Petty Cash</option>
          <option value="ADJ">Adjustment</option>
          <option value="TSF">Transfer</option>
          <option value="OTH">Other</option>
        </select></td>
      <td><input name="refno" type="text" readonly id="newrefno" size="10"></td>
    </tr>
    <tr bgcolor="#CCCCCC">
      <td class="boxlabel" ><label style="color:#000000;">Account to Debit</label></td>
      <td><div align="left">
          <select name="bgacc2dr" id="newbgacc2dr" onchange="ajaxGetACList(this.value,'dr'); return false;">
            <option>Select Ledger</option>
            <option value="GL">General Ledger</option>
            <option value="DR">Debtors Ledger</option>
            <option value="CR">Creditors Ledger</option>
            <option value="AS">Fixed Assets</option>
          </select>
        </div></td>
      <td colspan="3"><div align="left">
          <input type="text" name="DRaccount" id="DRaccount" size="45" readonly >
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onclick="sboxvisibledr()"> </div></td>
      <td id="ndrgst"><span class="style3">Include for <?php echo $_SESSION['s_tradtax']; ?> </span>
        <select name="dgst" id="newdrgst">
          <option value="N">Yes</option>
          <option value="Y">No</option>
        </select></td>
    </tr>
    <tr bgcolor="#FF9933">
      <td class="boxlabel"><label style="color:#000000;">Account to Credit</label></td>
      <td><div align="left">
          <select name="bgacc2cr" id="newbgacc2cr" onchange="ajaxGetACList(this.value,'cr'); return false;">
            <option>Select Ledger</option>
            <option value="GL">General Ledger</option>
            <option value="DR">Debtors Ledger</option>
            <option value="CR">Creditors Ledger</option>
            <option value="AS">Fixed Assets</option>
          </select>
        </div></td>
      <td colspan="3"><div align="left">
          <input type="text" name="CRaccount" id="CRaccount" size="45" readonly >
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="crsearch" onclick="sboxvisiblecr()"> </div></td>
      <td id="ncrgst"><span class="style3">Include for <?php echo $_SESSION['s_tradtax']; ?> </span>
        <select name="cgst" id="newcrgst">
          <option value="N">Yes</option>
          <option value="Y">No</option>
        </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Description</td>
      <td colspan="3">
          <input name="description" id="newdescription" type="text" size="60" maxlength="60">
        </td>
      <td colspan="2"><select name="newasset" id="newasset">
          <?php echo $asset_options; ?>
        </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Type of Tax</td>
      <td><div align="left">
          <select name="taxtype" id="newtaxtype">
            <?php echo $tax_options; ?>
          </select>
        </div></td>
      <td class="boxlabel" width="174">Amount before Tax</td>
      <td colspan="2"><div align="left">
          <input name="amount" id="newamount" type="text" size="17" maxlength="17" onfocus="this.select();" onBlur="add_tax(this.value);">
        </div></td>
      <td class="boxlabel">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel">Tax</td>
      <td><div align="left">
          <input name="tax" id="newtax" type="text" size="17" maxlength="17" onChange="change_tax(this.value);">
        </div></td>
      <td class="boxlabel">Amount after Tax</td>
      <td colspan="2"><div align="left">
          <input name="total" id="newtotal" value="0" type="text" size="17" maxlength="17" onfocus="this.select();" onBlur="deduct_tax(this.value);">
        </div></td>
      <td align="right">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel">&nbsp;</td>
      <td>&nbsp;</td>
      <td class="boxlabel">&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td align="right"><input type="button" value="Add" name="save"  onClick="addtrans()" ></td>
    </tr>
    <tr>
      <td colspan="6"><?php include "gettrans.php"; ?></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="6"><div align="right">
          <input name="Submit" type="button" value="Post" onClick="postTrans()">
        </div></td>
    </tr>
  </table>
<script>
	document.getElementById('newasset').style.visibility = 'hidden';
	document.getElementById('ndrgst').style.visibility = 'hidden';
	document.getElementById('ncrgst').style.visibility = 'hidden';
	document.getElementById('drsearch').style.visibility = 'hidden';
	document.getElementById('crsearch').style.visibility = 'hidden';
	document.getElementById('DRaccount').style.visibility = 'hidden';
	document.getElementById('CRaccount').style.visibility = 'hidden';
	//document.getElementById('newtaxtype').style.visibility = 'hidden';
	//document.getElementById('newamount').style.visibility = 'hidden';
	//document.getElementById('newtax').style.visibility = 'hidden';
	//document.getElementById('newtotal').style.visibility = 'hidden';
	</script>
    
    <?php 
		if ($gstperiod == 'Not Registered') {
			echo '<script>';
			echo "document.getElementById('newtaxtype').style.visibility = 'hidden';";
			echo "document.getElementById('newamount').style.visibility = 'hidden';";
			echo "document.getElementById('newtax').style.visibility = 'hidden';";
			echo "document.getElementById('gstnt').value = 'N_T~0'";
			echo '</script>';
		}
	?>
    
  <div id="glselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchgl" size="50" onkeypress="doSearchgl()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="glclose" onclick="sboxhidegl()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectgl.php"; ?></td>
      </tr>
    </table>
  </div>
  
  <div id="drselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchdr" size="50" onkeypress="doSearchdr()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="drclose" onclick="sboxhidedr()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectdr.php"; ?></td>
      </tr>
    </table>
  </div>
  
  <div id="crselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchcr" size="50" onkeypress="doSearchcr()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="drclose" onclick="sboxhidecr()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectcr.php"; ?></td>
      </tr>
    </table>
  </div>
  
  <div id="asselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchas" size="50" onkeypress="doSearchas()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="drclose" onclick="sboxhideas()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectas.php"; ?></td>
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
