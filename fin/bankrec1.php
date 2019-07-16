<?php
session_start();
$usersession = $_SESSION['usersession'];
$tdate = $_REQUEST['ddate'];
$bkno = $_REQUEST['bankno'];
$b = explode('~',$bkno);
$bankno = $b[0];
$bankbr = $b[1];
$banksb = $b[2];
$bankname = $b[3];

$_SESSION['s_showreconcilled'] = 'N';

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];;

$bankrectable = 'ztmp'.$user_id.'_bankrec';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$bankrectable);
$db->execute();

$heading = 'Bank Reconcilliation for '.$bankname.' as at '.$tdate;

$db->query("create table ".$findb.".".$bankrectable." (uid integer(11), ddate date default '0000-00-00', debit decimal(16,2) default 0, credit decimal(16,2) default 0, reference varchar(45) default '', description varchar(45) default '', reconciled char(1) default 'N') engine myisam"); 
$db->execute();

// get bank balance at statement date
$db->query("select sum(debit - credit) as tot from ".$findb.".trmain where ddate <= '".$tdate."' and accountno = ".$bankno." and branch = '".$bankbr."' and sub = ".$banksb);
$row = $db->single();
extract($row);
$nbanktot = $tot;

// populate bank rec table
$db->query("select uid as id,ddate as dt,debit as dr,credit as cr,reference as rf, descript1 as ds, reconciled as tr from ".$findb.".trmain where ddate <= '".$tdate."' and reconciled != 'Y' and accountno = ".$bankno." and branch = '".$bankbr."' and sub = ".$banksb." order by ddate");
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query('insert into '.$findb.'.'.$bankrectable.' (uid,ddate,debit,credit,reference,description,reconciled) values ('.$id.',"'.$dt.'",'.$dr.','.$cr.',"'.$rf.'","'.$ds.'","'.$tr.'")');
	$db->execute(); 
}

// get unreconciled balance
$db->query("select sum(debit - credit) as tot from ".$findb.".".$bankrectable." where reconciled = 'N'");
$row = $db->single();
extract($row);
$unrecon = $tot;

$db->closeDB();

date_default_timezone_set($_SESSION['s_timezone']);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Bank Reconciliation</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>

<script>

window.name = "bankrec";

function toFixed(val) {
	if (isNaN(val)) {
		alert('Must be a number');
	} else {
		return parseFloat(val).toFixed(2);
	}
}

function recon(id) {
	$.get("includes/ajaxreconcile.php", {tid: id}, function(data){
	var amt = data.split('~');
	var dr = parseFloat(amt[0]);
	var cr = parseFloat(amt[1]);
	var bnk = document.getElementById('coybal').value;
	var dif = parseFloat(document.getElementById('diff').value);
	document.getElementById('diff').value = toFixed(dif + dr - cr);
	var unrec = parseFloat(document.getElementById('balance').value);
	document.getElementById('balance').value = toFixed(unrec - dr + cr);
	$("#bankreclist").trigger("reloadGrid")});
}

function unrecon(id) {
	$.get("includes/ajaxunreconcile.php", {tid: id}, function(data){
	var amt = data.split('~');
	var dr = parseFloat(amt[0]);
	var cr = parseFloat(amt[1]);
	var bnk = document.getElementById('coybal').value;
	var dif = parseFloat(document.getElementById('diff').value);
	document.getElementById('diff').value = toFixed(dif + dr - cr);
	var unrec = parseFloat(document.getElementById('balance').value);
	document.getElementById('balance').value = toFixed(unrec + dr - cr);
	$("#bankreclist").trigger("reloadGrid")});
}

function showall() {
	$.get("includes/ajaxshowrecon.php", {}, function(data){$("#bankreclist").trigger("reloadGrid")});
}

function hiderecon() {
	$.get("includes/ajaxhiderecon.php", {}, function(data){$("#bankreclist").trigger("reloadGrid")});
}

function saverecon() {
	$.get("includes/ajaxsaverecon.php", {}, function(data){});
	this.close();
}

