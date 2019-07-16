<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);
$cluid = $_SESSION["s_memberid"];
$coy = $_REQUEST['cid'];
$c = explode('~',$coy);
$_SESSION['s_coyid'] = $c[0];
$_SESSION['s_coyname'] = $c[1];
$coyid = $c[0];
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
$subscriber = $subid;
$sname = $row['uname'];

$_SESSION['s_findb'] = 'infinint_fin'.$subid.'_'.$coyid;

$tradetable = 'ztmp'.$user_id.'_quote';

$cltdb = $_SESSION['s_cltdb'];

$db->query("drop table if exists ".$cltdb.".".$tradetable);
$db->execute();

$db->query("create table ".$cltdb.".".$tradetable." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, itemcode varchar(30) default '',item varchar(100) default '',price decimal(16,2) default 0, unit varchar(20) default '',quantity decimal(16,2) default 0,tax decimal(16,2) default 0, value decimal(16,2) default 0, discount decimal(16,2) default 0, disctype char(1) default '', discamount decimal(16,2) default 0, tot decimal(16,2) default 0, taxindex int(11) default 0, taxtype char(3) default '', taxpcent decimal(5,2) default 0, sellacc int(11) default 0, sellbr char(4) default '', sellsub int(4) default 0, purchacc int(11) default 0, purchbr char(4) default '', purchsub int(4) default 0, groupid int(11) default 0, catid int(11) default 0, loc int(11) default 0, note text, currency char(3) default '', rate decimal(7,3) default 1 )  engine myisam");
$db->execute();

$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");

$db->query("select concat(members.firstname,' ',members.lastname,' ',client_company_xref.subname) as account,client_company_xref.priceband,client_company_xref.drno as accountno,client_company_xref.drsub as sub,client_company_xref.client_id from ".$cltdb.".members left join ".$cltdb.".client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.company_id = ".$coyid." and client_company_xref.drno != '' and client_company_xref.blocked = 'No' and members.member_id = ".$cluid);

$row = $db->single();
if (!empty($row)) {
	extract($row);
} else {
	$db->query("select concat(members.firstname,' ',members.lastname) as account from ".$cltdb.".members where members.member_id = ".$cluid);
	$row = $db->single();
	extract($row);
	$accountno = 0;
	$sub = 0;
	$priceband = 1;
}
$cl = trim($account).'~'.$accountno.'~'.$sub.'~'.'';

$findb = $_SESSION['s_findb'];

// populate Tax type list
$db->query("select * from ".$findb.".taxtypes");
$rows = $db->resultset();
$tax_options = "<option value=\"\">Select Tax Type</option>";
foreach ($rows as $row) {
	extract($row);
	$tax_options .= "<option value=\"".$taxpcent."#".$tax."\">".$tax.' - '.$description."</option>";
}

// populate forex list
$db->query("select * from ".$findb.".forex");
$rows = $db->resultset();
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
$db->query("select currency, rate from ".$findb.".forex where def_forex = 'Yes'");
$row = $db->single();
extract($row);
$_SESSION['s_localcurrency'] = $currency;
$fx = $currency.'~'.$rate;

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Quote</title>
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
<script type="text/javascript" src="includes/ajaxCheckTransDate.js"></script>
<script>

	window.name = "addquote";

function calcPriceForex() {
	$.get("../fin/includes/ajaxGetLocalCurrency.php", {}, function(data){
		var pr = document.getElementById('price').value;	
		var e = document.getElementById("currency");
		var fx = e.options[e.selectedIndex].value;
		var f = fx.split('~');
		var fxc = f[0];
		var fxr = f[1];
		var lcur = data;
		if (lcur != fxc) {
			var lamt = pr * fxr;
			ntr = Math.round(lamt*100)/100;
			ntf = ntr.toFixed(2);				
			document.getElementById('fxamt').value = ntf;
		}
	});	
}

function checkserial(trd) {
	var loc = 1;
	if (loc == 0) {
		alert('Please select a location');
		document.getElementById('loc').focus();
		return false;
	}
	addtrdtrans();
}

function CheckAvailable(q) {

	var is = document.getElementById('stkitem').value;
	var locid = 1;
	var i = is.split('~');
	var stkid = i[0];

	if (q != 0) {
		$.get("../fin/includes/ajaxCheckAvailable.php", {q:q, stkid:stkid, locid:locid}, function(data){
				if (data == '') {
					return true;
				} else {
					alert("Warning - there are insufficient in stock to meet this quote");
					//document.getElementById('qty').value = 0;
					//document.getElementById('qty').focus();
					return true;
				}
		});
	}

}

