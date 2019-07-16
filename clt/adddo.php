<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);
$coyname = $_SESSION['s_coyname'];
$son = $_REQUEST['son'];
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

$tradetable = 'ztmp'.$user_id.'_dn';

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$tradetable;
$result = mysql_query($query) or die(mysql_error());

$q = "select * from quotes where ref_no = '".$son."'";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$cl = $client.'~'.$accountno.'~'.$sub.'~'.'';
$coyid = $coy_id;
$_SESSION['s_coyid'] = $coyid;
$ddateh = $ddate;
$d = explode('-',$ddate);
$ddate = $d[2].'/'.$d[1].'/'.$d[0];
$desc = $gldesc;
$ref = $invno;
$rf = $ref_no;
$cacc = $client.'~'.$accountno.'~'.$sub.'~'.$branch;
$qnote = $note;
$padd = $postaladdress;
$dadd = $deliveryaddress;
$cname = $coyname;
$cluid = $member_id;

$pb = 1;
$_SESSION['s_findb'] = 'fin'.$subid.'_'.$coyid;


$query = "create table ".$tradetable." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, itemcode varchar(30) default '',item varchar(100) default '',price decimal(16,2) default 0, unit varchar(20) default '',quantity decimal(16,2) default 0,tax decimal(16,2) default 0, value decimal(16,2) default 0, discount decimal(16,2) default 0, disctype char(1) default '', discamount decimal(16,2) default 0, tot decimal(16,2) default 0, taxindex int(11) default 0, taxtype char(3) default '', taxpcent decimal(5,2) default 0, sellacc int(11) default 0, sellbr char(4) default '', sellsub int(4) default 0, purchacc int(11) default 0, purchbr char(4) default '', purchsub int(4) default 0, groupid int(11) default 0, catid int(11) default 0, loc int(11) default 0, note text, sent decimal(16,2) default 0, picked decimal(16,2) default 0)  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$country = $_SESSION['country'];

$ino = 'QOT'.substr($son,3);

$q = "insert into ".$tradetable." (uid,itemcode,item,price,unit,quantity,sent,tax,value,tot,discount,disctype,taxindex,taxtype,taxpcent,note) select uid,itemcode,item,price,unit,quantity,sent,tax,value,(tax+value),discount,disc_type,taxindex,taxtype,taxpcent,note from quotelines where ref_no = '".$ino."'";
$r = mysql_query($q) or die(mysql_error().' '.$q);

$moduledb = 'fin'.$subid.'_'.$coyid;
mysql_select_db($moduledb) or die(mysql_error());

// populate Tax type list
$query = "select * from taxtypes";
$result = mysql_query($query) or die(mysql_error());
$tax_options = "<option value=\"\">Select Tax Type</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$tax_options .= "<option value=\"".$taxpcent."#".$tax."\">".$tax.' - '.$description."</option>";
}

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Delivery Note</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../fin/includes/ajaxgetref.js"></script>
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>
<script>

	window.name = "adddo";


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
					alert(data);
					//document.getElementById('qty').value = 0;
					//document.getElementById('qty').focus();
					return true;
				}
		});
	}
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
	window.open('PrintQuote.php?type='+type+'&tradingref='+rf,'plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function emailtrading(type,rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	window.open('PrintQuote.php?type='+type+'&tradingref='+rf+'&doemail=Y','plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function makedn() {
	var ref = document.getElementById('refno').value;
	  if (confirm("Are you sure you want to dispatch the picked items?")) {
		jQuery.ajaxSetup({async:false});  
		$.get("../ajax/ajaxdnpick.php", {ref: ref}, function(data){alert(data);$("#deliverylist").trigger("reloadGrid")});
		jQuery.ajaxSetup({async:true});
		this.close();
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
  <input type="hidden" name="clientid" id="clientid" value=<?php echo $client_id; ?>>
  <input type="hidden" name="priceband" id="priceband" value=<?php echo $pb; ?>>
  <input type="hidden" name="setsell" id="setsell" value="0">
  <input type="hidden" name="trackserial" id="trackserial" value="No">
  <input type="hidden" name="gstinvpay" id="gstinvpay" value=<?php echo $gstinvpay; ?>>
  <table  width="965" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="14"><label style="color: <?php echo $thfont; ?>"><strong>Quote</strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel" >Date</td>
      <td><div align="left">
          <input type="text" name="newddate" id="newddate" value="<?php echo $ddate; ?>" onChange="ajaxCheckTransDate();">
     <input type="Text" id="newdate" name="newdate" maxlength="25" size="25" value="<?php echo $ddate; ?>" onChange="ajaxCheckTransDate();"><a href="javascript:NewCal('newdate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
        </div></td>
      <td class="boxlabel">Description</td>
      <td colspan="8" ><div align="left">
          <input name="description" id="description" type="text" value="<?php echo $desc; ?>" readonly >
        </div></td>
      <td  class="boxlabel">Reference</td>
      <td><input name="ref" id="ref" type="text" size="5" value="S_O" readonly></td>
      <td><input name="refno" type="text" id="refno" size="10" readonly value="<?php echo $ref; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel" >&nbsp;</td>
      <td class="boxlabelleft" >&nbsp;</td>
      <td class="boxlabel">Client</td>
      <td colspan="8" ><input type="text" name="TRaccount" id="TRaccount" value="<?php echo $cl ?>" size="45" readonly ></td>
      <td class="boxlabel">&nbsp;</td>
      <td colspan="2" >&nbsp;</td>
    </tr>
    <tr>
      <td colspan="14" class="boxlabelleft" ><label>Post Invoice to:
          <input type="text" name="postadd" id="postadd" value="<?php echo $padd ?>" size="45"  readonly >
        &nbsp;&nbsp;&nbsp;&nbsp;
        Deliver Goods to:
        <input type="text" name="deliveradd" id="deliveradd" value="<?php echo $dadd ?>" size="45" readonly ></td>
    </tr>
    <tr>
      <td colspan="14" class="boxlabelleft" ><textarea name="qnote" id="qnote" cols="115" rows="2" readonly><?php echo $qnote; ?></textarea></td>
    </tr>
    <tr>
      <td colspan="14"><?php include "getdeliverynotes.php"; ?></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="7"><img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
      <td align="right" colspan="7"><input name="Submit" type="button" value="Compile Delivery Note" onClick="makedn()"></td>
    </tr>
  </table>
  <div id="printpage" style="position:absolute;visibility:hidden;top:200px;left:400px;height:75px;width:250px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bghead; ?>; border-style:outset;">
    <table width="250">
      <tr bgcolor="<?php echo $bghead; ?>">
        <td colspan="3" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Print Quote</strong></label></td>
      <tr>
        <td><input type="button" name="cancel" id="cancel" value="Close" onClick="hideprint()"></td>
        <td align="center"><input type="button" name="email" id="email" value="Email" onClick="emailtrading('QOT')"></td>
        <td align="right"><input type="button" name="print" id="print" value="Print" onClick="printtrading('QOT')"></td>
      </tr>
    </table>
  </div>

</form>
</body>
</html>
