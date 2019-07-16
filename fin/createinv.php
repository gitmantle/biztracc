<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);
$coyidno = $_SESSION['s_coyid'];
$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$usersession = $_SESSION['usersession'];
$cust = $_SESSION['s_customer'];
$c = explode('~',$cust);
$ac = $c[0];
$sb = $c[1];

$_SESSION['s_dn2inv'] = 'Y';

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
$dntable = 'ztmp'.$user_id.'_dns';

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$sql = "drop table if exists ".$findb.".".$table;
$db_trd->query($sql);
$db_trd->execute();
$sql = "drop table if exists ".$findb.".".$serialtable;
$db_trd->query($sql);
$db_trd->execute();

$sql = "create table ".$findb.".".$table." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, itemcode varchar(30) default '',item varchar(100) default '',price decimal(16,2) default 0, unit varchar(20) default '',quantity decimal(16,2) default 0,tax decimal(16,2) default 0, value decimal(16,2) default 0, discount decimal(16,2) default 0, disctype char(1) default '', discamount decimal(16,2) default 0, tot decimal(16,2) default 0, taxindex int(11) default 0, taxtype char(3) default '', taxpcent decimal(5,2) default 0, sellacc int(11) default 0, sellbr char(4) default '', sellsub int(4) default 0, purchacc int(11) default 0, purchbr char(4) default '', purchsub int(4) default 0, groupid int(11) default 0, catid int(11) default 0, loc int(11) default 0, stock char(3) default '', avcost decimal(16,2) default 0, forex decimal(16,2) default 0, currency char(3) default '', rate decimal(7,3) default 1  )  engine innodb";
$db_trd->query($sql);
$db_trd->execute();
$sql = "create table ".$findb.".".$serialtable." ( uid int(11) primary key NOT NULL AUTO_INCREMENT PRIMARY KEY, itemcode varchar(30) default '',item varchar(100) default '',serialno varchar(50) default '', locationid int(11) default 0, location varchar(30) default '', ref_no varchar(15) default '', selected char(1) default 'N')  engine innodb";
$db_trd->query($sql);
$db_trd->execute();

$coyid = $_SESSION['s_coyid'];
$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$country = $_SESSION['country'];

// populate trading table with rows from _dns
$db_trd->query("select ref_no, locid from ".$findb.".".$dntable." where selected = 'Y'");
$db_trd->resultset();
$rows = $db_trd->resultset();
foreach ($rows as $row) {
	extract($row);
	$rf = $ref_no;
	$locationid = $locid;
	$db_trd->query("select itemcode,item,price,discount,unit,quantity,tax,value,taxtype,taxpcent,currency,rate from ".$findb.".invtrans where ref_no = '".$rf."'");
	$rowsi = $db_trd->resultset();
	foreach ($rowsi as $rowi) {
		extract($rowi);
		$db_trd->query("insert into ".$findb.".".$table." (itemcode,item,price,discount,unit,quantity,tax,value,taxtype,taxpcent,tot,loc,currency,rate) values (:itemcode,:item,:price,:discount,:unit,:quantity,:tax,:value,:taxtype,:taxpcent,:tot,:loc,:currency,:rate)");
		$db_trd->bind(':itemcode', $itemcode);
		$db_trd->bind(':item', $rf.' ~ '.$item);
		$db_trd->bind(':price', $price);
		$db_trd->bind(':discount', $discount);
		$db_trd->bind(':unit', $unit);
		$db_trd->bind(':quantity', $quantity);
		$db_trd->bind(':tax', $tax);
		$db_trd->bind(':value', $value);
		$db_trd->bind(':taxtype', $taxtype);
		$db_trd->bind(':taxpcent', $taxpcent);
		$db_trd->bind(':tot', $value + $tax);
		$db_trd->bind(':loc', $locationid);
		$db_trd->bind(':currency', $currency);
		$db_trd->bind(':rate', $rate);
		$db_trd->execute();
	}
	
	/*
	// populate serials table with rows from stkserials for the delivery notes
	$db_trd->query("select itemcode,item,serialno,locationid,ref_no from ".$findb.".stkserials where sold = '".$rf."'");
	$rowsk = $db_trd->resultset();
	if (count($rowsk > 0)) {
		foreach ($rowsk as $rowk) {
			extract($rowk);
			$db_trd->query("insert into ".$findb.".".$serialtable." (itemcode,item,serialno,locationid,ref_no) values (:itemcode,:item,:serialno,:locationid,:ref_no)");
			$db_trd->bind(':itemcode', $itemcode);
			$db_trd->bind(':item', $item);
			$db_trd->bind(':serialno', $serialno);
			$db_trd->bind(':locationid', $locationid);
			$db_trd->bind(':ref_no', $ref_no);
			$db_trd->execute();
		}
    }
	*/
}

$currate = $currency.'~'.$rate;

