<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$usersession = $_SESSION['usersession'];
$coyidno = $_SESSION['s_coyid'];

include_once("../includes/DBClass.php");
$db_trd = new DBClass();

$db_trd->query("select * from sessions where session = :vusersession");
$db_trd->bind(':vusersession', $usersession);
$row = $db_trd->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_trading';
$serialtable = 'ztmp'.$user_id.'_serialnos';

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$sql = "drop table if exists ".$findb.".".$table;
$db_trd->query($sql);
$db_trd->execute();
$sql = "drop table if exists ".$findb.".".$serialtable;
$db_trd->query($sql);
$db_trd->execute();

$db_trd->query("create table ".$findb.".".$table." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, itemcode varchar(30) default '',item varchar(100) default '',price decimal(16,2) default 0, unit varchar(20) default '',quantity decimal(16,2) default 0,tax decimal(16,2) default 0, value decimal(16,2) default 0, discount decimal(16,2) default 0, disctype char(1) default '', discamount decimal(16,2) default 0, tot decimal(16,2) default 0, taxindex int(11) default 0, taxtype char(3) default '', taxpcent decimal(5,2) default 0, sellacc int(11) default 0, sellbr char(4) default '', sellsub int(4) default 0, purchacc int(11) default 0, purchbr char(4) default '', purchsub int(4) default 0, groupid int(11) default 0, catid int(11) default 0, loc int(11) default 0, stock char(3) default '', avcost decimal(16,2) default 0, forex decimal(16,2) default 0, currency char(3) default '', rate decimal(7,3) default 1, your_ref varchar(30) default '' )  engine myisam");
$db_trd->execute();

$db_trd->query("create table ".$findb.".".$serialtable." (itemcode varchar(30) default '',item varchar(100) default '',serialno varchar(50) default '', locationid int(11) default 0, location varchar(30) default '', ref_no varchar(15) default '', selected char(1) default 'N')  engine myisam");
$db_trd->execute();

$crfile = 'ztmp'.$user_id.'_crlist';

$db_trd->query("drop table if exists ".$findb.".".$crfile);
$db_trd->execute();

$db_trd->query("create table ".$findb.".".$crfile." (account varchar(45),preferred varchar(45),priceband int,accountno int,Sub int default 0,client_id int default 0,blocked char(3) default 'No')  engine myisam");
$db_trd->execute();

$db_trd->query("insert into ".$findb.".".$crfile." select concat(members.firstname,' ',members.lastname,' ',client_company_xref.subname) as account,members.preferredname,client_company_xref.priceband,client_company_xref.crno as accountno,client_company_xref.crsub as sub,client_company_xref.client_id,".$cltdb.".client_company_xref.blocked from ".$cltdb.".members left join ".$cltdb.".client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.company_id = ".$coyidno." and client_company_xref.crno != '' ");
$db_trd->execute();