function addtrdtrans() {
	var tradingtype = document.getElementById('trading').value;
	
	var fx = document.getElementById('currency').value;
	var f = fx.split('~');
	var fxcode = f[0];
	var fxrate = f[1];
	var fxamt = document.getElementById('fxamt').value;
	
	var is = document.getElementById('stkitem').value;
	var i = is.split('~');
	var stkid = i[0];
	var stkitem = i[1];
	var sa = document.getElementById('sacc').value;
	var s = sa.split('~');
	var sac = s[0];
	var sbr = s[1];
	var ssb = s[2];
	var sa = document.getElementById('pacc').value;
	var p = sa.split('~');
	var pac = p[0];
	var pbr = p[1];
	var psb = p[2];
	var stkprice = document.getElementById('price').value;
	var stkunit = document.getElementById('unit').value;
	var stkqty = document.getElementById('qty').value;
	var tx = document.getElementById('tax').value;
	if (tx == '') {
		var tpcent = 0;
		var taxtype = "";
		var taxindex = 	0;
	} else {
		var t = tx.split('#');
		var tpcent = t[0];
		var taxtype = t[1];
		var taxindex = 	document.getElementById('tax').selectedIndex;
	}
	var grp = document.getElementById('grp').value;
	var cat = document.getElementById('cat').value;
	var priceband = document.getElementById('priceband').value;	
	var discount = document.getElementById('disc').value;
	var disctype = document.getElementById('disctype').value;
	var setsell = document.getElementById('setsell').value;
	var lnote = document.getElementById('lnote').value;
	
	var acct = document.getElementById('TRaccount').value;
	if (acct == '') {
		alert('Please select an account');
		document.getElementById('TRaccount').focus();
		return false;
	}
					 

	var sitem = document.getElementById('stkitem').value;
	if (sitem == '') {
		alert('Please select a stock item');
		document.getElementById('stkitem').focus();
		return false;
	}
	
	var refno = document.getElementById('refno').value;
	if (refno == 0) {
		alert('Please select a reference number');
		document.getElementById('refno').focus();
		return false;
	}
	
	var qt = document.getElementById('qty').value;
	if (qt == 0) {
		alert('Please select a quantity');
		document.getElementById('qty').focus();
		return false;
	}
	var lnote = document.getElementById('lnote').value;
	
	$.get("includes/ajaxAddQuoteTrans.php", {stkid:stkid,stkitem:stkitem,stkprice:stkprice,stkunit:stkunit,stkqty:stkqty,tpcent:tpcent,taxindex:taxindex,taxtype:taxtype,sac:sac,sbr:sbr,ssb:ssb,pac:pac,pbr:pbr,psb:psb,grp:grp,cat:cat,priceband:priceband,discount:discount,disctype:disctype,setsell:setsell,lnote:lnote,fxcode:fxcode,fxrate:fxrate,fxamt:fxamt}, function(data){$("#qlist").trigger("reloadGrid")});

	document.getElementById('stkitem').value = '';
	document.getElementById('price').value = 0;
	document.getElementById('unit').value = '';
	document.getElementById('qty').value = 0;
	document.getElementById('tax').selectedIndex = 0;
	document.getElementById('disc').value = 0;
	document.getElementById('sacc').value = '';
	document.getElementById('pacc').value = '';
	document.getElementById('grp').value = 0;
	document.getElementById('cat').value = 0;
	document.getElementById('setsell').value = 0;
	document.getElementById('lnote').value = '';
	document.getElementById('fxamt').value = '';

}

