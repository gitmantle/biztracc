<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];
$coyname = $_SESSION['s_coyname'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());


$dc = $_REQUEST['cl'];
if ($dc == 'd') {
	$fl = "documents/".$coyid."__dr.xlsx";
	$hed = "Process Customer/Debtor List";
	$ledger = 'Debtor';
	$uploadfile = "ztmp".$user_id."_impdr";
} else {
	$fl = "documents/".$coyid."__cr.xlsx";
	$hed = "Process Supplier/Creditor List";
	$ledger = 'Creditor';
	$uploadfile = "ztmp".$user_id."_impcr";
}

if (file_exists($fl)) {
	// PHPExcel_IOFactory 
	require_once '../includes/phpexcel/Classes/PHPExcel/IOFactory.php';
	
	$inputFileName = $fl;
	
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
	try {
		// Load $inputFileName to a PHPExcel Object 
		$objPHPExcel = $objReader->load($fl); 
	} catch(Exception $e) {
		die('Error loading file: '.$e->getMessage());
	}

} else { // file not exists
  echo '<script>';
  echo 'alert("'.$ledger.' spreadsheet does not exist");';
  echo 'this.close();';
  echo '</script>';
} // file exists


$query = "drop table if exists ".$uploadfile;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$uploadfile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY )  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Process Client List Spreadsheet</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

<script>
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function process() {
	var client = document.getElementById('tlastname').value;
	var ok = "Y";
	
	if (client == "") {
		alert('Please state column that contains the client last name');
		ok = 'N';
		return false;
	}
	
	if (ok == 'Y') {
	  document.getElementById('savebutton').value = "Y";
	  document.getElementById('form1').submit();
	}
}