// Add uid
$db_trd->query("alter table ".$findb.".".$crfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
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

$db_trd->query("select gsttype as gstinvpay, gstperiod from ".$findb.".globals");
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


require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

$db_trd->closeDB();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Goods Received</title>

<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script type="text/javascript" src="includes/ajaxgetref.js"></script>

<script>

	window.name = "tr_grn";

	function dogrid() {
		jQuery("#tradlist").setGridParam({url:"gettradingtrans.php"}).trigger("reloadGrid"); 
	}

</script>
</head>
<body>
<form name="grn" id="grn" method="post" >
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <input type="hidden" name="paytype" id="paytype" value="">
  <input type="hidden" name="sacc" id="sacc" value="">
  <input type="hidden" name="pacc" id="pacc" value="">
  <input type="hidden" name="grp" id="grp" value="0">
  <input type="hidden" name="cat" id="cat" value="0">
  <input type="hidden" name="trading" id="trading" value="grn">
  <input type="hidden" name="clientid" id="clientid" value="0">
  <input type="hidden" name="priceband" id="priceband" value="0">
  <input type="hidden" name="setsell" id="setsell" value="0">
  <input type="hidden" name="stock" id="stock" value="">
  <input type="hidden" name="avcost" id="avcost" value="">
  <input type="hidden" name="disc" id="disc" value="0">
  <input type="hidden" name="disctype" id="disctype" value="">
  <input type="hidden" name="trackserial" id="trackserial" value="No">
  <input type="hidden" name="noselected" id="noselected" value="0">
  <input type="hidden" name="gstinvpay" id="gstinvpay" value=<?php echo $gstinvpay; ?>>
  <input type="hidden" name="scurrency" id="scurrency" value="">
  <input type="hidden" name="gstnt" id="gstnt" value="">
  <table  width="960" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="11"><label style="color: <?php echo $thfont; ?>"><strong>Goods Received Note </strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel" >Date</td>
      <td><div align="left">
          <input type="Text" id="newdate" name="newdate" maxlength="25" size="25" value="<?php echo $ddateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y">
        </div></td>
      <td class="boxlabel">Description</td>
      <td colspan="5" ><div align="left">
          <input name="description" id="description" type="text" onFocus="this.select()" size="50" maxlength="60">
        </div></td>
      <td  class="boxlabel">Reference</td>
      <td><input name="ref" id="ref" type="text" size="5" value="GRN" readonly></td>
      <td><input name="refno" type="text" id="refno" size="10" value="0" readonly onFocus="nexttrdref('grn','N')"></td>
    </tr>
    <tr>
      <td class="boxlabel" >Currency</td>
      <td ><select name="currency" id="currency" onchange="setfx();"><?php echo $forex_options;?></select></td>
      <td class="boxlabel">Creditor</td>
      <td colspan="5" ><input type="text" name="TRaccount" id="TRaccount" size="45" readonly >
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="crsearch" onclick="transvisiblecr()"></td>
      <td class="boxlabel">To Location</td>
      <td colspan="2" ><select name="loc" id="loc"><?php echo $loc_options; ?>
      </select></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="10"><label style="color: <?php echo $thfont; ?>"><strong>Add an Item </strong></label>&nbsp;&nbsp;&nbsp;Barcode&nbsp;<input type="text" name="stkbarcode" id="stkbarcode" size="30" onkeypress="findbcode()" onpaste="findbcode()"> 
      <td><div align="right"></div></td>
    </tr>
    <tr>
      <td class="boxlabel" >Item</td>
      <td colspan="2" ><input type="text" name="stkitem" id="stkitem" style="width: 190px !important; min-width: 190px; max-width: 190px;" readonly >
      <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="crsearch" onclick="stkvisible()"> </div></td>
      <td class="boxlabel" ><input type="text" name="localcode" id="localcode" size="5" value="Price" readonly></td>
      <td ><input type="text" name="price" id="price" size="7" value="0" onfocus="this.select();" onChange="calcPriceForex();"></td>
      <td class="boxlabel" >per Unit</td>
      <td ><input type="text" name="unit" id="unit" size="5" readonly></td>
      <td class="boxlabel" >Qty</td>
      <td ><input type="text" name="qty" id="qty" size="7" value="0" onfocus="this.select();"></td>
      <td colspan="2"><select name="tax" id="tax"><?php echo $tax_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel" >&nbsp;</td>
      <td colspan="2" >&nbsp;</td>
      <td class="boxlabel"><input type="text" name="fxcode" id="fxcode" size="5" value = "" readonly></td>
      <td ><input type="text" name="fxamt" id="fxamt" size="10" value = "" readonly></td>
      <td class="boxlabel" >&nbsp;</td>
      <td >&nbsp;</td>
      <td class="boxlabel" >&nbsp;</td>
      <td >&nbsp;</td>
      <td colspan="2" class="boxlabel" ><input type="button" value="Add" name="save"  onClick="checkserial('grn')" ></td>
    </tr>
    <tr>
      <td colspan="11"><?php include "gettradingtrans.php"; ?></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="11"><div align="right">
          <input name="Submit" type="button" value="Post" onClick="postTrdTrans('grn')">
        </div></td>
    </tr>
    
  </table>

  
  <div id="crselect" style="position:absolute;visibility:hidden;top:98px;left:342px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchtrdcr" size="50" onkeypress="doSearchtrdcr()" /></td>
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
  
   <div id="tserial" style="position:absolute;visibility:hidden;top:200px;left:200px;height:190px;width:560px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bghead; ?>; border-style:outset;">
	<table width="550">
    <tr bgcolor="<?php echo $bghead; ?>">
    <td colspan="3" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Enter serial numbers separated by commas.</strong></label></td>
    <tr>
      <td>Serial numbers</td>
      <td align="right"><input type="button" name="sequential" id="sequential" value="Add sequential serial numbers" onClick="sequentialserial()"></td>
    </tr>
    <tr>
      <td colspan="2"><textarea name="serialnos" id="serialnos" cols="85" rows="5"></textarea></td>
      </tr>
    <tr>
      <td><input type="button" name="bcancel" id="bcancel" value="Cancel" onClick="tserialclose()"></td>
      <td align="right"><input type="button" name="bserial" id="bserial" value="Save" onClick="addserialnos()"></td>
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