function editlineitem(uid) {

	$.get("includes/ajaxgetquoteline.php", {lineno: uid}, function(data){
		var ln = data.split('~');
		var itemcode = ln[0];
		var stkitem = ln[1];
		var price = ln[2];
		var qty = ln[3];
		var taxindex = ln[6];
		var sac = ln[7];
		var sbr = ln[8];
		var ssb = ln[9];
		var pac = ln[10];
		var pbr = ln[11];
		var psb = ln[12];
		var grp = ln[13];
		var cat = ln[14];
		var stkunit = ln[15];
		var disc = ln[16];
		var disctype = ln[17];
		var lnote = ln[18];
		
	
		document.getElementById('stkitem').value = itemcode+'~'+stkitem;
		document.getElementById('price').value = price;
		document.getElementById('unit').value = stkunit;
		document.getElementById('qty').value = qty;
		document.getElementById('tax').selectedIndex = taxindex;
		document.getElementById('disctype').options[document.getElementById('disctype').selectedIndex].value = disctype;
		document.getElementById('disc').value = disc;
		document.getElementById('sacc').value = sac+'~'+sbr+'~'+ssb;
		document.getElementById('pacc').value = pac+'~'+pbr+'~'+psb;
		document.getElementById('grp').value = grp;
		document.getElementById('cat').value = cat;
		document.getElementById('lnote').value = lnote;
		
		$.get("includes/ajaxdelquoteline.php", {tid: uid}, function(data){$("#qlist").trigger("reloadGrid")});
		
	});
	
}

function dellineitem(uid) {
	$.get("includes/ajaxdelquoteline.php", {tid: uid}, function(data){$("#qlist").trigger("reloadGrid")});
}

function nexttrdref(ref,add) {
	$.get("../fin/includes/ajaxGetTrdRef.php", {ref: ref, add: add}, function(data){
			document.getElementById('refno').value = data;
	});
}

function transvisibledr() {
		var source = document.getElementById('trading').value;
		$.get("../ajax/ajaxUpdtSource.php", {source: source}, function(data){
		});
		document.getElementById('drselect').style.visibility = 'visible';											
		document.getElementById('searchdr').value = "";
		document.getElementById('searchdr').focus();
}




function blanklist(ad) {
	if (ad == 'post') {
		// remove all entries from postal address list
		var x=document.getElementById("postadd");
		var listlength = document.getElementById("postadd").length;
		for (var i = 0; i < listlength; i ++) {
			x.remove(x[i]);
		}
	} else {
		// remove all entries from delivery address list
		var x=document.getElementById("deliveradd");
		var listlength = document.getElementById("deliveradd").length;
		for (var i = 0; i < listlength; i ++) {
			x.remove(x[i]);
		}
	}
}

function setstkselect(stk) {
	var a = stk.split('~');
	var scode = a[0];
	var sname = a[1];
	var sunit = a[2];
	var stax = a[3];
	var sac = a[4];
	var sbr = a[5];
	var ssb = a[6];
	var pac = a[7];
	var pbr = a[8];
	var psb = a[9];
	var grp = a[10];
	var cat = a[11];
	var cost = a[12];
	var setsell = a[13];
	var trackserial = a[14];
	var staxpcent = a[15];
	var s = scode+'~'+sname;
	var trading = document.getElementById('trading').value;
	var gstinvpay = document.getElementById('gstinvpay').value;
	var priceband = document.getElementById('priceband').value;
	
	document.getElementById('stkitem').value = s;
	document.getElementById('sacc').value = sac+'~'+sbr+'~'+ssb;
	document.getElementById('pacc').value = pac+'~'+pbr+'~'+psb;
	document.getElementById('grp').value = grp;
	document.getElementById('cat').value = cat;
	document.getElementById('trackserial').value = trackserial;
	
	document.getElementById('tax').selectedIndex = stax;
	document.getElementById('unit').value = sunit;
	document.getElementById('setsell').value = cost;
	
	if (setsell > 0 ) {
			document.getElementById('price').value = setsell;
	} else {
		$.get("../fin/includes/ajaxgetpricepcent.php", {priceband: priceband}, function(data){
			var addpcent = data;	
			var sellat = cost * (1 + addpcent/100);
			sellat = parseFloat(sellat).toFixed(2);
			document.getElementById('price').value = sellat;
			calcPriceForex();
		});
	}
	

	document.getElementById('stkselect').style.visibility = 'hidden';
	document.getElementById('stkitem').style.visibility = 'visible';
}

function sboxhidestk() {
	document.getElementById('stkselect').style.visibility = 'hidden';											
}

function stkvisible() {
		document.getElementById('stkselect').style.visibility = 'visible';											
		document.getElementById('searchstk').value = "";
		document.getElementById('searchstk').focus();
}

function gridReload1stk(){ 
	var cr_mask = jQuery("#searchstk").val(); 
	cr_mask = cr_mask.toUpperCase();
	jQuery("#selectstklist").setGridParam({url:"../fin/selectstk.php?cr_mask="+cr_mask}).trigger("reloadGrid"); 
} 