// fill in stock account details for items in trading table
$db_trd->query("select uid,itemcode from ".$findb.".".$table);
$rows = $db_trd->resultset();
foreach ($rows as $row) {
	extract($row);
	$id = $uid;
	$ic = $itemcode;
	$db_trd->query("select sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,stock,avgcost from ".$findb.".stkmast where itemcode = '".$ic."'");
	$rowv = $db_trd->single();
	extract($rowv);
	
	$db_trd->query("update ".$findb.".".$table." set sellacc = :sellacc,sellsub = :sellsub,purchacc = :purchacc,purchsub = :purchsub,groupid = :groupid,catid = :catid,stock = :stock,avcost = :avcost where uid = :id");
	$db_trd->bind(':sellacc', $sellacc);
	$db_trd->bind(':sellsub', $sellsub);
	$db_trd->bind(':purchacc', $purchacc);
	$db_trd->bind(':purchsub', $purchsub);
	$db_trd->bind(':groupid', $groupid);
	$db_trd->bind(':catid', $catid);
	$db_trd->bind(':stock', $stock);
	$db_trd->bind(':avcost', $avgcost);
	$db_trd->bind(':id', $id);
	$db_trd->execute();
}

// get header details
$db_trd->query("select concat(".$cltdb.".members.lastname,' ',".$cltdb.".members.firstname,' ',".$cltdb.".client_company_xref.subname) as account,".$cltdb.".client_company_xref.priceband,".$cltdb.".client_company_xref.client_id, members.member_id as cid from ".$cltdb.".members left join ".$cltdb.".client_company_xref on ".$cltdb.".members.member_id = ".$cltdb.".client_company_xref.client_id where ".$cltdb.".client_company_xref.company_id = ".$coyidno." and ".$cltdb.".client_company_xref.drno = ".$ac." and ".$cltdb.".client_company_xref.drsub = ".$sb);
$row = $db_trd->single();
extract($row);
$acc = $account.'~'.$ac.'~'.$sb.'~'.' ';

// get postal address

