<?php
session_start();
$usersession = $_SESSION['usersession'];

$coyidno = $_SESSION['s_coyid'];
$ddate = $_SESSION['s_ddate'];
$ref = $_SESSION['s_ref'];
$acc = $_SESSION['s_acc'];
$asb = $_SESSION['s_asb'];
$loc = $_SESSION['s_loc'];
$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db_po = new DBClass();

$db_po->query("select * from sessions where session = :vusersession");
$db_po->bind(':vusersession', $usersession);
$row = $db_po->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_grn2po';

$sql = "drop table if exists ".$findb.".".$table;
$db_po->query($sql);
$db_po->execute();

$db_po->query("create table ".$findb.".".$table." ( uid int(11), ddate date, item varchar(100) default '',itemcode varchar(20) default '', ref_no char(15) default '', unit varchar(20) default '', quantity decimal (9,3) default 0, supplied decimal (9,3) default 0, thisgrn decimal (9,3) default 0)  engine myisam");
$db_po->execute();

$db_po->query("select distinct itemcode from ".$findb.".ztmp".$user_id."_trading");
$rows = $db_po->resultset();
foreach ($rows as $row) {
	extract($row);
	$db_po->query("select l.uid,h.ddate,l.item,l.itemcode,l.ref_no,l.unit,l.quantity,l.supplied,0 from ".$findb.".p_olines l inner join ".$findb.".p_ohead h on l.ref_no = h.ref_no and l.supplier = ".$acc." and l.sub = ".$asb." and l.itemcode = '".$itemcode."' where (l.quantity - l.supplied) > 0");
	$items = $db_po->resultset();
	$db_po->query("insert into ".$findb.".".$table." (uid,ddate,item,itemcode,ref_no,unit,quantity,supplied,thisgrn) values (:uid,:ddate,:item,:itemcode,:ref_no,:unit,:quantity,:supplied,:thisgrn)");
	foreach ($items as $it) {
		extract($it);
		$db_po->bind(':uid', $uid);
		$db_po->bind(':ddate', $ddate);
		$db_po->bind(':item', $item);
		$db_po->bind(':itemcode', $itemcode);
		$db_po->bind(':ref_no', $ref_no);
		$db_po->bind(':unit', $unit);
		$db_po->bind(':quantity', $quantity);
		$db_po->bind(':supplied', $supplied);
		$db_po->bind(':thisgrn', 0);
		
		$db_po->execute();
		
	}
}

$db_po->query("select itemcode, quantity from ".$findb.".ztmp".$user_id."_trading"); 
$rows = $db_po->resultset();
foreach ($rows as $row) {
	extract($row);
	$ic = $itemcode;
	$qt = $quantity;
	$db_po->query("select uid,itemcode,quantity - supplied as need from ".$findb.".".$table." where itemcode = '".$ic."' order by ddate");
	$items = $db_po->resultset();
	foreach ($items as $it) {
		extract($it);
		$id = $uid;
		if ($qt > 0) {
			if ($qt >= $need) {
				$db_po->query("update ".$findb.".".$table." set thisgrn = ".$need." where uid = ".$id);
				$db_po->execute();
				$qt = $qt - $need;
			} else {
				$db_po->query("update ".$findb.".".$table." set thisgrn = ".$qt." where uid = ".$id);
				$db_po->execute();
				$qt = 0;
			}
		}
	}
}

// if no relevant PO's empty trading table and close this form.
$db_po->query("select * from ".$findb.".".$table);
$rows = $db_po->resultset();
$numrows = $db_po->rowCount();
if ($numrows == 0) {
	echo '<script>';
	echo 'alert("There are no outstanding Purchce Orders against this supplier");';
	echo 'this.close();';
	echo '</script>';
	
	$db_po->query("delete from ".$findb.".ztmp".$user_id."_trading");
	$db_po->execute();	
	
}


require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

$db_po->closeDB();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Allocate GRN to PO</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>

<script type="text/javascript">

window.name = "allocGrn_Po";

function grn2po() {
	jQuery.ajaxSetup({async:false});
	$.get("includes/ajaxAllocgrn2po.php", {}, function(data){});
	$.get("includes/ajaxDelg2p.php", {}, function(data){});
	window.opener.dogrid();
	jQuery.ajaxSetup({async:true});
	this.close();
}

function tempgrn2po(id,refno,need) {
	document.getElementById('changealloc').style.visibility = 'visible';
	document.getElementById('lid').value = id;
	document.getElementById('lref').value = refno;
	document.getElementById('lneed').value = need;
}

function changealloc() {
	var id = document.getElementById('lid').value;
	var refno = document.getElementById('lref').value;
	var amt = document.getElementById('amount').value;
	chgalloc(id,amt,refno);
	document.getElementById('changealloc').style.visibility = 'hidden';
	document.getElementById('amount').value = 0;
}

function chgalloc(id,amt,refno) {
	var need = document.getElementById('lneed').value;
	
	if (parseFloat(amt) > parseFloat(need)) {
		alert('You are trying to allocate more items than those required');
		return false;
	}
	
	$.get("includes/ajaxchgalloc.php", {id: id, refno: refno, amt: amt}, 
	function(data){$("#polist").trigger("reloadGrid");
		  });
	document.getElementById('changealloc').style.visibility = 'hidden';
	
}


</script>
</head>
<form name="g2p" id="g2p">
  <input type="hidden" name="lid" id="lid" value="0">
  <input type="hidden" name="lref" id="lref" value="">
  <input type="hidden" name="lneed" id="lneed" value="">
<body>
  <table width="850" border="0" align="center">
    <tr>
      <td colspan="2"><?php include "getPOlisting.php"; ?></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
      <td align="right"><input type="button" name="balloc" id="balloc" value="Allocate" onclick="grn2po()" /></td>
    </tr>
  </table>
  
  
<div id="changealloc" style="position:absolute;visibility:hidden;top:400px;left:500px;height:75px;width:250px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bghead; ?>; border-style:outset;">
  <table width="250">
        <tr bgcolor="<?php echo $bghead; ?>">
        <td colspan="3" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Quantity to allocate</strong></label></td>
    </tr>
    <tr>
      <td>Quantity<input type="text" name="amount" id="amount" value="0" onFocus="this.select()"></td>
      <td align="right"><input type="button" name="bamount" id="bamount" value="Save" onClick="changealloc()"></td>
    </tr>
    </table>
  
</div>    
  
</form>  
</body>
</html>