function doSearchstk(){ 
	var timeoutHnd = setTimeout(gridReload1stk,500); 
} 

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

function setfx() {
	var fx = document.getElementById('currency').value;	
	var f = fx.split('~');
	var fxc = f[0];
	$.get("../fin/includes/ajaxGetLocalCurrency.php", {}, function(data){
		var lcur = data;
		if (lcur != fxc) {
			document.getElementById('fxcode').value = fxc;
			document.getElementById('localcode').value = lcur;
			document.getElementById('currency').disabled = 'disable';
		} else {
			document.getElementById('localcode').value = lcur;
			document.getElementById('fxcode').value = "";
			document.getElementById('currency').disabled = 'disable';
		}
		
	});	
	
}

var tradingref;

function postTrdTrans() {
	var ddate = document.getElementById('newdate').value;
	var descript = document.getElementById('description').value;
	var ref = 'QOT'+document.getElementById('refno').value;
	var trading = document.getElementById('trading').value;
	tradingref = ref;
	var fx = document.getElementById('currency').value;

	var a = document.getElementById('TRaccount').value;
	var as = a.split('~');
	var clt = as[0];
	var acc = as[1];
	var asb = as[2];
	var paymethod = "";
	
	var loc = 1;
	var postaladdress = document.getElementById('postadd').value;
	var deliveryaddress = document.getElementById('deliveradd').value;
	var qnote = document.getElementById('qnote').value;
	var act = 'add';
	
	$.get("includes/ajaxPostQuote.php", {act:act,ddate:ddate,descript:descript,ref:ref,acc:acc,asb:asb,loc:loc,postaladdress:postaladdress,deliveryaddress:deliveryaddress,clt:clt,paymethod:paymethod,qnote:qnote,forex:fx}, function(data){$("#qlist").trigger("reloadGrid")});
	
	document.getElementById('description').value = "";
	document.getElementById('refno').value = 0;
	document.getElementById('TRaccount').value = "";
	document.getElementById('qnote').value = "";
	document.getElementById('postadd').style.visibility = 'hidden';
	document.getElementById('deliveradd').style.visibility = 'hidden';
	document.getElementById('printpage').style.visibility = 'visible';
	window.open("","editmembers").jQuery("#mquotelist").trigger("reloadGrid");	
	
}

function hideprint() {
	document.getElementById('printpage').style.visibility = 'hidden';
	this.close();
}

