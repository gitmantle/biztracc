<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$cluid = $_SESSION["s_memberid"];

$aduid = $_REQUEST['uid'];

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "select * from addresses where address_id = ".$aduid;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$memid = $member_id;
$adtype = $address_type_id;
$adloc = $location;
switch ($adloc) {
	case "Street":
		$stno = $street_no;
		$sad1 = $ad1;
		break;
	case "Post Box":
		$bad1 = $street_no;
		$po = $ad2;
		break;
	case "Rural Delivery":
		$stno = $street_no;
		$rd = $ad1;
		break;
}


// populate address type drop down
$query = "select * from address_type";
$result = mysql_query($query) or die(mysql_error());
$adtype_options = "<option value=\"\">Select Address Type</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($address_type_id == $adtype) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$adtype_options .= "<option value=\"".$address_type_id."\" ".$selected.">".$address_type."</option>";
}

// populate address location drop down
    $arr = array( 'Street','Post Box', 'Rural Delivery');
	$loc_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $adloc) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$loc_options .= "<option value=\"".$arr[$i]."\" ".$selected.">".$arr[$i]."</option>";
 	}


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Address</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script type="text/javascript" src="includes/ajaxGetPostCode.js"></script>

<script>
function getpc() {
	var adloc = document.getElementById('location').value;
	var adtype = document.getElementById('address_type').value;
	var stno = document.getElementById('stno').value;
	var sad1 = document.getElementById('sad1').value;
	var bad1 = document.getElementById('bad1').value;
	var po = document.getElementById('po').value;
	var rd = document.getElementById('rd').value;
	var suburb = document.getElementById('suburb').value;
	var town = document.getElementById('town').value;
	
	ajaxGetPostCode(adloc,adtype,stno,sad1,bad1,po,rd,suburb,town);

}


function hidedivs() {
	document.getElementById('streetno').style.visibility = "hidden";
	document.getElementById('street').style.visibility = "hidden";
	document.getElementById('pobox').style.visibility = "hidden";
	document.getElementById('postoffice').style.visibility = "hidden";
	document.getElementById('ruraldelivery').style.visibility = "hidden";
	document.getElementById('s').style.visibility = "hidden";
	document.getElementById('t').style.visibility = "hidden";
	document.getElementById('pc').style.visibility = "hidden";
	document.getElementById('ct').style.visibility = "hidden";
	document.getElementById('preferredp').style.visibility = "hidden";
	document.getElementById('preferredv').style.visibility = "hidden";
	document.getElementById('save').style.visibility = "hidden";
}

function showdivs(loc) {
	hidedivs();
	switch (loc) {
		case "Street":
			document.getElementById('streetno').style.visibility = "visible";
			document.getElementById('street').style.visibility = "visible";
			break;
		case "Post Box":
			document.getElementById('pobox').style.visibility = "visible";
			document.getElementById('postoffice').style.visibility = "visible";
			break;
		case "Rural Delivery":
			document.getElementById('streetno').style.visibility = "visible";
			document.getElementById('street').style.visibility = "visible";
			document.getElementById('ruraldelivery').style.visibility = "visible";
			break;
	}
	document.getElementById('s').style.visibility = "visible";
	document.getElementById('t').style.visibility = "visible";
	document.getElementById('pc').style.visibility = "visible";
	document.getElementById('ct').style.visibility = "visible";
	document.getElementById('preferredp').style.visibility = "visible";
	document.getElementById('preferredv').style.visibility = "visible";
	document.getElementById('save').style.visibility = "visible";
}

function popads(ad) {
	var addr = ad.split('~');
	var tp = addr[0];
	switch (tp) {
		case 's':
			document.getElementById('sad1').value = addr[1];
			document.getElementById('suburb').value = addr[2];
			document.getElementById('town').value = addr[3];
			document.getElementById('postcode').value = addr[4];
			break;
		case 'p':
			document.getElementById('po').value = addr[1];
			document.getElementById('town').value = addr[2];
			document.getElementById('postcode').value = addr[3];
			break;
		case 'r':
			document.getElementById('rd').value = addr[1];
			document.getElementById('town').value = addr[2];
			document.getElementById('postcode').value = addr[3];
			break;
		
		
	}
}


function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

</script>




