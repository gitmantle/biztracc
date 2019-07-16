<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];

$db->query('select * from '.$findb.'.stklocs order by location');
$rows= $db->resultset();
// populate location list
$loc_options = "<option value=\"\">Select Location</option>";
foreach ($rows as $row) {
	extract($row);
	$loc_options .= "<option value=\"".$uid."\">".$location."</option>";
}

$q = "select invno,gldesc,accountno,sub,postaladdress,deliveryaddress,client from quotes where ref_no = '".$dref."'";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

// find last invoice for this sales order and increment suffix by 1
$q = "select max(ref_no) as mr from invhead where ref_no like 'INV%' and substring(ref_no,4) = ".$invno;
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);
if ($mr != '') {
	$no = explode('-',$mr);
	$n1 = $no[1];
	$n2 = $n1 + 1;
} else {
	$n2 = 1;
}

$inno = 'INV'.$invno.'-'.$n2;


$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());


$ddateh = date("Y-m-d");

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Create Invoice</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script>

	window.name = "creatinv";

function postinv() {
	var aprint = jQuery("#dn4invlist").getGridParam('selarrrow');	
	var astring = aprint.toString();
	var loc = document.getElementById('loc').value;
	var ok = "Y";
	if (astring == "") {
		alert("Please select at least one delivery note.");
		ok = "N";
		return false;
	}
	if (loc == "") {
		alert("Please select relevant location.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
	
	var x = 0, y = 0; // default values	
	  if (confirm("Are you sure you want to create an invoice from the selected delivery notes?")) {
		jQuery.ajaxSetup({async:false});  
		$.get("../ajax/ajaxmakeinv.php", {dstring: astring}, function(data){});
		
		var type = 'INV';
		var ddate = "<?php echo $ddateh; ?>";
		var descript = "<?php echo $gldesc; ?>";
		var ref = "<?php echo $inno; ?>";
		var acc = <?php echo $accountno; ?>;
		var asb = <?php echo $sub; ?>;
		var postaladdress = "<?php echo $postaladdress; ?>";
		var deliveryaddress = "<?php echo $deliveryaddress; ?>";
		var clt = "<?php echo $client; ?>";
		var paymethod = '';
		
		$.get("../fin/includes/ajaxPostTrade.php", {type:type,ddate:ddate,descript:descript,ref:ref,acc:acc,asb:asb,loc:loc,postaladdress:postaladdress,deliveryaddress:deliveryaddress,clt:clt,paymethod:paymethod}, function(data){});
		jQuery.ajaxSetup({async:true});  
		this.close();
	  }
	}
}

</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="700" border="0" align="center" cellpadding="3">
  <tr>
  	<td align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Create Invoice</label></td>
  </tr>
  <tr>
  <td align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:12px;">Obtain stock from location</label><select name="loc" id="loc"><?php echo $loc_options; ?>
      </select></td>
  </tr>
  <tr>
        <td ><?php include "getdnlines4inv.php"; ?></td>
  </tr>
  <tr>
  	<td align="right"><input type="button" name="binv" id="binv" value="Post Invoice" onClick="postinv()"/></td>
  </tr>
  </table>
  
  
</form>
</body>
</html>