function getheader(header,col) {
	var field = 'x'+col.toUpperCase();
	var txt = document.getElementById(field).value;
	document.getElementById(header).value = txt;
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



  <div id="heading" style="position:absolute;visibility:visible;top:200px;left:1px;height:49px;width:890px;background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
  <table width="900" border="0" align="center">
    <tr>
      <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u><?php echo $hed; ?></u></label></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;">Type relevant column letter from Data Available against Data Accepted</label></td>
    </tr>
  </table>
  </div>
  <div id="accepted" style="position:absolute; visibility:visible; top:251px; left:1px; height:670px; width:490px; background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid; overflow:scroll">
		<table width="450" border="0">
		  <tr>
		    <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u>Data Accepted by System</u></label></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">First name</label></td>
		    <td><input type="text" name="tfirstname" id="tfirstname" size="5" onchange="getheader('pfirstname',this.value)">&nbsp;<input type="text" name="pfirstname" id="pfirstname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Middle Name</label></td>
		    <td><input type="text" name="tmiddlename" id="tmiddlename" size="5" onchange="getheader('pmiddlename',this.value)">&nbsp;<input type="text" name="pmiddlename" id="pmiddlename" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Last/Company name</label></td>
		    <td><input type="text" name="tlastname" id="tlastname" size="5" onchange="getheader('plastname',this.value)">&nbsp;<input type="text" name="plastname" id="plastname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Preferred name</label></td>
		    <td><input type="text" name="tpreferredname" id="tpreferredname" size="5" onchange="getheader('ppreferredname',this.value)">&nbsp;<input type="text" name="ppreferredname" id="ppreferredname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Date of birth</label></td>
		    <td><input type="text" name="tdob" id="tdob" size="5" onchange="getheader('pdob',this.value)">&nbsp;<input type="text" name="pdob" id="pdob" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Title</label></td>
		    <td><input type="text" name="ttitle" id="ttitle" size="5" onchange="getheader('ptitle',this.value)">&nbsp;<input type="text" name="ptitle" id="ptitle" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Category</label></td>
		    <td><input type="text" name="tcategory" id="tcategory" size="5" onchange="getheader('pcategory',this.value)">&nbsp;<input type="text" name="pcategory" id="pcategory" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Dealt with by</label></td>
		    <td><input type="text" name="tpadvisor" id="tpadvisor" size="5" onchange="getheader('ppadvisor',this.value)">&nbsp;<input type="text" name="ppadvisor" id="ppadvisor" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Position</label></td>
		    <td><input type="text" name="tposition" id="tposition" size="5" onchange="getheader('pposition',this.value)">&nbsp;<input type="text" name="pposition" id="pposition" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Occupation</label></td>
		    <td><input type="text" name="toccupation" id="toccupation" size="5" onchange="getheader('poccupation',this.value)">&nbsp;<input type="text" name="poccupation" id="poccupation" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone home country 1</label></td>
		    <td><input type="text" name="tphonehomecountry1" id="tphonehomecountry1" size="5" onchange="getheader('pphonehomecountry1',this.value)">&nbsp;<input type="text" name="pphonehomecountry1" id="pphonehomecountry1" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone home area code 1</label></td>
		    <td><input type="text" name="tphonehomearea1" id="tphonehomearea1" size="5" onchange="getheader('pphonehomearea1',this.value)">&nbsp;<input type="text" name="pphonehomearea1" id="pphonehomearea1" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone home number 1</label></td>
		    <td><input type="text" name="tphonehomenumber1" id="tphonehomenumber1" size="5" onchange="getheader('pphonehomenumber1',this.value)">&nbsp;<input type="text" name="pphonehomenumber1" id="pphonehomenumber1" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone home country 2</label></td>
		    <td><input type="text" name="tphonehomecountry2" id="tphonehomecountry2" size="5" onchange="getheader('pphonehomecountry2',this.value)">&nbsp;<input type="text" name="pphonehomecountry2" id="pphonehomecountry2" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone home area 2</label></td>
		    <td><input type="text" name="tphonehomearea2" id="tphonehomearea2" size="5" onchange="getheader('pphonehomearea2',this.value)">&nbsp;<input type="text" name="pphonehomearea2" id="pphonehomearea2" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone home number 2</label></td>
		    <td><input type="text" name="tphonehomenumber2" id="tphonehomenumber2" size="5" onchange="getheader('pphonehomenumber2',this.value)">&nbsp;<input type="text" name="pphonehomenumber2" id="pphonehomenumber2" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Mobile country 1</label></td>
		    <td><input type="text" name="tmobilecountry1" id="tmobilecountry1" size="5" onchange="getheader('pmobilecountry1',this.value)">&nbsp;<input type="text" name="pmobilecountry1" id="pmobilecountry1" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Mobile code 1</label></td>
		    <td><input type="text" name="tmobilecode1" id="tmobilecode1" size="5" onchange="getheader('pmobilecode1',this.value)">&nbsp;<input type="text" name="pmobilecode1" id="pmobilecode1" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Mobile number 1</label></td>
		    <td><input type="text" name="tmobilenumber1" id="tmobilenumber1" size="5" onchange="getheader('pmobilenumber1',this.value)">&nbsp;<input type="text" name="pmobilenumber1" id="pmobilenumber1" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Mobile country 2</label></td>
		    <td><input type="text" name="tmobilecountry2" id="tmobilecountry2" size="5" onchange="getheader('pmobilecountry2',this.value)">&nbsp;<input type="text" name="pmobilecountry2" id="pmobilecountry2" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Mobile code 2</label></td>
		    <td><input type="text" name="tmobilecode2" id="tmobilecode2" size="5" onchange="getheader('pmobilecode2',this.value)">&nbsp;<input type="text" name="pmobilecode2" id="pmobilecode2" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Mobile number 2</label></td>
		    <td><input type="text" name="tmobilenumber2" id="tmobilenumber2" size="5" onchange="getheader('pmobilenumber2',this.value)">&nbsp;<input type="text" name="pmobilenumber2" id="pmobilenumber2" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone work country 1</label></td>
		    <td><input type="text" name="tphoneworkcountry1" id="tphoneworkcountry1" size="5" onchange="getheader('pphoneworkcountry1',this.value)">&nbsp;<input type="text" name="pphoneworkcountry1" id="pphoneworkcountry1" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone work area code 1</label></td>
		    <td><input type="text" name="tphoneworkarea1" id="tphoneworkarea1" size="5" onchange="getheader('pphoneworkarea1',this.value)">&nbsp;<input type="text" name="pphoneworkarea1" id="pphoneworkarea1" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone work number 1</label></td>
		    <td><input type="text" name="tphoneworknumber1" id="tphoneworknumber1" size="5" onchange="getheader('pphoneworknumber1',this.value)">&nbsp;<input type="text" name="pphoneworknumber1" id="pphoneworknumber1" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone work country 2</label></td>
		    <td><input type="text" name="tphoneworkcountry2" id="tphoneworkcountry2" size="5" onchange="getheader('pphoneworkcountry2',this.value)">&nbsp;<input type="text" name="pphoneworkcountry2" id="pphoneworkcountry2" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone work area code 2</label></td>
		    <td><input type="text" name="tphoneworkarea2" id="tphoneworkarea2" size="5" onchange="getheader('pphoneworkarea2',this.value)">&nbsp;<input type="text" name="pphoneworkarea2" id="pphoneworkarea2" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone work number 2</label></td>
		    <td><input type="text" name="tphoneworknumber2" id="tphoneworknumber2" size="5" onchange="getheader('pphoneworknumber2',this.value)">&nbsp;<input type="text" name="pphoneworknumber2" id="pphoneworknumber2" readonly></td>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone work country 3</label></td>
		    <td><input type="text" name="tphoneworkcountry3" id="tphoneworkcountry3" size="5" onchange="getheader('pphoneworkcountry3',this.value)">&nbsp;<input type="text" name="pphoneworkcountry3" id="pphoneworkcountry3" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone work area code 3</label></td>
		    <td><input type="text" name="tphoneworkarea3" id="tphoneworkarea3" size="5" onchange="getheader('pphoneworkarea3',this.value)">&nbsp;<input type="text" name="pphoneworkarea3" id="pphoneworkarea3" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone work number 3</label></td>
		    <td><input type="text" name="tphoneworknumber3" id="tphoneworknumber3" size="5" onchange="getheader('pphoneworknumber3',this.value)">&nbsp;<input type="text" name="pphoneworknumber3" id="pphoneworknumber3" readonly></td>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone work country 4</label></td>
		    <td><input type="text" name="tphoneworkcountry4" id="tphoneworkcountry4" size="5" onchange="getheader('pphoneworkcountry4',this.value)">&nbsp;<input type="text" name="pphoneworkcountry4" id="pphoneworkcountry4" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone work area code 4</label></td>
		    <td><input type="text" name="tphoneworkarea4" id="tphoneworkarea4" size="5" onchange="getheader('pphoneworkarea4',this.value)">&nbsp;<input type="text" name="pphoneworkarea4" id="pphoneworkarea4" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Phone work number 4</label></td>
		    <td><input type="text" name="tphoneworknumber4" id="tphoneworknumber4" size="5" onchange="getheader('pphoneworknumber4',this.value)">&nbsp;<input type="text" name="pphoneworknumber4" id="pphoneworknumber4" readonly></td>
	      </tr>
 		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Fax work country</label></td>
		    <td><input type="text" name="tfaxworkcountry" id="tfaxworkcountry" size="5" onchange="getheader('pfaxworkcountry',this.value)">&nbsp;<input type="text" name="pfaxworkcountry" id="pfaxworkcountry" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Fax work area code</label></td>
		    <td><input type="text" name="tfaxworkarea" id="tfaxworkarea" size="5" onchange="getheader('pfaxworkarea',this.value)">&nbsp;<input type="text" name="pfaxworkarea" id="pfaxworkarea" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Fax work number</label></td>
		    <td><input type="text" name="tfaxworknumber" id="tfaxworknumber" size="5" onchange="getheader('pfaxworknumber',this.value)">&nbsp;<input type="text" name="pfaxworknumber" id="pfaxworknumber" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Email 1</label></td>
		    <td><input type="text" name="temail1" id="temail1" size="5" onchange="getheader('pemail1',this.value)">&nbsp;<input type="text" name="pemail1" id="pemail1" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Email 2</label></td>
		    <td><input type="text" name="temail2" id="temail2" size="5" onchange="getheader('pemail2',this.value)">&nbsp;<input type="text" name="pemail2" id="pemail2" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Home street number</label></td>
		    <td><input type="text" name="thomestreetno" id="thomestreetno" size="5" onchange="getheader('phomestreetno',this.value)">&nbsp;<input type="text" name="phomestreetno" id="phomestreetno" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Home street name</label></td>
		    <td><input type="text" name="thomestreetname" id="thomestreetname" size="5" onchange="getheader('phomestreetname',this.value)">&nbsp;<input type="text" name="phomestreetname" id="phomestreetname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Home street suburb</label></td>
		    <td><input type="text" name="thomestreetsuburb" id="thomestreetsuburb" size="5" onchange="getheader('phomestreetsuburb',this.value)">&nbsp;<input type="text" name="phomestreetsuburb" id="phomestreetsuburb" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Home street town</label></td>
		    <td><input type="text" name="thomestreettown" id="thomestreettown" size="5" onchange="getheader('phomestreettown',this.value)">&nbsp;<input type="text" name="phomestreettown" id="phomestreettown" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Home street country</label></td>
		    <td><input type="text" name="thomestreetcountry" id="thomestreetcountry" size="5" onchange="getheader('phomestreetcountry',this.value)">&nbsp;<input type="text" name="phomestreetcountry" id="phomestreetcountry" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Home street postcode</label></td>
		    <td><input type="text" name="thomestreetpostcode" id="thomestreetpostcode" size="5" onchange="getheader('phomestreetpostcode',this.value)">&nbsp;<input type="text" name="phomestreetpostcode" id="phomestreetpostcode" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Home postbox number</label></td>
		    <td><input type="text" name="thomeponumber" id="thomeponumber" size="5" onchange="getheader('phomeponumber',this.value)">&nbsp;<input type="text" name="phomeponumber" id="phomeponumber" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Home postbox suburb</label></td>
		    <td><input type="text" name="thomeposuburb" id="thomeposuburb" size="5" onchange="getheader('phomeposuburb',this.value)">&nbsp;<input type="text" name="phomeposuburb" id="phomeposuburb" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Home postbox town</label></td>
		    <td><input type="text" name="thomepotown" id="thomepotown" size="5" onchange="getheader('phomepotown',this.value)">&nbsp;<input type="text" name="phomepotown" id="phomepotown" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Home postbox postcode</label></td>
		    <td><input type="text" name="thomepopostcode" id="thomepopostcode" size="5" onchange="getheader('phomepopostcode',this.value)">&nbsp;<input type="text" name="phomepopostcode" id="phomepopostcode" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Home postbox country</label></td>
		    <td><input type="text" name="thomepocountry" id="thomepocountry" size="5" onchange="getheader('phomepocountry',this.value)">&nbsp;<input type="text" name="phomepocountry" id="phomepocountry" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Work street number</label></td>
		    <td><input type="text" name="tworkstreetno" id="tworkstreetno" size="5" onchange="getheader('pworkstreetno',this.value)">&nbsp;<input type="text" name="pworkstreetno" id="pworkstreetno" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Work street name</label></td>
		    <td><input type="text" name="tworkstreetname" id="tworkstreetname" size="5" onchange="getheader('pworkstreetname',this.value)">&nbsp;<input type="text" name="pworkstreetname" id="pworkstreetname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Work street suburb</label></td>
		    <td><input type="text" name="tworkstreetsuburb" id="tworkstreetsuburb" size="5" onchange="getheader('pworkstreetsuburb',this.value)">&nbsp;<input type="text" name="pworkstreetsuburb" id="pworkstreetsuburb" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Work street town</label></td>
		    <td><input type="text" name="tworkstreettown" id="tworkstreettown" size="5" onchange="getheader('pworkstreettown',this.value)">&nbsp;<input type="text" name="pworkstreettown" id="pworkstreettown" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Work street postcode</label></td>
		    <td><input type="text" name="tworkstreetpostcode" id="tworkstreetpostcode" size="5" onchange="getheader('pworkstreetpostcode',this.value)">&nbsp;<input type="text" name="pworkstreetpostcode" id="pworkstreetpostcode" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Work street country</label></td>
		    <td><input type="text" name="tworkstreetcountry" id="tworkstreetcountry" size="5" onchange="getheader('pworkstreetcountry',this.value)">&nbsp;<input type="text" name="pworkstreetcountry" id="pworkstreetcountry" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Billing street number/PO</label></td>
		    <td><input type="text" name="tbillstreetno" id="tbillstreetno" size="5" onchange="getheader('pbillstreetno',this.value)">&nbsp;<input type="text" name="pbillstreetno" id="pbillstreetno" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Billing street name</label></td>
		    <td><input type="text" name="tbillstreetname" id="tbillstreetname" size="5" onchange="getheader('pbillstreetname',this.value)">&nbsp;<input type="text" name="pbillstreetname" id="pbillstreetname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Billing suburb</label></td>
		    <td><input type="text" name="tbillstreetsuburb" id="tbillstreetsuburb" size="5" onchange="getheader('pbillstreetsuburb',this.value)">&nbsp;<input type="text" name="pbillstreetsuburb" id="pbillstreetsuburb" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Billing town</label></td>
		    <td><input type="text" name="tbillstreettown" id="tbillstreettown" size="5" onchange="getheader('pbillstreettown',this.value)">&nbsp;<input type="text" name="pbillstreettown" id="pbillstreettown" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Billing postcode</label></td>
		    <td><input type="text" name="tbillstreetpostcode" id="tbillstreetpostcode" size="5" onchange="getheader('pbillstreetpostcode',this.value)">&nbsp;<input type="text" name="pbillstreetpostcode" id="pbillstreetpostcode" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Billing country</label></td>
		    <td><input type="text" name="tbillstreetcountry" id="tbillstreetcountry" size="5" onchange="getheader('pbillstreetcountry',this.value)">&nbsp;<input type="text" name="pbillstreetcountry" id="pbillstreetcountry" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Notes 1</label></td>
		    <td><input type="text" name="tnotes1" id="tnotes1" size="5" onchange="getheader('pnotes1',this.value)">&nbsp;<input type="text" name="pnotes1" id="pnotes1" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Notes 2</label></td>
		    <td><input type="text" name="tnotes2" id="tnotes2" size="5" onchange="getheader('pnotes2',this.value)">&nbsp;<input type="text" name="pnotes2" id="pnotes2" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Source Client Number</label></td>
		    <td><input type="text" name="tsourceno" id="tsourceno" size="5" onchange="getheader('psourceno',this.value)">&nbsp;<input type="text" name="psourceno" id="psourceno" readonly></td>
	      </tr>
          
          
          
          
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Contact First name</label></td>
		    <td><input type="text" name="ta1firstname" id="ta1firstname" size="5" onchange="getheader('pa1firstname',this.value)">&nbsp;<input type="text" name="pa1firstname" id="pa1firstname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Contact Last name</label></td>
		    <td><input type="text" name="ta1lastname" id="ta1lastname" size="5" onchange="getheader('pa1lastname',this.value)">&nbsp;<input type="text" name="pa1lastname" id="pa1lastname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Contact Date of Birth</label></td>
		    <td><input type="text" name="ta1dob" id="ta1dob" size="5" onchange="getheader('pa1dob',this.value)">&nbsp;<input type="text" name="pa1dob" id="pa1dob" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Contact Preferred name</label></td>
		    <td><input type="text" name="ta1preferredname" id="ta1preferredname" size="5" onchange="getheader('pa1preferredname',this.value)">&nbsp;<input type="text" name="pa1preferredname" id="pa1preferredname" readonly></td>
	      </tr>
          
		  <tr>
		    <td class="boxlabel">&nbsp;</td>
		    <td><label>
		      <input type="button" name="btnprocess" id="btnprocess" value="Process" onclick="process()">
	        </label></td>
	      </tr>
	    </table>
  </div>
  		<div id="available" style="position:absolute;visibility:visible;top:251px;left:497px;height:670px;width:394px;background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid; overflow:scroll">
        <table width="377" border="0">
		  <tr>
		    <td colspan="2"align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u>Data Available from your Client List spreadsheet</u></label></td>
            <?php
			
			foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
				foreach ($worksheet->getRowIterator() as $row) {
					$cellIterator = $row->getCellIterator();
					//$cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
					$cellIterator->setIterateOnlyExistingCells(true); // Loop set cells only
					foreach ($cellIterator as $cell) {
						
						if ($cell->getRow() == 1) {
							if (!is_null($cell) && $cell->getCalculatedValue() != "" ) {
								echo '<tr>';
								echo '<td width="50" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">'.$cell->getColumn().'</label></td>';
								echo '<td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>"><input type="text" id="x'.$cell->getColumn().'" readonly value="'.$cell->getCalculatedValue().'"></label></td>';
								echo '</tr>';
								
								$qcol = "select * from information_schema.columns where table_name = '".$uploadfile."' and column_name = 'x".$cell->getColumn()."'";
								$rcol = mysql_query($qcol) or die (mysql_error().' '.$qcol);
								if ($numrows == 0) {
									$qc = "alter table ".$uploadfile." add x".$cell->getColumn()." varchar( 45 ) not null" ;
									$rc = mysql_query($qc) or die (mysql_error().' '.$qc);
								}
								
								$lastcol = $cell->getColumn();
								$lastcolIndex = PHPExcel_Cell::columnIndexFromString($lastcol); 
							}
						}
					
						
						if ($cell->getRow() > 1 && $col <= $lastcolIndex) {
							$rownum = $cell->getRow() - 1;
							if (!is_null($cell)) {
								$qu = 'select uid from '.$uploadfile.' where uid = '.$rownum;
								$ru = mysql_query($qu) or die (mysql_error().' '.$qu);
								$numrowsu = mysql_num_rows($ru);
								if ($numrowsu == 0) {
									$qi = 'insert into '.$uploadfile.' (x'.$cell->getColumn().') values ("'.$cell->getCalculatedValue().'")';
									$ri = mysql_query($qi) or die (mysql_error().' '.$qi);
								} else {
									$qi = 'update '.$uploadfile.' set x'.$cell->getColumn().' = "'.trim($cell->getCalculatedValue()).'" where uid = '.$rownum;
									$ri = mysql_query($qi) or die (mysql_error().' '.$qi);
								}
							}
						}
					} // foreach ($cellIterator as $cell)
				} // foreach ($worksheet->getRowIterator() as $row)
			} // foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
	
			$objPHPExcel->disconnectWorksheets();
			unset($objPHPExcel);

			
			?>
	      </tr>
        </table>
        </div>
        
        
