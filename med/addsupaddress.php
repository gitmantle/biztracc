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

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate address types drop down
$query = "select * from address_type";
$result = mysql_query($query) or die(mysql_error());
$adtype_options = "<option value=\"\">Select Address Type</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$adtype_options .= "<option value=\"".$address_type_id."\">".$address_type."</option>";
}


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Address</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

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
	document.getElementById('preferred').style.visibility = "hidden";
	document.getElementById('save').style.visibility = "hidden";
}

function showdivs(loc) {
	if (loc == '') {
		alert('Please select a location');
		return false;
	} else {
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
				document.getElementById('sad1').value = '';
				document.getElementById('ruraldelivery').style.visibility = "visible";
				document.getElementById('town').value = "Enter the town and press Get Post Code";
				break;
		}
		document.getElementById('s').style.visibility = "visible";
		document.getElementById('t').style.visibility = "visible";
		document.getElementById('pc').style.visibility = "visible";
		document.getElementById('ct').style.visibility = "visible";
		document.getElementById('preferred').style.visibility = "visible";
		document.getElementById('save').style.visibility = "visible";
	}
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
      <td colspan="3"><div align="center" class="style1"><u>Add Address </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Location</td>
      <td colspan="2"><select name="location" id="location" onChange="showdivs(this.value);">
        <option>Select Location</option>
        <option value="Street">Street</option>
        <option value="Post Box">Post Box</option>
        <option value="Rural Delivery">Rural Delivery</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Type</td>
      <td colspan="2"><select name="address_type" id="address_type">
	  	<?php echo $adtype_options; ?>
      </select></td>
    </tr>
    <tr id="streetno">
      <td class="boxlabel">Street No/Building</td>
      <td colspan="2"><input name="stno" type="text" id="stno"  size="45" maxlength="45" onfocus="this.select();"></td>
    </tr>
    <tr id="street">
      <td class="boxlabel">Street</td>
      <td colspan="2"><input name="sad1" type="text" id="sad1"  size="45" maxlength="45" onfocus="this.select();"></td>
     </tr>
    <tr id="pobox">
      <td class="boxlabel">PO Box</td>
      <td colspan="2"><input name="bad1" type="text" id="bad1"  size="45" maxlength="45" onfocus="this.select();"></td>
     </tr>
    <tr id="postoffice">
      <td class="boxlabel">Post Office</td>
      <td colspan="2"><input name="po" type="text" id="po"  size="45" maxlength="45" onfocus="this.select();"></td>
     </tr>
     <tr id="ruraldelivery">
      <td class="boxlabel">RD</td>
      <td colspan="2"><input name="rd" type="text" id="rd"  size="45" maxlength="45" onfocus="this.select();"></td>
     </tr>
   <tr id="s">
      <td class="boxlabel">Suburb</td>
      <td colspan="2"><input name="suburb" type="text" id="suburb"  size="45" maxlength="45" onfocus="this.select();"></td>
     </tr>
    <tr id="t">
      <td class="boxlabel">Town</td>
      <td colspan="2"><input name="town" type="text" id="town"  size="45" maxlength="45" onfocus="this.select();"></td>
     </tr>
    <tr id="pc">
      <td class="boxlabel">        Post Code</td>
      <td colspan="2"><input name="postcode" type="text" id="postcode"  size="5" maxlength="4" on>&nbsp;</td>
     </tr>
    <tr id="ct">
      <td class="boxlabel">Country</td>
      <td colspan="2"><input name="country" type="text" id="country"  size="45" maxlength="45" onfocus="this.select();" ></td>
     </tr>
    <tr>
      <td class="boxlabel">Preferred Postal Address</td>
      <td colspan="2"><select name="prefp" id="prefp">
        <option value="N">No</option>
        <option value="Y">Yes</option>
      </select></td>
    </tr>
	 
	 
	<tr id="preferred">
      <td class="boxlabel">Preferred Visiting Address</td>
      <td align="left"><select name="prefv" id="prefv">
        <option value="N">No</option>
        <option value="Y">Yes</option>
      </select></td>
      <td align="right"><input type="submit" value="Save" name="save" id="save"></td>
      </tr>
  </table>
</form>

<script>
	document.onkeypress = stopRKey;
</script> 


<script>
	hidedivs();
</script>


</div>

<?php

	if(isset($_POST['save'])) {

		if ($_REQUEST['town'] == '') {
			echo '<script>';
			echo 'alert("Please enter an address.")';
			echo '</script>';	
		} else {			

				include_once("../includes/cltadmin.php");
				$oAd = new cltadmin;	

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
		  		$oAd->sub_id = $sub_id;
		
				$addid = $oAd->AddAddress();

			  $hdate = date('Y-m-d');
			  $ttime = strftime("%H:%M", mktime());
	
			  $query = "insert into audit (ddate,ttime,user_id,uname,member_id,address_id,action) values ";
			  $query .= "('".$hdate."',";
			  $query .= "'".$ttime."',";
			  $query .= $user_id.",";
			  $query .= "'".$uname."',";
			  $query .= $cluid.",";
			  $query .= $addid.",";
			  $query .= "'Add Address')";
			  
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
