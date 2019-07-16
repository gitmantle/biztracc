<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);
$coyname = $_SESSION['s_coyname'];
$son = $_REQUEST['son'];
$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$tradetable = 'ztmp'.$user_id.'_dn';
$serialtable = 'ztmp'.$user_id.'_serialnos';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$tradetable);
$db->execute();
$sql = "drop table if exists ".$findb.".".$serialtable;
$db->query($sql);
$db->execute();

$db->query("select * from ".$findb.".invhead where ref_no = '".$son."'");
$row = $db->single();
extract($row);
$cl = $client.'~'.$accountno.'~'.$sub.'~'.'';
$ddateh = $ddate;
$d = explode('-',$ddate);
$ddate = $d[2].'/'.$d[1].'/'.$d[0];
$desc = $gldesc;
$rf = substr($ref_no,3);
$cacc = $client.'~'.$accountno.'~'.$sub.'~'.$branch;
$padd = $postaladdress;
$dadd = $deliveryaddress;

$pb = 1;

$db->query("create table ".$findb.".".$tradetable." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, itemcode varchar(30) default '',item varchar(100) default '',price decimal(16,2) default 0, unit varchar(20) default '',quantity decimal(16,2) default 0,tax decimal(16,2) default 0, value decimal(16,2) default 0, discount decimal(16,2) default 0, disctype char(1) default '', discamount decimal(16,2) default 0, tot decimal(16,2) default 0, taxindex int(11) default 0, taxtype char(3) default '', taxpcent decimal(5,2) default 0, sellacc int(11) default 0, sellbr char(4) default '', sellsub int(4) default 0, purchacc int(11) default 0, purchbr char(4) default '', purchsub int(4) default 0, groupid int(11) default 0, catid int(11) default 0, loc int(11) default 0, sent decimal(16,2) default 0, picked decimal(16,2) default 0, trackserial char(3) default 'No', forex decimal(16,2) default 0, currency char(3) default '', rate decimal(7,3) default 1 )  engine myisam");
$db->execute();
$sql = "create table ".$findb.".".$serialtable." ( uid int(11) primary key, itemcode varchar(30) default '',item varchar(100) default '',serialno varchar(50) default '', locationid int(11) default 0, location varchar(30) default '', ref_no varchar(15) default '', selected char(1) default 'N')  engine innodb";
$db->query($sql);
$db->execute();

$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$country = $_SESSION['country'];

$ino = 'S_O'.substr($son,3);

// find last delivery note for this sales order and increment suffix by 1
$db->query("select max(ref_no) as mr from ".$findb.".invhead where ref_no like 'D_N%' and xref = '".$son."'");
$row = $db->single();
extract($row);
if ($mr == NULL) {
	$n2 = 1;
} else {
	$no = explode('-',$mr);
	$n1 = $no[1];
	$n2 = $n1 + 1;
}

$dnno = $rf.'-'.$n2;

$db->query("insert into ".$findb.".".$tradetable." (uid,itemcode,item,price,unit,quantity,sent,tax,value,tot,discount,disctype,taxtype,taxpcent,currency,rate) select uid,itemcode,item,price,unit,quantity,returns,tax,value,(tax+value),discount,disc_type,taxtype,taxpcent,currency,rate from ".$findb.".invtrans where ref_no = '".$ino."'");
$db->execute();

// find out which use serial numbers
$db->query("select uid,itemcode from ".$findb.".".$tradetable);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$id = $uid;
	$db->query("select trackserial from ".$findb.".stkmast where itemcode = '".$itemcode."'");
	$rowt = $db->single();
	extract($rowt);
	$db->query("update ".$findb.".".$tradetable." set trackserial = :trackserial where uid = :uid");
	$db->bind(':trackserial', $trackserial);
	$db->bind(':uid', $id);
	$db->execute();
}

// populate Tax type list
$db->query("select * from ".$findb.".taxtypes");
$rows = $db->resultset();
$tax_options = "<option value=\"\">Select Tax Type</option>";
foreach ($rows as $row) {
	extract($row);
	$tax_options .= "<option value=\"".$taxpcent."#".$tax."\">".$tax.' - '.$description."</option>";
}

