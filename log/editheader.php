<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];
$costuid = $_REQUEST['uid'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select * from costheader where uid = ".$costuid;
$r = mysql_query($q) or die(mysql_error().$q);
$row = mysql_fetch_array($r);
extract($row);
$ocreditor = $supplierid;

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate creditor drop down
$query = "select members.lastname,members.firstname,client_company_xref.crno,client_company_xref.crsub from members left join client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.company_id = ".$coyid." and client_company_xref.crno != 0"; 
$result = mysql_query($query) or die(mysql_error().$query);
$creditor_options = "<option value=\"0\">Select Supplier</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($ocreditor == $crno.'~'.$crsub) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$creditor_options .= '<option value="'.$crno.'~'.$crsub.'~'.trim($firstname." ".$lastname).'"'.$selected.'>'.trim($firstname." ".$lastname).'</option>';
}

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());


date_default_timezone_set($_SESSION['s_timezone']);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Cost Header</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>

<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function post() {

	//add validation here if required.
	var ok = 'Y'
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
	
	
}


</script>

<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
 <table width="500" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" align="center"  bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>"><strong>Edit Cost Header</strong></label></td>
    </tr>
    <tr>
      <td  class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Date</label></td>
      <td><input name="dt" type="text" id="dt"  value="<?php echo $date; ?>" readonly ></td>
      </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Truck</label></td>
      <td><input name="truck" type="text" id="truck" value="<?php echo $truckno; ?>"  readonly ></td>
    </tr>
    <tr>
      <td  class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Trailer</label></td>
      <td><input name="trailer" type="text" id="trailer" value="<?php echo $trailerno; ?>" readonly ></td>
      </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Description</label></td>
      <td><input name="desc" type="text" id="desc" value="<?php echo $description; ?>"></td>
      </tr>
    <tr>
      <td  class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Supplier</label></td>
      <td><select name="sup" id="sup"><?php echo $creditor_options;?></select></td>
      </tr>
    <tr>
      <td  class="boxlabel" id="snos">Supplier Ref.</td>
      <td><input type="text" name="sref" id="sref"  value="<?php echo $supplierref; ?>"></td>
    </tr>
	<tr>
      <td colspan="2" align="right">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  	</table>
    
    
</form>

<?php

	if($_REQUEST['savebutton'] == "Y") {
		$desc = $_REQUEST['desc'];
		$s = $_REQUEST['sup'];
		$sp = explode('~',$s);
		$sup = $sp[0].'~'.$sp[1];
		$supplier = $sp[2];
		$sref = $_REQUEST['sref'];

		$q = "update costheader set ";
		$q .= "supplierid = '".$sup."',";
		$q .= "supplierref = '".$sref."',";
		$q .= "description = '".$desc."',";
		$q .= "supplier = '".$supplier."'";
		$q .= " where uid = ".$costuid;

		$r = mysql_query($q) or die(mysql_error().$q);

	  ?>
	  <script>
	  window.open("","costs").jQuery("#costheadlisting").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