</form>

<script>document.onkeypress = stopRKey;</script> 

<?php
	if($_REQUEST['savebutton'] == "Y") {
		
		  include_once("clientimport.php");
	  
		  $oImp = new clientimport;
		  
		  $oImp->xfirstname = strtoupper($_REQUEST['tfirstname']);
		  $oImp->xmiddlename = strtoupper($_REQUEST['tmiddlename']);
		  $oImp->xlastname = strtoupper($_REQUEST['tlastname']);
		  $oImp->xpreferredname = strtoupper($_REQUEST['tpreferredname']);
		  $oImp->xdob = strtoupper($_REQUEST['tdob']);
		  $oImp->xtitle = strtoupper($_REQUEST['ttitle']);
		  $oImp->xcategory = strtoupper($_REQUEST['tcategory']);
		  $oImp->xpadvisor = strtoupper($_REQUEST['tpadvisor']);
		  $oImp->xposition = strtoupper($_REQUEST['tposition']);
		  $oImp->xoccupation = strtoupper($_REQUEST['toccupation']);
		  $oImp->xphonehomecountry1 = strtoupper($_REQUEST['tphonehomecountry1']);
		  $oImp->xphonehomearea1 = strtoupper($_REQUEST['tphonehomearea1']);
		  $oImp->xphonehomenumber1 = strtoupper($_REQUEST['tphonehomenumber1']);
		  $oImp->xphonehomecountry2 = strtoupper($_REQUEST['tphonehomecountry2']);
		  $oImp->xphonehomearea2 = strtoupper($_REQUEST['tphonehomearea2']);
		  $oImp->xphonehomenumber2 = strtoupper($_REQUEST['tphonehomenumber2']);
		  $oImp->xmobilecountry1 = strtoupper($_REQUEST['tmobilecountry1']);
		  $oImp->xmobilecode1 = strtoupper($_REQUEST['tmobilecode1']);
		  $oImp->xmobilenumber1 = strtoupper($_REQUEST['tmobilenumber1']);
		  $oImp->xmobilecountry2 = strtoupper($_REQUEST['tmobilecountry2']);
		  $oImp->xmobilecode2 = strtoupper($_REQUEST['tmobilecode2']);
		  $oImp->xmobilenumber2 = strtoupper($_REQUEST['tmobilenumber2']);
		  $oImp->xphoneworkcountry1 = strtoupper($_REQUEST['tphoneworkcountry1']);
		  $oImp->xphoneworkarea1 = strtoupper($_REQUEST['tphoneworkarea1']);
		  $oImp->xphoneworknumber1 = strtoupper($_REQUEST['tphoneworknumber1']);
		  $oImp->xphoneworkcountry2 = strtoupper($_REQUEST['tphoneworkcountry2']);
		  $oImp->xphoneworkarea2 = strtoupper($_REQUEST['tphoneworkarea2']);
		  $oImp->xphoneworknumber2 = strtoupper($_REQUEST['tphoneworknumber2']);
		  $oImp->xphoneworkcountry3 = strtoupper($_REQUEST['tphoneworkcountry3']);
		  $oImp->xphoneworkarea3 = strtoupper($_REQUEST['tphoneworkarea3']);
		  $oImp->xphoneworknumber3 = strtoupper($_REQUEST['tphoneworknumber3']);
		  $oImp->xphoneworkcountry4 = strtoupper($_REQUEST['tphoneworkcountry4']);
		  $oImp->xphoneworkarea4 = strtoupper($_REQUEST['tphoneworkarea4']);
		  $oImp->xphoneworknumber4 = strtoupper($_REQUEST['tphoneworknumber4']);
		  $oImp->xfaxworkcountry = strtoupper($_REQUEST['tfaxworkcountry']);
		  $oImp->xfaxworkarea = strtoupper($_REQUEST['tfaxworkarea']);
		  $oImp->xfaxworknumber = strtoupper($_REQUEST['tfaxworknumber']);
		  $oImp->xemail1 = strtoupper($_REQUEST['temail1']);
		  $oImp->xemail2 = strtoupper($_REQUEST['temail2']);
		  $oImp->xhomestreetno = strtoupper($_REQUEST['thomestreetno']);
		  $oImp->xhomestreetname = strtoupper($_REQUEST['thomestreetname']);
		  $oImp->xhomestreetsuburb = strtoupper($_REQUEST['thomestreetsuburb']);
		  $oImp->xhomestreettown = strtoupper($_REQUEST['thomestreettown']);
		  $oImp->xhomestreetcountry = strtoupper($_REQUEST['thomestreetcountry']);
		  $oImp->xhomestreetpostcode = strtoupper($_REQUEST['thomestreetpostcode']);
		  $oImp->xhomepono = strtoupper($_REQUEST['thomeponumber']);
		  $oImp->xhomeposuburb = strtoupper($_REQUEST['thomeposuburb']);
		  $oImp->xhomepotown = strtoupper($_REQUEST['thomepotown']);
		  $oImp->xhomepopostcode = strtoupper($_REQUEST['thomepopostcode']);
		  $oImp->xhomepocountry = strtoupper($_REQUEST['thomepocountry']);
		  $oImp->xworkstreetno = strtoupper($_REQUEST['tworkstreetno']);
		  $oImp->xworkstreetname = strtoupper($_REQUEST['tworkstreetname']);
		  $oImp->xworkstreetsuburb = strtoupper($_REQUEST['tworkstreetsuburb']);
		  $oImp->xworkstreettown = strtoupper($_REQUEST['tworkstreettown']);
		  $oImp->xworkstreetcountry = strtoupper($_REQUEST['tworkstreetcountry']);
		  $oImp->xworkstreetpostcode = strtoupper($_REQUEST['tworkstreetpostcode']);
		  $oImp->xbillstreetno = strtoupper($_REQUEST['tbillstreetno']);
		  $oImp->xbillstreetname = strtoupper($_REQUEST['tbillstreetname']);
		  $oImp->xbillstreetsuburb = strtoupper($_REQUEST['tbillstreetsuburb']);
		  $oImp->xbillstreettown = strtoupper($_REQUEST['tbillstreettown']);
		  $oImp->xbillstreetcountry = strtoupper($_REQUEST['tbillstreetcountry']);
		  $oImp->xbillstreetpostcode = strtoupper($_REQUEST['tbillstreetpostcode']);
		  $oImp->xnotes1 = strtoupper($_REQUEST['tnotes1']);
		  $oImp->xnotes2 = strtoupper($_REQUEST['tnotes2']);
		  $oImp->xsourceno = strtoupper($_REQUEST['tsourceno']);
		  
		  $oImp->xa1firstname = strtoupper($_REQUEST['ta1firstname']);
		  $oImp->xa1lastname = strtoupper($_REQUEST['ta1lastname']);
		  $oImp->xa1dob = strtoupper($_REQUEST['ta1dob']);
		  $oImp->xa1preferredname = strtoupper($_REQUEST['ta1preferredname']);
		  
		  $oImp->xsid = $subscriber;
		  $oImp->xfile = $uploadfile;
		  $oImp->xuserid = $user_id;
		  $oImp->xcoyid = $coyid;
		  $oImp->xdc = $dc;
		  
		  
		  $oImp->updateClients();
		  
		  
		  echo '<script>';
		  //echo 'this.close();';
		  echo '</script>';
}
?>


</body>
</html>