<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="bwin">
<form name="form1" method="post" >
  <table width="850" border="0" align="center">
    <tr>
      <td colspan="3"><div align="center" class="style1"><u>Edit Address </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Location</td>
      <td colspan="2"><select name="location" id="location" onChange="showdivs(this.value);">
	  	<?php echo $loc_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Type</td>
      <td colspan="2"><select name="address_type" id="address_type">
	  	<?php echo $adtype_options; ?>
      </select></td>
    </tr>
    <tr id="streetno">
      <td class="boxlabel">Street No/Building/PO Box</td>
      <td colspan="2"><input name="stno" type="text" id="stno"  size="45" maxlength="45" onfocus="this.select();" value="<?php echo $stno; ?>"></td>
    </tr>
    <tr id="street">
      <td class="boxlabel"><div align="right">Street</div></td>
      <td colspan="2"><input name="sad1" type="text" id="sad1"  size="45" maxlength="45" onfocus="this.select();" value="<?php echo $sad1; ?>"></td>
     </tr>
    <tr id="pobox">
      <td class="boxlabel"><div align="right">PO Box</div></td>
      <td colspan="2"><input name="bad1" type="text" id="bad1"  size="45" maxlength="45" onfocus="this.select();" value="<?php echo $bad1; ?>"></td>
     </tr>
    <tr id="postoffice">
      <td class="boxlabel">Post Office</td>
      <td colspan="2"><input name="po" type="text" id="po"  size="45" maxlength="45" onfocus="this.select();" value="<?php echo $po; ?>"></td>
     </tr>
     <tr id="ruraldelivery">
      <td class="boxlabel">RD</td>
      <td colspan="2"><input name="rd" type="text" id="rd"  size="45" maxlength="45" onfocus="this.select();" value="<?php echo $rd; ?>"></td>
     </tr>
   <tr id="s">
      <td class="boxlabel"><div align="right">Suburb</div></td>
      <td colspan="2"><input name="suburb" type="text" id="suburb"  size="45" maxlength="45" onfocus="this.select();" value="<?php echo $suburb; ?>"></td>
     </tr>
    <tr id="t">
      <td class="boxlabel"><div align="right">Town</div></td>
      <td colspan="2"><input name="town" type="text" id="town"  size="45" maxlength="45" onfocus="this.select();" value="<?php echo $town; ?>"></td>
     </tr>
    <tr id="pc">
      <td class="boxlabel"><div align="right">
      Post Code</div></td>
      <td colspan="2"><input name="postcode" type="text" id="postcode"  size="5" maxlength="4" value="<?php echo $postcode; ?>">&nbsp;</td>
     </tr>
    <tr id="ct">
      <td class="boxlabel"><div align="right">Country</div></td>
      <td colspan="2"><input name="country" type="text" id="country"  size="45" maxlength="45" onfocus="this.select();" value="<?php echo $country; ?>"></td>
     </tr>
    <tr id="preferredp">
      <td class="boxlabel">Preferred Postal Address</td>
      <td colspan="2"><select name="prefp" id="prefp">
        <?php
		if ($preferredp == 'Y') {
			echo '<option value="N">No</option>';
			echo '<option value="Y" selected>Yes</option>';
		} else {
			echo '<option value="N" selected>No</option>';
			echo '<option value="Y">Yes</option>';
		}
		?>
      </select></td>
    </tr>
	 
	 
	<tr id="preferredv">
      <td class="boxlabel">Preferred Visiting Address</td>
      <td align="left"><select name="prefv" id="prefv">
		<?php
		if ($preferredv == 'Y') {
			echo '<option value="N">No</option>';
			echo '<option value="Y" selected>Yes</option>';
		} else {
			echo '<option value="N" selected>No</option>';
			echo '<option value="Y">Yes</option>';
		}
		?>
      </select></td>
      <td align="right" id="save"><input type="submit" value="Save" name="save" ></td>
      </tr>
  </table>
</form>
</div>

	<script>
		var loc = document.getElementById('location').value;
		showdivs(loc);
	</script>
	<script>document.onkeypress = stopRKey;</script> 

<?php

	if(isset($_POST['save'])) {

		if ($_REQUEST['town'] == '') {
			echo '<script>';
			echo 'alert("Please enter an address.")';
			echo '</script>';	
		} else {			

				include_once("../includes/cltadmin.php");
				$oAd = new cltadmin;	
				
				$oAd->uid = $aduid;
				$oAd->loc = $_REQUEST['location'];
				$oAd->address_type_id = $_REQUEST['address_type'];
				$oAd->client_id = $cluid;
				
				switch ($_REQUEST['location']) {
					case "Street":
						$oAd->street_no = $_REQUEST['stno'];
						$oAd->ad1 = $_REQUEST['sad1'];
						break;
					case "Post Box":
						$oAd->street_no = $_REQUEST['bad1'];
						$oAd->ad2 = $_REQUEST['po'];
						break;
					case "Rural Delivery":
						$oAd->street_no = $_REQUEST['stno'];
						$oAd->ad1 = $_REQUEST['rd'];
						break;
				}
				
				$oAd->suburb = ucwords(strtolower($_REQUEST['suburb']));
				$oAd->town = ucwords(strtolower($_REQUEST['town']));
				$oAd->postcode = $_REQUEST['postcode'];
				$oAd->country = $_REQUEST['country'];
				$oAd->preferredp = $_REQUEST['prefp'];
				$oAd->preferredv = $_REQUEST['prefv'];
				
				$oAd->EditAddress();

			  $hdate = date('Y-m-d');
			  $ttime = strftime("%H:%M", mktime());
	
			  $query = "insert into audit (ddate,ttime,user_id,uname,member_id,address_id,action) values ";
			  $query .= "('".$hdate."',";
			  $query .= "'".$ttime."',";
			  $query .= $user_id.",";
			  $query .= "'".$uname."',";
			  $query .= $memid.",";
			  $query .= $aduid.",";
			  $query .= "'Edit Address')";
			  
			  $result = mysql_query($query) or die(mysql_error().$query);

				?>
				<script>
				window.open("","editsuppliers").jQuery("#madlist2").trigger("reloadGrid");
				this.close();
				</script>
				<?php
		
			
		}
	}

?>


</body>
</html>