function restore() {
	$.get("includes/ajaxrestorerecon.php", {}, function(data){$("#bankreclist").trigger("reloadGrid")});
}

function commitrecon() {
	var unrecon = toFixed(parseFloat(document.getElementById('balance').value));
	var cbal = document.getElementById('coybal').value;
	var sbal = document.getElementById('statbal').value;
	var torecon = toFixed(parseFloat(cbal) - parseFloat(sbal));
	var tdate = document.getElementById('ddate').value;
	//if (torecon < 0) {
		//torecon = torecon * -1;
	//}
	//if (unrecon < 0) {
		//unrecon = unrecon * -1;
	//}
	
	if (unrecon == torecon) {
	
		$.get("includes/ajaxcommitrecon.php", {tdate:tdate}, function(data){
			var bankbalance = document.getElementById('statbal').value;	
			if (bankbalance == "") {bankbalance = 0;}
			var reconbal = data.split('\\');
			var rb = reconbal[0];
			var bank = parseFloat(bankbalance) + parseFloat(rb);	
			var coy = document.getElementById('coybal').value;
			var diff = bank - parseFloat(coy);
			var rdate = "<?php echo $tdate; ?>";
			
			var oldwin = window.open('bankrec1.php','bkrec');
			oldwin.close();
			var x = 0, y = 0; // default values	
			x = window.screenX +5;
			y = window.screenY +200;
			window.open('rep_bankrec2pdf.php?rdate='+rdate+'&bankbal='+bankbalance+'&unrec='+rb+'&coybal='+coy,'brpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	});
	
	} else {
		var diff = toFixed(unrecon - torecon);
		if (diff < 0) {
			diff = diff * -1;
		}
		
		alert('Your balances do not reconile by '+diff+'. Please check for errors.');
		return false;
	}
	
}

function calcdiff() {
	var bnk = document.getElementById('coybal').value;
	var stm = document.getElementById('statbal').value;
	var dif = toFixed(parseFloat(bnk) - parseFloat(stm));
	document.getElementById('diff').value = dif;
	
}

</script>


</head>

<body>

<form name="bankrec" method="post" >
 <input type="hidden" name="ddate" id="ddate" value="<?php echo $tdate; ?>">
 <input type="hidden" name="diff" id="diff" value="0">

  <table width="1000" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="8" align="center"><strong><?php echo $heading ?></strong></td>
    </tr>
	<tr>
	  <td class="boxlabel">Accounts Bank Balance</td>
	  <td> 
	    <input  type="text" name="coybal" id="coybal" size="15" value="<?php echo $nbanktot; ?>" readonly>
      </td>
	  <td  class="boxlabel">Statement Closing Balance</td>
	  <td><input type="text" name="statbal" id="statbal" value="0" onFocus="this.select()" size="15" onBlur="calcdiff()"></td>
	  <td class="boxlabel">&nbsp;</td>
	  <td>&nbsp;</td>
	  <td class="boxlabel">Unreconciled</td>
	  <td><input type="text" name="balance" id="balance" value="<?php echo $unrecon; ?>" readonly size="15"></td>
	</tr>
    <tr>
    <td colspan="8"><?php include "getbankrec.php"; ?></td>
    </tr>
	</table>	
	
 	 <table width="1000" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="<?php echo $bgcolor; ?>">
     <tr>
      <td><input type="button" name="save" value="Exit - Save for Later" onClick="saverecon()"></td>
      <td><input type="button" name="brestore" id="brestore" value="Restore from Saved" onclick="restore()"></td>
      <td><input type="button" name="bshowall" id="bshowall" value="Show All" onClick="showall()"></td>
      <td><input type="button" name="bhide" id="bhide" value="Hide Reconciled" onClick="hiderecon()"></td>
      <td align="right"><input name="commit" type="button" value="Commit" onClick="commitrecon()"></td>
     </tr>
  </table>
</form>

<script>
	document.getElementById('statbal').focus();
</script>

</body>
</html>
