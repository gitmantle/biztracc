<?php
session_start();
//error_reporting (E_ERROR);

//ini_set('display_errors', true);
$usersession = $_SESSION['usersession'];
$dbase = $_SESSION['s_admindb'];
$dbprefix = $_SESSION['s_dbprefix'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

date_default_timezone_set($_SESSION['s_timezone']);

$id = $_REQUEST['uid'];

$cltdb = $_SESSION['s_cltdb'];

$db->query("select client_id,company_id,priceband,billing,email,sendstatement,blocked from ".$cltdb.".client_company_xref where uid = ".$id); 
$row = $db->single();
extract($row);
$sendby = $sendstatement;
$em = $email;
$bill = $billing;
$pb = $priceband;
$mid = $client_id;
$blk = $blocked;
$coyid = $company_id;

$findb = $dbprefix.'fin'.$subid.'_'.$coyid;

// populate price band  drop down
$db->query("select * from ".$findb.".stkpricepcent");
$rows = $db->resultset();
$priceband_options = "<option value=\"0\">Select Price Band</option>";
foreach ($rows as $row) {
	extract($row);
	if ($uid == $pb) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$priceband_options .= "<option value=\"".$uid."\" ".$selected.">".$priceband."</option>";
}

// populate method list
    $arr = array('Post','Email');
	$method_options = "";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $sendby) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$method_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

if ($bill == 0 ) {
	$ad = "";
} else {
	// populate address list
	$db->query("select * from ".$cltdb.".addresses where address_id = ".$bill);
	$row = $db->single();
	extract($row);
	$ad = trim($street_no.' '.$ad1.' '.$ad2.' '.$suburb.' '.$town.' '.$postcode);	
}

if ($em == 0) {
	$cm = "";
} else {
	// populate comms list
	$db->query("select comm from ".$cltdb.".comms where comms_id = ".$em);
	$row = $db->single();
	extract($row);
	$cm = $comm;	
}

// populate blocked list
    $arr = array('No','Yes');
	$blk_options = "";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $blk) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$blk_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}


$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Financials</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>

</head>

<script>
	 
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}
	 
function post() {

	//add validation here if required.
	var meth = document.getElementById('lmethod').value;
	//var em = document.getElementById('lemail').value;
	//var bl = document.getElementById('lbilling').value;
	var pb = document.getElementById('lpb').value;
	
	var ok = "Y";
	if (pb == '0') {
		alert("Please enter a price band.");
		ok = "N";
		return false;
	}

	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
}

</script>

<body>
<div id="lwin">
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="700" border="0" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Edit Statement Parameters</strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Send Statement by:-</label></td>
      <td><select name="lmethod" id="lmethod" ><?php echo $method_options; ?></select></td>
    </tr>
    <tr id="b">
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Send Statement by Post to Address to:-</label></td>
      <td class="boxlabelleft"><?php echo $ad; ?></td>
    </tr>
    <tr id="e">
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Send Statement by Email to Address:-</label></td>
      <td class="boxlabelleft" ><?php echo $cm; ?></td>
    </tr>
    <tr>
      <td class="boxlabel">Blocked</td>
      <td ><select name="blk" id="blk"><?php echo $blk_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Price Band:-</td>
      <td ><select name="lpb" id="lpb"><?php echo $priceband_options; ?></select></td>
    </tr>
	<tr>
      <td align="right" colspan="2">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  </table>
</form>
</div>

	<script>
		document.onkeypress = stopRKey;
    </script> 

<?php

	if($_REQUEST['savebutton'] == "Y") {
		
		$pband = $_REQUEST['lpb'];
		$billad = $_REQUEST['lbilling'];
		$emailad = $_REQUEST['lemail'];
		$sendby = $_REQUEST['lmethod'];
		$blkd = $_REQUEST['blk'];
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$db->query("update ".$cltdb.".client_company_xref set sendstatement = :sendstatement, billing = :billing, email = :email, blocked = :blocked, priceband = :priceband where uid = :uid");
		$db->bind(':sendstatement', $sendby);
		$db->bind(':billing', $billad);
		$db->bind(':email', $emailad);
		$db->bind(':blocked', $blkd);
		$db->bind(':priceband', $pband);
		$db->bind(':uid', $id);
		
		$db->execute();
		$db->closeDB();
		
		
?>	
	<script>
	window.open("","editmembers").jQuery("#mfinancialslist").trigger("reloadGrid");
	this.close();
	</script>
    
<?php
		}
?>


</body>
</html>
