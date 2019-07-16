<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);


$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);



$table = 'ztmp'.$user_id.'_trans';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$table;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$table." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, acc2dr int(11) default 0,subdr int(11) default 0,brdr char(4) default '',drindex int(10) default 0,acc2cr int(11) default 0,subcr int(11) default 0,brcr char(4)default '',crindex int(10) default 0,ddate date default '0000-00-00',descript1 varchar(60),reference char(9) default '',refindex int(10) default 0,amount double(16,2) default 0,depdr int(11),depbrdr char(4),depcr int(11),depbrcr char(4),nallocate int(11),tax double(16,2),taxtype char(3),taxpcent double(5,2),applytax char(1),total double(16,2) default 0, done int(11) default 0,type char(1),grn char(10),inv char(10),currency char(3), rate double(7,3),a2d varchar(45),a2c varchar(45),taxindex int(10),drgst char(1) default 'N', crgst char(1) default 'N')  engine myisam";
$calc = mysql_query($query) or die(mysql_error());



$coyid = $_SESSION['s_coyid'];
$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$country = $_SESSION['country'];



$query = 'select * from branch order by branchname';
$result = mysql_query($query) or die(mysql_error());
// populate branches list
$branch_options = "<option value=\"\">Select Branch</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$branch_options .= "<option value=\"".$branch."\">".$branchname."</option>";
}

$q = "select gsttype from globals";
$r = mysql_query($q) or die (mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$gstinvpay = $gsttype;


//populate list of assets
$asset_options = "<option value=\"\">Against which asset</option>";

// populate Tax type list
$query = "select * from taxtypes";
$result = mysql_query($query) or die(mysql_error());
$tax_options = "<option value=\"\">Select Tax Type</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$tax_options .= "<option value=\"".$taxpcent."#".$tax."\">".$tax.' - '.$description."</option>";
}



require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Non-Stock Purchase</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script type="text/javascript" src="includes/ajaxgetref.js"></script>
<script type="text/javascript" src="includes/ajaxCheckTransDate.js"></script>
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>
<script>

	window.name = "tr_nsp";

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
  <table width="950" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td><label style="color: <?php echo $thfont; ?>"><strong>New Transaction Line </strong></label></td>
      <td>&nbsp;</td>
      <td colspan="4"><div align="right">
          <input type="button" value="Add" name="save"  onClick="addtrans()" >
        </div></td>
    </tr>
    <tr>
      <td width="189" class="boxlabel" >Date</td>
      <td><div align="left">
          <input type="Text" id="newdate" name="newdate" maxlength="25" size="25" value="<?php echo $ddate; ?>" onChange="ajaxCheckTransDate();"><a href="javascript:NewCal('newdate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
        </div></td>
      <td colspan="2" class="boxlabel">Reference</td>
      <td width="151"><select name="ref" id="newref" >
          <option value="NSP" selected>Non-Stock Purchase</option>
        </select></td>
      <td width="165"><input name="refno" type="text" id="newrefno" size="10" readonly onFocus="nexttrdref_ns('nsp')"></td>
    </tr>
    <tr bgcolor="#CCCCCC">
      <td class="boxlabel" ><label style="color:#000000;">Allocate Purchase to</label></td>
      <td colspan="3"><div align="left">
          <input type="text" name="DRaccount" id="DRaccount" size="45" readonly >
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onclick="sboxvisibledr_ns()"> </div></td>
      <td><div align="left">
          <select name="bgacc2dr" id="newbgacc2dr" onchange="ajaxGetACList_ns(this.value,'dns'); return false;">
            <option value="GL" selected>General Ledger</option>
          </select>
        </div></td>
      <td id="ndrgst"><span class="style3">Include for GST</span>
        <select name="dgst" id="newdrgst">
          <option value="Y">Yes</option>
          <option value="N">No</option>
        </select></td>
    </tr>
    <tr bgcolor="#FF9933">
      <td class="boxlabel"><label style="color:#000000;">Pay from Account</label></td>
      <td colspan="3"><div align="left">
          <input type="text" name="CRaccount" id="CRaccount" size="45" readonly >
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="crsearch" onclick="sboxvisiblecr_ns()"> </div></td>
      <td><div align="left">
          <select name="bgacc2cr" id="newbgacc2cr" onchange="ajaxGetACList_ns(this.value,'bns'); return false;">
            <option value="GL" selected>General Ledger</option>
          </select>
        </div></td>
      <td id="ncrgst"><span class="style3">Include for GST </span>
        <select name="cgst" id="newcrgst">
          <option value="Y">Yes</option>
          <option value="N">No</option>
        </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Description</td>
      <td colspan="3">
          <input name="description" id="newdescription" type="text" onFocus="showtax_ns()" size="60" maxlength="60">
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
          <input name="tax" id="newtax" readonly type="text" size="17" maxlength="17">
        </div></td>
      <td class="boxlabel">Amount after Tax</td>
      <td colspan="2"><div align="left">
          <input name="total" id="newtotal" value="0" type="text" size="17" maxlength="17" onfocus="this.select();" onBlur="deduct_tax(this.value);">
        </div></td>
      <td align="right">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="6"><?php include "gettrans.php"; ?></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="12"><div align="right">
          <input name="Submit" type="button" value="Post" onClick="postTrans()">
        </div></td>
    </tr>
  </table>
  <script>
	document.getElementById('newasset').style.visibility = 'hidden';
	document.getElementById('ndrgst').style.display = 'none';
	document.getElementById('ncrgst').style.display = 'none';
	document.getElementById('newtaxtype').style.visibility = 'hidden';
	document.getElementById('newamount').style.visibility = 'hidden';
	document.getElementById('newtax').style.visibility = 'hidden';
	document.getElementById('newtotal').style.visibility = 'hidden';
	document.getElementById('newbgacc2dr').style.visibility = 'hidden';
	document.getElementById('newbgacc2cr').style.visibility = 'hidden';
	</script>
    
  <div id="crselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchgl" size="50" onkeypress="doSearchglns()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="glclose" onclick="sboxhideglns()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectbcnsp.php"; ?></td>
      </tr>
    </table>
  </div>
  
  <div id="drselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchdr" size="50" onkeypress="doSearchdr()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="drclose" onclick="sboxhidedrns()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectdnsp.php"; ?></td>
      </tr>
    </table>
  </div>
  

  
</form>
</body>
</html>