$db_trd->query("select addresses.street_no,addresses.ad1,addresses.ad2,addresses.suburb,addresses.town,addresses.postcode,addresses.billing from ".$cltdb.".addresses where addresses.member_id = ".$cid);
$rows = $db_trd->resultset();
$postadd = "";
$add = '';
foreach ($rows as $row) {
	extract($row);
	
	if ($street_no.$ad1 <> '') {
		$add .= str_replace(',',' ',trim($street_no." ".$ad1." ".$ad2));
	}
	if ($suburb <> '') {
		$add .= ','.str_replace(',',' ',trim($suburb));
	}
	if ($town <> '') {
		$add .= ','.str_replace(',',' ',trim($town));
	}
	if ($postcode <> '') {
		$add .= ','.trim($postcode);
	}
	
	if ($billing == 'Y') {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$postadd .= '<option value="'.$add.'"'.$selected.'>'.$add.'</option>';
}				
	

// get delivery address

$db_trd->query("select addresses.street_no,addresses.ad1,addresses.ad2,addresses.suburb,addresses.town,addresses.postcode,addresses.delivery from ".$cltdb.".addresses where addresses.member_id = ".$cid);
$rows = $db_trd->resultset();
$deladd = "";
$add = '';
foreach ($rows as $row) {
	extract($row);
	
	if ($street_no.$ad1 <> '') {
		$add .= str_replace(',',' ',trim($street_no." ".$ad1." ".$ad2));
	}
	if ($suburb <> '') {
		$add .= ','.str_replace(',',' ',trim($suburb));
	}
	if ($town <> '') {
		$add .= ','.str_replace(',',' ',trim($town));
	}
	if ($postcode <> '') {
		$add .= ','.trim($postcode);
	}
	
	if ($delivery == 'Y') {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$deladd .= '<option value="'.$add.'"'.$selected.'>'.$add.'</option>';
}				

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

// get next invoice number
$db_trd->query("select inv from ".$findb.".numbers");
$row = $db_trd->single();
extract($row);
$refno = $inv + 1;
$db_trd->query("update ".$findb.".numbers set inv = :refno");
$db_trd->bind(':refno', $refno);
$db_trd->execute();

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


<script type="text/javascript" src="includes/ajaxgetref.js"></script>
<script type="text/javascript" src="includes/ajaxCheckTransDate.js"></script>

<script>

	window.name = "tr_dn2inv";
	
function SQLdate(dt) {
	var sdt = dt.split('/');
	var d = sdt[0];
	var m = sdt[1];
	var y = sdt[2];
	if (m.length < 2) m = "0" + m;
	if (d.length < 2) d = "0" + d;
	
	var SQLFormatted = "" + y +"-"+ m +"-"+ d;	
	
	return SQLFormatted;
	
}	
	
function nexttrdref(ref) {
	jQuery.ajaxSetup({async:false});
	
	var dt = document.getElementById('newdate').value;
	$.get("includes/ajaxCheckTransDate.php", {dt: dt}, function(data){
			if (data == '') {
				stopProcess = 'N';
				return true;
			} else {
				alert(data);
				document.getElementById('newdate').focus();
				stopProcess = 'Y';
				return false;
			}
	});	
	
	var rno = document.getElementById('refno').value;
	if (rno == 0) {
		$.get("includes/ajaxGetTrdRef.php", {ref: ref}, function(data){
				document.getElementById('refno').value = data;
		});
	}
	
	jQuery.ajaxSetup({async:true});
	
}	

function spostTrdTrans(type) {
	$.get("includes/ajaxRecords2Post.php", {}, function(data){
		if (data == '') {
			spostTrdTrans2(type);
		} else {
			alert(data);
			return false;
		}
	});
}

function spostTrdTrans2(type) {
	jQuery.ajaxSetup({async:false});

	document.getElementById("bPost").disabled = true;


	var ddate = document.getElementById('newdate').value;
	var descript = document.getElementById('description').value;
	var ref = type+document.getElementById('refno').value;
	var trading = document.getElementById('trading').value;
	tradingref = ref;
	var fx = document.getElementById('currency').value;
	
	if (document.getElementById('lstaff') == null) {
		var staffember = '';
	} else {
		var staffmember = document.getElementById('lstaff').value;
	}
	
	var a = document.getElementById('TRaccount').value;
	var as = a.split('~');
	var clt = as[0];
	var acc = as[1];
	var asb = as[2];
	var paymethod = "";
	var yourref = "";
	
	var loc = 0;
	
	var postaladdress = document.getElementById('postadd').value;
	var deliveryaddress = document.getElementById('deliveradd').value;
	
	$.get("includes/ajaxPostTrade.php", {type:type,ddate:ddate,descript:descript,ref:ref,yourref:yourref,acc:acc,asb:asb,loc:loc,postaladdress:postaladdress,deliveryaddress:deliveryaddress,clt:clt,paymethod:paymethod,staffmember:staffmember,forex:fx}, function(data){$("#tradlist").trigger("reloadGrid")});

	document.getElementById('description').value = "";
	document.getElementById('refno').value = 0;
	document.getElementById('TRaccount').value = "";
	
	document.getElementById('postadd').style.visibility = 'hidden';
	document.getElementById('deliveradd').style.visibility = 'hidden';
	document.getElementById('printpage').style.visibility = 'visible';
	
	$.get("includes/ajaxcleardn.php", {}, function(data){
		window.opener.refreshdnsgrid();
	});
	jQuery.ajaxSetup({async:true});
}


function hideprint() {
	document.getElementById('printpage').style.visibility = 'hidden';
}
	
function printtrading(type,rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	window.open('PrintTrading.php?type='+type+'&tradingref='+rf,'plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function emailtrading(type,rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	window.open('PrintTrading.php?type='+type+'&tradingref='+rf+'&doemail=Y','plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
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
  <input type="hidden" name="clientid" id="clientid" value="<?php echo $client_id; ?>">
  <input type="hidden" name="priceband" id="priceband" value="<?php echo $priceband; ?>">
  <input type="hidden" name="setsell" id="setsell" value="0">
  <input type="hidden" name="stock" id="stock" value="">
  <input type="hidden" name="avcost" id="avcost" value="">
  <input type="hidden" name="trackserial" id="trackserial" value="No">
  <input type="hidden" name="gstinvpay" id="gstinvpay" value="<?php echo $gstinvpay; ?>">
  <input type="hidden" name="currency" id="currency" value="<?php echo $currate; ?>">
 
  <table  width="960" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="9"><label style="color: <?php echo $thfont; ?>"><strong>Invoice </strong></label></td>
      <td colspan="4">Staff Member
        <select name="lstaff" id="lstaff"><?php echo $staff_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel" >Date</td>
      <td><div align="left">
          <input type="Text" id="newdate" name="newdate" value="<?php echo $ddateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j,Y">
        </div></td>
      <td class="boxlabel">Description</td>
      <td colspan="7" ><div align="left">
          <input name="description" id="description" type="text" onFocus="this.select()" value="Invoice Delivery Notes" size="50" maxlength="60">
        </div></td>
      <td  class="boxlabel">Reference</td>
      <td><input name="ref" id="ref" type="text" size="5" value="INV" readonly></td>
      <td><input name="refno" type="text" id="refno" size="10" value="<?php echo $refno; ?>" readonly ></td>	
    </tr>
    <tr>
      <td class="boxlabel" >Currency</td>
      <td ><input name="cur" type="text" id="cur" size="10" value="<?php echo $currency; ?>" readonly ></td>
      <td class="boxlabel">Debtor</td>
      <td colspan="7" ><input type="text" name="TRaccount" id="TRaccount" size="45" value="<?php echo $acc; ?>" readonly >        </div></td>
      <td class="boxlabel">&nbsp;</td>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4" class="boxlabelleft" >Post Invoice to:
          <select name="postadd" id="postadd" style="width: 300px !important; min-width: 300px; max-width: 300px;"><?php echo $postadd; ?> </select></td>
      <td colspan="9" class="boxlabelleft" >Deliver Goods to:
        <select name="deliveradd" id="deliveradd" style="width: 300px !important; min-width: 300px; max-width: 300px;"><?php echo $deladd; ?></select></td>
    </tr>
    <tr>
      <td colspan="13"><?php include "gettradingtrans.php"; ?></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="7"><img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
      <td align="right" colspan="6"><input name="bPost" id="bPost" type="button" value="Post" onClick="spostTrdTrans('INV')"></td>
    </tr>
    
  </table>

  
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