$db->query('select * from '.$findb.'.stklocs order by location');
$rows = $db->resultset();
// populate location list
$loc_options = "<option value=\"\">Select Location</option>";
foreach ($rows as $row) {
	extract($row);
	$loc_options .= "<option value=\"".$uid."\">".$location."</option>";
}

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Delivery Note</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../fin/includes/ajaxgetref.js"></script>
<script>

	window.name = "adddo";


function CheckAvailable(q) {
	var is = document.getElementById('stkitem').value;
	var locid = 1;
	var i = is.split('~');
	var stkid = i[0];

	if (q != 0) {
		$.get("includes/ajaxCheckAvailable.php", {q:q, stkid:stkid, locid:locid}, function(data){
				if (data == '') {
					return true;
				} else {
					alert(data);
					document.getElementById('pickqty').value = 0;
					document.getElementById('pickqty').focus();
					return true;
				}
		});
	}
}

var lineref = '';
var icode = '';

function pick(cl,rf) {
	lineref = cl;
	icode = rf;
	document.getElementById('qty2pick').style.visibility = 'visible';
}

function qtypicked() {
	jQuery.ajaxSetup({async:false});

	var pickedqty = document.getElementById('pickqty').value;
	$.get("includes/ajaxdnpicked.php", {rid:lineref, picked:pickedqty}, function(data){
			if (data == 'Y') {
				$("#deliverylist").trigger("reloadGrid");
				document.getElementById('pickqty').value = 0;
			} else if (data == 'S') {
				$.get("../ajax/ajaxUpdtItemcode.php", {itemcode: icode}, function(data){});
				jQuery("#selectseriallist").setGridParam({url:"selectserials.php"}).trigger("reloadGrid"); 
				document.getElementById('sellserial').style.visibility = 'visible';
			} else {
				alert(data);
				return true;
				document.getElementById('pickqty').value = 0;
			}
	});
	document.getElementById('qty2pick').style.visibility = 'hidden';
	jQuery.ajaxSetup({async:true});
}

function addsellserialnos() {
	var required = document.getElementById('pickqty').value;
	var selected = document.getElementById('noselected').value;
	if (parseFloat(required) != parseFloat(selected)) {
		alert('Quantity required and quantity selected do not match');
		return false;
	} else {
		$.get("includes/ajaxtrimserials.php", {}, function(data){});
		document.getElementById('pickqty').value = 0;
		document.getElementById('sellserial').style.visibility = 'hidden';
		$("#deliverylist").trigger("reloadGrid");
	}
}

function serialselect(id) {
	var sel = document.getElementById('noselected').value;
	var newsel = parseFloat(sel) + 1;
	document.getElementById('noselected').value = newsel;
	$.get("includes/ajaxserialselect.php", {id: id}, function(data){$("#selectseriallist").trigger("reloadGrid")});																	
}

function serialdeselect(id) {
	var sel = document.getElementById('noselected').value;
	var newsel = parseFloat(sel) - 1;
	document.getElementById('noselected').value = newsel;
	$.get("includes/ajaxserialdeselect.php", {id: id}, function(data){$("#selectseriallist").trigger("reloadGrid")});																	
}

function sellserialclose() {
	document.getElementById('sellserial').style.visibility = 'hidden';
}

function hideprint() {
	document.getElementById('printpage').style.visibility = 'hidden';
}

