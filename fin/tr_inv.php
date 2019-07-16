<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$usersession = $_SESSION['usersession'];

$_SESSION['s_dn2inv'] = 'N';

include_once("../includes/DBClass.php");
$db_trd = new DBClass();

$db_trd->query("select * from sessions where session = :vusersession");
$db_trd->bind(':vusersession', $usersession);
$row = $db_trd->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

// populate  staff drop down
$db_trd->query("select * from users where sub_id = :subid order by ulname");
$db_trd->bind(':subid', $subid);
$rows = $db_trd->resultset();
$staff_options = '<option value="0">Select</option>';
foreach ($rows as $row) {
	extract($row);
	if ($row['uid'] == $user_id) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$staff_options .= '<option value="'.$row['ufname'].' '.$row['ulname'].'"'.$selected.'>'.$row['ufname'].' '.$row['ulname'].'</option>';
}

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

$sql = "create table ".$findb.".".$table." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, itemcode varchar(30) default '',item varchar(100) default '',price decimal(16,2) default 0, unit varchar(20) default '',quantity decimal(16,2) default 0,tax decimal(16,2) default 0, value decimal(16,2) default 0, discount decimal(16,2) default 0, disctype char(1) default '', discamount decimal(16,2) default 0, tot decimal(16,2) default 0, taxindex int(11) default 0, taxtype char(3) default '', taxpcent decimal(5,2) default 0, sellacc int(11) default 0, sellbr char(4) default '', sellsub int(4) default 0, purchacc int(11) default 0, purchbr char(4) default '', purchsub int(4) default 0, groupid int(11) default 0, catid int(11) default 0, loc int(11) default 0, stock char(3) default '', avcost decimal(16,2) default 0, forex decimal(16,2) default 0, currency char(3) default '', rate decimal(7,3) default 1, your_ref varchar(30) default '' )  engine innodb";
$db_trd->query($sql);
$db_trd->execute();
$sql = "create table ".$findb.".".$serialtable." ( uid int(11) primary key, itemcode varchar(30) default '',item varchar(100) default '',serialno varchar(50) default '', locationid int(11) default 0, location varchar(30) default '', ref_no varchar(15) default '', selected char(1) default 'N')  engine innodb";
$db_trd->query($sql);
$db_trd->execute();


$coyid = $_SESSION['s_coyid'];
$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$country = $_SESSION['country'];

$drfile = 'ztmp'.$user_id.'_drlist';

$db_trd->query("drop table if exists ".$findb.".".$drfile);
$db_trd->execute();

$db_trd->query("create table ".$findb.".".$drfile." (account varchar(45),preferred varchar(45),priceband int,accountno int,sub int default 0,client_id int default 0,blocked char(3) default 'No')  engine myisam");
$db_trd->execute();

$db_trd->query("insert into ".$findb.".".$drfile." select concat(".$cltdb.".members.firstname,' ',".$cltdb.".members.lastname,' ',".$cltdb.".client_company_xref.subname) as account,".$cltdb.".members.preferredname,".$cltdb.".client_company_xref.priceband,".$cltdb.".client_company_xref.drno as accountno,".$cltdb.".client_company_xref.drsub as sub,".$cltdb.".client_company_xref.client_id,".$cltdb.".client_company_xref.blocked from ".$cltdb.".members left join ".$cltdb.".client_company_xref on ".$cltdb.".members.member_id = ".$cltdb.".client_company_xref.client_id where ".$cltdb.".client_company_xref.company_id = ".$coyid." and ".$cltdb.".client_company_xref.drno != ''");
$db_trd->execute();