function printtrading(type,rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	window.open('PrintQuote.php?type='+type+'&rf='+rf,'plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function emailtrading(type,rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	window.open('PrintQuote.php?type='+type+'&rf='+rf+'&doemail=Y','plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addstock() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('../fin/st_additem.php','stadd','toolbar=0,scrollbars=1,height=470,width=980,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);

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
  <input type="hidden" name="priceband" id="priceband" value=<?php echo $priceband; ?>>
  <input type="hidden" name="setsell" id="setsell" value="0">
  <input type="hidden" name="trackserial" id="trackserial" value="No">
  <input type="hidden" name="gstinvpay" id="gstinvpay" value=<?php echo $gstinvpay; ?>>
  <input type="hidden" name="currency" id="currency" value=<?php echo $fx; ?>>
  <table  width="950" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="13"><label style="color: <?php echo $thfont; ?>"><strong>Quote</strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel" >Date</td>
      <td><div align="left">
          <input type="Text" id="newdate" name="newdate" maxlength="25" size="25" value="<?php echo $ddateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y">
        </div></td>
      <td class="boxlabel">Description</td>
      <td colspan="7" ><div align="left">
          <input name="description" id="description" type="text" onFocus="this.select()" size="50" maxlength="60">
        </div></td>
      <td  class="boxlabel">Reference</td>
      <td><input name="ref" id="ref" type="text" size="5" value="QOT" readonly></td>
      <td><input name="refno" type="text" id="refno" size="10" value="0" onFocus="nexttrdref('qot','Y')"></td>
    </tr>
    <tr>
      <td class="boxlabel" >Currency</td>
      <td ><select name="currency" id="currency" onchange="setfx();"><?php echo $forex_options;?></select></td>
      <td class="boxlabel">Client</td>
      <td colspan="8" ><input type="text" name="TRaccount" id="TRaccount" value="<?php echo $cl ?>" size="45" readonly ></td>
      <td class="boxlabel">&nbsp;</td>
      <td colspan="1" >&nbsp;</td>
    </tr>
    <tr>
      <td colspan="13" class="boxlabelleft" ><label>Post Invoice to:
          <select name="postadd" id="postadd">
          </select>
        </label>
        &nbsp;&nbsp;&nbsp;&nbsp;
        Deliver Goods to:
        <select name="deliveradd" id="deliveradd">
        </select></td>
    </tr>
    <tr>
      <td colspan="13" class="boxlabelleft" ><textarea name="qnote" id="qnote" cols="115" rows="2" placeholder="Enter note that applies to the entire quote ..."></textarea></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="13"><label style="color: <?php echo $thfont; ?>"><strong>Add an Item </strong></label>
        &nbsp;&nbsp;&nbsp;Barcode&nbsp;
        <input type="text" name="stkbarcode" id="stkbarcode" size="30" onkeypress="findbcode()" onpaste="findbcode()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="button" name="baddstock" id="baddstock" value="Add unstocked item" onClick="addstock()"></td>
    </tr>
    
    <tr>
      <td colspan="13" class="boxlabelleft" >Item
        <input type="text" name="stkitem" id="stkitem" size="35" readonly >
        <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onclick="stkvisible()"><input type="text" name="localcode" id="localcode" size="5" value="Price" readonly>
        <input type="text" name="price" id="price" size="7" onfocus="this.select();" onChange="calcPriceForex();">
              per
        <input type="text" name="unit" id="unit" size="5" readonly>
            Disc.
        <label>
          <input type="text" name="disc" id="disc" size="5" value="0" onfocus="this.select();">
           </label>
        <select name="disctype" id="disctype">
          <option value="%">%</option>
          <option value="$">$</option>
        </select>
             Qty
        <input type="text" name="qty" id="qty" size="7" value="0" onfocus="this.select();" onBlur="CheckAvailable(this.value)">
        <select name="tax" id="tax">
          <?php echo $tax_options; ?>
        </select></td>
    </tr>
    <tr>
      <td  colspan="13" class="boxlabelleft" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input type="text" name="fxcode" id="fxcode" size="5" value = "" readonly>
      <input type="text" name="fxamt" id="fxamt" size="10" value = "" readonly>
      &nbsp;</td>
    </tr>
    <tr>
      <td colspan="12" class="boxlabelleft" >
        <textarea name="lnote" id="lnote" cols="100" rows="2" placeholder="Enter note for this line item ..."></textarea></td>
      <td align="right"><input type="button" value="Add" name="save"  onClick="checkserial('inv')" ></td>
    </tr>
    <tr>
      <td colspan="13"><?php include "getquotetrans.php"; ?></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="7"><img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
      <td align="right" colspan="6"><input name="Submit" type="button" value="Create" onClick="postTrdTrans()"></td>
    </tr>
  </table>
  <div id="stkselect" style="position:absolute;visibility:hidden;top:156px;left:80px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchstk" size="50" onkeypress="doSearchstk()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="stkclose" onclick="sboxhidestk()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "../fin/selectstk.php"; ?></td>
      </tr>
    </table>
  </div>
  <div id="printpage" style="position:absolute;visibility:hidden;top:200px;left:400px;height:75px;width:250px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bghead; ?>; border-style:outset;">
    <table width="250">
      <tr bgcolor="<?php echo $bghead; ?>">
        <td colspan="3" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Print Quote</strong></label></td>
      <tr>
        <td><input type="button" name="cancel" id="cancel" value="Close" onClick="hideprint()"></td>
       <!-- <td align="center"><input type="button" name="email" id="email" value="Email" onClick="emailtrading('QOT')"></td> -->
        <td align="right"><input type="button" name="print" id="print" value="Print" onClick="printtrading('QOT')"></td>
      </tr>
    </table>
  </div>
  <script>
	//********************************************************
	// populate billing and delivery addresses
	//*********************************************************
	var cid = <?php echo $cluid; ?>;
		blanklist('post');
		$.get("../fin/includes/ajaxGetPostAdd.php", {cid:cid}, function(data){
				$("#postadd").append(data);
		});
		blanklist('delivery');
		$.get("../fin/includes/ajaxGetDeliveryAdd.php", {cid:cid}, function(data){
				$("#deliveradd").append(data);
		});
	// Upgrade Lead to Prospect
		$.get("includes/ajaxLead2Prospect.php", {cid:cid}, function(data){
		});
	

</script>

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