function printtrading(rf) {
	
	alert('got here');
	
	
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	$type = 'D_N'
	window.open('PrintTrading.php?type='+type+'&tradingref='+rf,'plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function emailtrading(rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	$type = 'D_N'
	window.open('PrintQuote.php?type='+type+'&tradingref='+rf+'&doemail=Y','plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function makedn() {
	var loc = document.getElementById('loc').value;
	if (loc == '') {
		alert('You must select a stock location');
		return;
	} else {
		var ref = document.getElementById('refno').value;
		  if (confirm("Are you sure you want to dispatch the picked items?")) {
			jQuery.ajaxSetup({async:false});  
			$.get("../ajax/ajaxdnpick.php", {ref: ref, loc: loc}, function(data){alert(data);$("#deliverylist").trigger("reloadGrid")});
			jQuery.ajaxSetup({async:true});
			window.opener.refreshdngrid();
			this.close();
		  }
	}
}

function pickall() {
	$.get("includes/ajaxpickall.php", {}, function(data){$("#deliverylist").trigger("reloadGrid")});																	
}

function picknone() {
	$.get("includes/ajaxpicknone.php", {}, function(data){$("#deliverylist").trigger("reloadGrid")});																	
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
  <input type="hidden" name="clientid" id="clientid" value=<?php echo $client_id; ?>>
  <input type="hidden" name="priceband" id="priceband" value=<?php echo $pb; ?>>
  <input type="hidden" name="setsell" id="setsell" value="0">
  <input type="hidden" name="trackserial" id="trackserial" value="No">
  <input type="hidden" name="gstinvpay" id="gstinvpay" value=<?php echo $gstinvpay; ?>>
  <table  width="1000" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="7"><label style="color: <?php echo $thfont; ?>"><strong>Delivery Note</strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel" >Date</td>
      <td><div align="left">
     <input type="Text" id="newdate" name="newdate" value="<?php echo $ddateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j,Y">
        </div></td>
      <td class="boxlabel">Description</td>
      <td ><div align="left">
          <input name="description" id="description" type="text" value="<?php echo $desc; ?>" readonly >
        </div></td>
      <td  class="boxlabel">Reference</td>
      <td><input name="ref" id="ref" type="text" size="5" value="D_N" readonly></td>
      <td><input name="refno" type="text" id="refno" size="10" readonly value="<?php echo $dnno; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel" >&nbsp;</td>
      <td class="boxlabelleft" >&nbsp;</td>
      <td class="boxlabel">Client</td>
      <td ><input type="text" name="TRaccount" id="TRaccount" value="<?php echo $cl ?>" size="45" readonly ></td>
      <td class="boxlabel">From Location</td>
      <td colspan="2" ><select name="loc" id="loc">
        <?php echo $loc_options; ?>
      </select></td>
    </tr>
    <tr>
      <td colspan="7" class="boxlabelleft" >Post Invoice to:
          <input type="text" name="postadd" id="postadd" value="<?php echo $padd ?>" size="45"  readonly >
        &nbsp;&nbsp;&nbsp;&nbsp;
        Deliver Goods to:
        <input type="text" name="deliveradd" id="deliveradd" value="<?php echo $dadd ?>" size="45" readonly ></td>
    </tr>
    <tr>
    	<td colspan="4">&nbsp;</td>
        <td align="right"><input name="ball" type="button" value="Pick same as Outstanding" onClick="pickall()"></td>
        <td colspan="2" align="right"><input name="bnone" type="button" value="Zero Pick" onClick="picknone()"></td>
    </tr>
    <tr>
      <td colspan="7" align="center"><?php include "getdeliverynotes.php"; ?></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="6"><img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
      <td align="right"><input name="Submit" type="button" value="Compile Delivery Note" onClick="makedn()"></td>
    </tr>
  </table>
  <div id="printpage" style="position:absolute;visibility:hidden;top:200px;left:400px;height:75px;width:250px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bghead; ?>; border-style:outset;">
    <table width="250">
      <tr bgcolor="<?php echo $bghead; ?>">
        <td colspan="3" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Print Delivery Note</strong></label></td>
      <tr>
        <td><input type="button" name="cancel" id="cancel" value="Close" onClick="hideprint()"></td>
        <td align="center"><input type="button" name="email" id="email" value="Email" onClick="emailtrading('D_N')"></td>
        <td align="right"><input type="button" name="print" id="print" value="Print" onClick="printtrading('D_N')"></td>
      </tr>
    </table>
  </div>
  <div id="qty2pick" style="position:absolute;visibility:hidden;top:400px;left:500px;height:75px;width:250px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bghead; ?>; border-style:outset;">
	<table width="250">
        <tr bgcolor="<?php echo $bghead; ?>">
        <td colspan="3" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Quantity to Deliver</strong></label></td>
    </tr>
    <tr>
      <td>Quantity<input type="text" name="pickqty" id="pickqty" value="0" onFocus="this.select()"></td>
      <td align="right"><input type="button" name="pqty" id="pqty" value="Save" onClick="qtypicked()"></td>
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
 
</form>
</body>
</html>