// Add uid
$db_trd->query("alter table ".$findb.".".$drfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db_trd->execute();

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
<title>Invoice</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script type="text/javascript" src="includes/ajaxgetref.js"></script>
<script type="text/javascript" src="includes/ajaxCheckTransDate.js"></script>
<script>

  window.name = "tr_inv";

//********************************************************
// list wip for invoicing
//********************************************************
function listwip() {
 	
		var x = 0, y = 0; // default values	
	    x = window.screenX +50;
		y = window.screenY +225;
		dr = document.getElementById('TRaccount').value;		
		d = dr.split('~');		
		dracc = d[1];	
		
		if (typeof dracc == 'undefined') {
			alert('Please select a Debtor');
		} else {
			window.open('invwiplist.php?dracc='+dracc,'invwipl','toolbar=0,scrollbars=1,height=550,width=330,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
		}
    
}


	
</script>
</head>
<body>
<form name="inv" id="inv" method="post" >
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <input type="hidden" name="paytype" id="paytype" value="">
  <input type="hidden" name="sacc" id="sacc" value="">
  <input type="hidden" name="pacc" id="pacc" value="">
  <input type="hidden" name="grp" id="grp" value="0">
  <input type="hidden" name="cat" id="cat" value="0">
  <input type="hidden" name="trading" id="trading" value="inv">
  <input type="hidden" name="clientid" id="clientid" value="0">
  <input type="hidden" name="priceband" id="priceband" value="0">
  <input type="hidden" name="setsell" id="setsell" value="0">
  <input type="hidden" name="stock" id="stock" value="">
  <input type="hidden" name="avcost" id="avcost" value="">
  <input type="hidden" name="trackserial" id="trackserial" value="No">
  <input type="hidden" name="gstinvpay" id="gstinvpay" value=<?php echo $gstinvpay; ?>>
  <input type="hidden" name="scurrency" id="scurrency" value="">
  <input type="hidden" name="gstnt" id="gstnt" value="">
  <table  width="980" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td><label style="color: <?php echo $thfont; ?>"><strong>Invoice </strong></label></td>
      <td>&nbsp;</td>
      <td>Our Reference</td>
      <td colspan="6"><input type="text" name="yourref" id="yourref"></td>
      <td colspan="5">Staff Member
        <select name="lstaff" id="lstaff"><?php echo $staff_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel" >Date</td>
      <td><div align="left">
          <input type="Text" id="newdate" name="newdate" value="<?php echo $ddateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j,Y">
        </div></td>
      <td class="boxlabel">Description</td>
      <td colspan="8" ><div align="left">
          <input name="description" id="description" type="text" onFocus="this.select()" size="50" maxlength="60">
        </div></td>
      <td  class="boxlabel">Reference</td>
      <td><input name="ref" id="ref" type="text" size="5" value="INV" readonly></td>
      <td><input name="refno" type="text" id="refno" size="10" value="0" readonly onFocus="nexttrdref('inv','N')"></td>	
    </tr>
    <tr>
      <td class="boxlabel" >&nbsp;</td>
      <td class="boxlabelleft" ><label>Proforma
        <input type="checkbox" name="proforma" id="proforma">
      </label></td>
      <td class="boxlabel">Debtor</td>
      <td colspan="8" ><input type="text" name="TRaccount" id="TRaccount" size="45" readonly >
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onclick="transvisibledr()"> </td>
      <td class="boxlabel"> From Location</td>
      <td colspan="2" ><select name="loc" id="loc"><?php echo $loc_options; ?>
      </select></td>
    </tr>
    <tr>
      <td colspan="3" class="boxlabelleft" ><label>Post Invoice to:
          <select name="postadd" id="postadd" style="width: 150px !important; min-width: 150px; max-width: 150px;">
          </select>
      </label></td>
      <td colspan="8" class="boxlabelleft" >Deliver Goods to:
        <select name="deliveradd" id="deliveradd" style="width: 200px !important; min-width: 200px; max-width: 200px;">
      </select></td>
      <td class="boxlabel">Currency</td>
      <td colspan="2"><select name="currency" id="currency" onchange="setfx();"><?php echo $forex_options;?></select></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="13"><label style="color: <?php echo $thfont; ?>"><strong>Add an Item </strong></label>&nbsp;&nbsp;&nbsp;Barcode&nbsp;<input type="text" name="stkbarcode" id="stkbarcode" size="30" onkeypress="findbcode()" onpaste="findbcode()"></td>
    <?php
		if ($subid == 45) {
			echo '<td><input type="button" name="invwip" id="invwip" value="Invoice from WIP" onclick="listwip()"></td>';					
		} else {
          echo '<td>&nbsp;</td>';
        }
	?>
      
    </tr>
    <tr>
      <td class="boxlabel" >Item</td>
      <td colspan="2" ><input type="text" name="stkitem" id="stkitem" style="width: 150px !important; min-width: 150px; max-width: 150px;" readonly >
      <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onclick="stkvisible()"> </td>
      <td class="boxlabel" ><input type="text" name="localcode" id="localcode" size="5" value="Price" readonly></td>
      <td ><input type="text" name="price" id="price" size="7" onfocus="this.select();" onChange="calcPriceForex();"></td>
      <td class="boxlabel">per</td>
      <td class="boxlabel" ><input type="text" name="unit" id="unit" size="5" readonly></td>
      <td class="boxlabel" >Disc.</td>
      <td ><label>
        <input type="text" name="disc" id="disc" size="5" value="0" onfocus="this.select();">
      </label></td>
      <td class="boxlabel" ><select name="disctype" id="disctype">
        <option value="%">%</option>
        <option value="$">$</option>
      </select></td>
      <td class="boxlabel" >Qty</td>
      <td ><input type="text" name="qty" id="qty" size="7" value="0" onfocus="this.select();" onBlur="CheckAvailable(this.value)"></td>
      <td colspan="2" id="gsttp"><select name="tax" id="tax"><?php echo $tax_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel" >&nbsp;</td>
      <td colspan="2" >&nbsp;</td>
      <td class="boxlabel"><input type="text" name="fxcode" id="fxcode" size="5" value = "" readonly></td>
      <td ><input type="text" name="fxamt" id="fxamt" size="10" value = "" readonly></td>
      <td class="boxlabel">&nbsp;</td>
      <td class="boxlabel" >&nbsp;</td>
      <td class="boxlabel" >&nbsp;</td>
      <td >&nbsp;</td>
      <td class="boxlabel" >&nbsp;</td>
      <td class="boxlabel" >&nbsp;</td>
      <td >&nbsp;</td>
      <td colspan="2" align="right" id="gsttp2"><input type="button" value="Add Line Item" name="save"  onClick="checkserial('inv')" ></td>
    </tr>
    <tr>
      <td colspan="14"><?php include "gettradingtrans.php"; ?></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="7"><img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
      <td align="right" colspan="7"><input name="Submit" type="button" value="Post" onClick="postTrdTrans('INV')"></td>
    </tr>
    
  </table>

  
  <div id="drselect" style="position:absolute;visibility:hidden;top:98px;left:342px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchtrddr" size="50" onkeypress="doSearchtrddr()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="crclose" onclick="sboxhidedr()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selecttrddr.php"; ?></td>
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
  
	<div id="printpage" style="position:absolute;visibility:hidden;top:200px;left:400px;height:75px;width:250px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bghead; ?>; border-style:outset;">
	<table width="250">
    <tr bgcolor="<?php echo $bghead; ?>">
    <td colspan="3" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Print Invoice</strong></label></td>
    <tr>
      <td><input type="button" name="cancel" id="cancel" value="Close" onClick="hideprint()"></td>
      <td align="center"><input type="button" name="email" id="email" value="Email" onClick="emailtrading('inv')"></td>
      <td align="right"><input type="button" name="print" id="print" value="Print" onClick="printtrading('inv')"></td>
    </tr>
    </table>
  
	</div>  

   <div id="sellserial" style="position:absolute;visibility:hidden;top:200px;left:400px;height:280px;width:320px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bghead; ?>; border-style:outset;">
	<table width="310">
    <tr>
        <td colspan="2"><?php include "selectserials.php"; ?></td>
    </tr>
    <tr>
    	<td class="boxlabel">Quantity selected</td>
        <td class="boxlabelleft"><input type="text" name="noselected" id="noselected" size="7" value="0" readonly></td>
    </tr>
    <tr>
      <td><input type="button" name="bcancel" id="bcancel" value="Cancel" onClick="sellserialclose()"></td>
      <td align="right"><input type="button" name="bserial" id="bserial" value="Save" onClick="addsellserialnos()"></td>
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
 
     <?php 
		if ($gstperiod == 'Not Registered') {
			echo '<script>';
			echo "document.getElementById('tax').style.visibility = 'hidden';";
			echo "document.getElementById('gstnt').value = 'N_T~0'";
			echo '</script>';
		}
	?> 
 
 
</form>
</body>
</html>
