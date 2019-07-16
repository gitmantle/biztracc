<?php
session_start();

ini_set('display_errors', true);

$usersession = $_COOKIE['usersession'];
$dbs = "ken47109_kenny";

require("../../db.php");
mysql_select_db($dbs) or die(mysql_error());
$query = "select * from sessions where session_id = ".$usersession;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subid = $sub_id;

$fl = "../../documents/imports/".$subid."__clients.xlsx";

if (file_exists($fl)) {
	// PHPExcel_IOFactory 
	require_once '../../includes/phpexcel/Classes/PHPExcel/IOFactory.php';
	
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
  echo 'alert("'.$provder.' spreadsheet does not exist");';
  echo 'this.close();';
  echo '</script>';
} // file exists

$uploadfile = "ztmp".$user_id."_uploadclients";

$query = "drop table if exists ".$uploadfile;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$uploadfile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY )  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

require_once("../../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Process Client List Spreadsheet</title>
<link rel="stylesheet" href="../../includes/kenny.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

<script>
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function process() {
	var policyno = document.getElementById('tlastname').value;
	var ok = "Y";
	
	if (policyno == "") {
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



  <div id="heading" style="position:absolute;visibility:visible;top:1px;left:1px;height:49px;width:890px;background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
  <table width="900" border="0" align="center">
    <tr>
      <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u>Process Client List</u></label></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;">Type relevant column letter from Data Available against Data Accepted</label></td>
    </tr>
  </table>
  </div>
  <div id="accepted" style="position:absolute; visibility:visible; top:51px; left:1px; height:670px; width:490px; background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid; overflow:scroll">
		<table width="450" border="0">
		  <tr>
		    <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u>Data Accepted by Kenny</u></label></td>
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
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Last name</label></td>
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
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Gender</label></td>
		    <td><input type="text" name="tgender" id="tgender" size="5" onchange="getheader('pgender',this.value)">&nbsp;<input type="text" name="pgender" id="pgender" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Title</label></td>
		    <td><input type="text" name="ttitle" id="ttitle" size="5" onchange="getheader('ptitle',this.value)">&nbsp;<input type="text" name="ptitle" id="ptitle" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Smoker</label></td>
		    <td><input type="text" name="tsmoker" id="tsmoker" size="5" onchange="getheader('psmoker',this.value)">&nbsp;<input type="text" name="psmoker" id="psmoker" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Marital status</label></td>
		    <td><input type="text" name="tmaritalstatus" id="tmaritalstatus" size="5" onchange="getheader('pmaritalstatus',this.value)">&nbsp;<input type="text" name="pmaritalstatus" id="pmaritalstatus" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Country of origin</label></td>
		    <td><input type="text" name="tcountryorigin" id="tcountryorigin" size="5" onchange="getheader('pcountryorigin',this.value)">&nbsp;<input type="text" name="pcountryorigin" id="pcountryorigin" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Review month</label></td>
		    <td><input type="text" name="treviewmonth" id="treviewmonth" size="5" onchange="getheader('previewmonth',this.value)">&nbsp;<input type="text" name="previewmonth" id="previewmonth" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Category</label></td>
		    <td><input type="text" name="tcategory" id="tcategory" size="5" onchange="getheader('pcategory',this.value)">&nbsp;<input type="text" name="pcategory" id="pcategory" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owned by</label></td>
		    <td><input type="text" name="townedby" id="townedby" size="5" onchange="getheader('pownedby',this.value)">&nbsp;<input type="text" name="pownedby" id="pownedby" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Principal advisor</label></td>
		    <td><input type="text" name="tpadvisor" id="tpadvisor" size="5" onchange="getheader('ppadvisor',this.value)">&nbsp;<input type="text" name="ppadvisor" id="ppadvisor" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Next meeting date</label></td>
		    <td><input type="text" name="tnextmeeting" id="tnextmeeting" size="5" onchange="getheader('pnextmeeting',this.value)">&nbsp;<input type="text" name="pnextmeeting" id="pnextmeeting" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Employer</label></td>
		    <td><input type="text" name="temployer" id="temployer" size="5" onchange="getheader('pemployer',this.value)">&nbsp;<input type="text" name="pemployer" id="pemployer" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Type of Contract</label></td>
		    <td><input type="text" name="tcontract" id="tcontract" size="5" onchange="getheader('pcontract',this.value)">&nbsp;<input type="text" name="pcontract" id="pcontract" readonly></td>
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
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Notes 1</label></td>
		    <td><input type="text" name="tnotes1" id="tnotes1" size="5" onchange="getheader('pnotes1',this.value)">&nbsp;<input type="text" name="pnotes1" id="pnotes1" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Notes 2</label></td>
		    <td><input type="text" name="tnotes2" id="tnotes2" size="5" onchange="getheader('pnotes2',this.value)">&nbsp;<input type="text" name="pnotes2" id="pnotes2" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Source</label></td>
		    <td><input type="text" name="tsource" id="tsource" size="5" onchange="getheader('psource',this.value)">&nbsp;<input type="text" name="psource" id="psource" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Source Client Number</label></td>
		    <td><input type="text" name="tsourceno" id="tsourceno" size="5" onchange="getheader('psourceno',this.value)">&nbsp;<input type="text" name="psourceno" id="psourceno" readonly></td>
	      </tr>
          
          
          
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner First name</label></td>
		    <td><input type="text" name="tfirstnamep" id="tfirstnamep" size="5" onchange="getheader('pfirstnamep',this.value)">&nbsp;<input type="text" name="pfirstnamep" id="pfirstnamep" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Middle Name</label></td>
		    <td><input type="text" name="tmiddlenamep" id="tmiddlenamep" size="5" onchange="getheader('pmiddlenamep',this.value)">&nbsp;<input type="text" name="pmiddlenamep" id="pmiddlenamep" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Last name</label></td>
		    <td><input type="text" name="tlastnamep" id="tlastnamep" size="5" onchange="getheader('plastnamep',this.value)">&nbsp;<input type="text" name="plastnamep" id="plastnamep" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Preferred name</label></td>
		    <td><input type="text" name="tpreferrednamep" id="tpreferrednamep" size="5" onchange="getheader('ppreferrednamep',this.value)">&nbsp;<input type="text" name="ppreferrednamep" id="ppreferrednamep" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Date of birth</label></td>
		    <td><input type="text" name="tdobp" id="tdobp" size="5" onchange="getheader('pdobp',this.value)">&nbsp;<input type="text" name="pdobp" id="pdobp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Gender</label></td>
		    <td><input type="text" name="tgenderp" id="tgenderp" size="5" onchange="getheader('pgenderp',this.value)">&nbsp;<input type="text" name="pgenderp" id="pgenderp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Title</label></td>
		    <td><input type="text" name="ttitlep" id="ttitlep" size="5" onchange="getheader('ptitlep',this.value)">&nbsp;<input type="text" name="ptitlep" id="ptitlep" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Smoker</label></td>
		    <td><input type="text" name="tsmokerp" id="tsmokerp" size="5" onchange="getheader('psmokerp',this.value)">&nbsp;<input type="text" name="psmokerp" id="psmokerp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Marital status</label></td>
		    <td><input type="text" name="tmaritalstatusp" id="tmaritalstatusp" size="5" onchange="getheader('pmaritalstatusp',this.value)">&nbsp;<input type="text" name="pmaritalstatusp" id="pmaritalstatusp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Counry of origin</label></td>
		    <td><input type="text" name="tcountryoriginp" id="tcountryoriginp" size="5" onchange="getheader('pcountryoriginp',this.value)">&nbsp;<input type="text" name="pcountryoriginp" id="pcountryoriginp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Review month</label></td>
		    <td><input type="text" name="treviewmonthp" id="treviewmonthp" size="5" onchange="getheader('previewmonthp',this.value)">&nbsp;<input type="text" name="previewmonthp" id="previewmonthp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Category</label></td>
		    <td><input type="text" name="tcategoryp" id="tcategoryp" size="5" onchange="getheader('pcategoryp',this.value)">&nbsp;<input type="text" name="pcategoryp" id="pcategoryp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Owned by</label></td>
		    <td><input type="text" name="townedbyp" id="townedbyp" size="5" onchange="getheader('pownedbyp',this.value)">&nbsp;<input type="text" name="pownedbyp" id="pownedbyp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Principal advisor</label></td>
		    <td><input type="text" name="tpadvisorp" id="tpadvisorp" size="5" onchange="getheader('ppadvisorp',this.value)">&nbsp;<input type="text" name="ppadvisorp" id="ppadvisorp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Next meeting date</label></td>
		    <td><input type="text" name="tnextmeetingp" id="tnextmeetingp" size="5" onchange="getheader('pnextmeetingp',this.value)">&nbsp;<input type="text" name="pnextmeetingp" id="pnextmeetingp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Employer</label></td>
		    <td><input type="text" name="temployerp" id="temployerp" size="5" onchange="getheader('pemployerp',this.value)">&nbsp;<input type="text" name="pemployerp" id="pemployerp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Type of Contract</label></td>
		    <td><input type="text" name="tcontractp" id="tcontractp" size="5" onchange="getheader('pcontractp',this.value)">&nbsp;<input type="text" name="pcontractp" id="pcontractp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Position</label></td>
		    <td><input type="text" name="tpositionp" id="tpositionp" size="5" onchange="getheader('ppositionp',this.value)">&nbsp;<input type="text" name="ppositionp" id="ppositionp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Occupation</label></td>
		    <td><input type="text" name="toccupationp" id="toccupationp" size="5" onchange="getheader('poccupationp',this.value)">&nbsp;<input type="text" name="poccupationp" id="poccupationp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone home country 1</label></td>
		    <td><input type="text" name="tphonehomecountry1p" id="tphonehomecountry1p" size="5" onchange="getheader('pphonehomecountry1p',this.value)">&nbsp;<input type="text" name="pphonehomecountry1p" id="pphonehomecountry1p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone home area code 1</label></td>
		    <td><input type="text" name="tphonehomearea1p" id="tphonehomearea1p" size="5" onchange="getheader('pphonehomearea1p',this.value)">&nbsp;<input type="text" name="pphonehomearea1p" id="pphonehomearea1p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone home number 1</label></td>
		    <td><input type="text" name="tphonehomenumber1p" id="tphonehomenumber1p" size="5" onchange="getheader('pphonehomenumber1p',this.value)">&nbsp;<input type="text" name="pphonehomenumber1p" id="pphonehomenumber1p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone home country 2</label></td>
		    <td><input type="text" name="tphonehomecountry2p" id="tphonehomecountry2p" size="5" onchange="getheader('pphonehomecountry2p',this.value)">&nbsp;<input type="text" name="pphonehomecountry2p" id="pphonehomecountry2p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone home area 2</label></td>
		    <td><input type="text" name="tphonehomearea2p" id="tphonehomearea2p" size="5" onchange="getheader('pphonehomearea2p',this.value)">&nbsp;<input type="text" name="pphonehomearea2p" id="pphonehomearea2p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone home number 2</label></td>
		    <td><input type="text" name="tphonehomenumber2p" id="tphonehomenumber2p" size="5" onchange="getheader('pphonehomenumber2p',this.value)">&nbsp;<input type="text" name="pphonehomenumber2p" id="pphonehomenumber2p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Mobile country 1</label></td>
		    <td><input type="text" name="tmobilecountry1p" id="tmobilecountry1p" size="5" onchange="getheader('pmobilecountry1p',this.value)">&nbsp;<input type="text" name="pmobilecountry1p" id="pmobilecountry1p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Mobile code 1</label></td>
		    <td><input type="text" name="tmobilecode1p" id="tmobilecode1p" size="5" onchange="getheader('pmobilecode1p',this.value)">&nbsp;<input type="text" name="pmobilecode1p" id="pmobilecode1p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Mobile number 1</label></td>
		    <td><input type="text" name="tmobilenumber1p" id="tmobilenumber1p" size="5" onchange="getheader('pmobilenumber1p',this.value)">&nbsp;<input type="text" name="pmobilenumber1p" id="pmobilenumber1p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Mobile country 2</label></td>
		    <td><input type="text" name="tmobilecountry2p" id="tmobilecountry2p" size="5" onchange="getheader('pmobilecountry2p',this.value)">&nbsp;<input type="text" name="pmobilecountry2p" id="pmobilecountry2p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Mobile code 2</label></td>
		    <td><input type="text" name="tmobilecode2p" id="tmobilecode2p" size="5" onchange="getheader('pmobilecode2p',this.value)">&nbsp;<input type="text" name="pmobilecode2p" id="pmobilecode2p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Mobile number 2</label></td>
		    <td><input type="text" name="tmobilenumber2p" id="tmobilenumber2p" size="5" onchange="getheader('pmobilenumber2p',this.value)">&nbsp;<input type="text" name="pmobilenumber2p" id="pmobilenumber2pp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone work country 1</label></td>
		    <td><input type="text" name="tphoneworkcountry1p" id="tphoneworkcountry1p" size="5" onchange="getheader('pphoneworkcountry1p',this.value)">&nbsp;<input type="text" name="pphoneworkcountry1p" id="pphoneworkcountry1p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone work area code 1</label></td>
		    <td><input type="text" name="tphoneworkarea1p" id="tphoneworkarea1p" size="5" onchange="getheader('pphoneworkarea1p',this.value)">&nbsp;<input type="text" name="pphoneworkarea1p" id="pphoneworkarea1p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone work number 1</label></td>
		    <td><input type="text" name="tphoneworknumber1p" id="tphoneworknumber1p" size="5" onchange="getheader('pphoneworknumber1p',this.value)">&nbsp;<input type="text" name="pphoneworknumber1p" id="pphoneworknumber1p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone work country 2</label></td>
		    <td><input type="text" name="tphoneworkcountry2p" id="tphoneworkcountry2p" size="5" onchange="getheader('pphoneworkcountry2p',this.value)">&nbsp;<input type="text" name="pphoneworkcountry2p" id="pphoneworkcountry2p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone work area code 2</label></td>
		    <td><input type="text" name="tphoneworkarea2p" id="tphoneworkarea2p" size="5" onchange="getheader('pphoneworkarea2p',this.value)">&nbsp;<input type="text" name="pphoneworkarea2p" id="pphoneworkarea2p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone work number 2</label></td>
		    <td><input type="text" name="tphoneworknumber2p" id="tphoneworknumber2p" size="5" onchange="getheader('pphoneworknumber2p',this.value)">&nbsp;<input type="text" name="pphoneworknumber2p" id="pphoneworknumber2p" readonly></td>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone work country 3</label></td>
		    <td><input type="text" name="tphoneworkcountry3p" id="tphoneworkcountry3p" size="5" onchange="getheader('pphoneworkcountry3p',this.value)">&nbsp;<input type="text" name="pphoneworkcountry3p" id="pphoneworkcountry3p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone work area code 3</label></td>
		    <td><input type="text" name="tphoneworkarea3p" id="tphoneworkarea3p" size="5" onchange="getheader('pphoneworkarea3p',this.value)">&nbsp;<input type="text" name="pphoneworkarea3p" id="pphoneworkarea3p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone work number 3</label></td>
		    <td><input type="text" name="tphoneworknumber3p" id="tphoneworknumber3p" size="5" onchange="getheader('pphoneworknumber3p',this.value)">&nbsp;<input type="text" name="pphoneworknumber3p" id="pphoneworknumber3p" readonly></td>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone work country 4</label></td>
		    <td><input type="text" name="tphoneworkcountry4p" id="tphoneworkcountry4p" size="5" onchange="getheader('pphoneworkcountry4p',this.value)">&nbsp;<input type="text" name="pphoneworkcountry4p" id="pphoneworkcountry4p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone work area code 4</label></td>
		    <td><input type="text" name="tphoneworkarea4p" id="tphoneworkarea4p" size="5" onchange="getheader('pphoneworkarea4p',this.value)">&nbsp;<input type="text" name="pphoneworkarea4p" id="pphoneworkarea4p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Phone work number 4</label></td>
		    <td><input type="text" name="tphoneworknumber4p" id="tphoneworknumber4p" size="5" onchange="getheader('pphoneworknumber4p',this.value)">&nbsp;<input type="text" name="pphoneworknumber4p" id="pphoneworknumber4p" readonly></td>
	      </tr>
 		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Fax work country</label></td>
		    <td><input type="text" name="tfaxworkcountryp" id="tfaxworkcountryp" size="5" onchange="getheader('pfaxworkcountryp',this.value)">&nbsp;<input type="text" name="pfaxworkcountryp" id="pfaxworkcountryp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Fax work area code</label></td>
		    <td><input type="text" name="tfaxworkareap" id="tfaxworkareap" size="5" onchange="getheader('pfaxworkareap',this.value)">&nbsp;<input type="text" name="pfaxworkareap" id="pfaxworkareap" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Fax work number</label></td>
		    <td><input type="text" name="tfaxworknumberp" id="tfaxworknumberp" size="5" onchange="getheader('pfaxworknumberp',this.value)">&nbsp;<input type="text" name="pfaxworknumberp" id="pfaxworknumberp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Email 1</label></td>
		    <td><input type="text" name="temail1p" id="temail1p" size="5" onchange="getheader('pemail1p',this.value)">&nbsp;<input type="text" name="pemail1p" id="pemail1p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Email 2</label></td>
		    <td><input type="text" name="temail2p" id="temail2p" size="5" onchange="getheader('pemail2p',this.value)">&nbsp;<input type="text" name="pemail2p" id="pemail2p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Home street number</label></td>
		    <td><input type="text" name="thomestreetnop" id="thomestreetnop" size="5" onchange="getheader('phomestreetnop',this.value)">&nbsp;<input type="text" name="phomestreetnop" id="phomestreetnop" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Home street name</label></td>
		    <td><input type="text" name="thomestreetnamep" id="thomestreetnamep" size="5" onchange="getheader('phomestreetnamep',this.value)">&nbsp;<input type="text" name="phomestreetnamep" id="phomestreetnamep" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Home street suburb</label></td>
		    <td><input type="text" name="thomestreetsuburbp" id="thomestreetsuburbp" size="5" onchange="getheader('phomestreetsuburbp',this.value)">&nbsp;<input type="text" name="phomestreetsuburbp" id="phomestreetsuburbp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Home street town</label></td>
		    <td><input type="text" name="thomestreettownp" id="thomestreettownp" size="5" onchange="getheader('phomestreettownp',this.value)">&nbsp;<input type="text" name="phomestreettownp" id="phomestreettownp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Home street country</label></td>
		    <td><input type="text" name="thomestreetcountryp" id="thomestreetcountryp" size="5" onchange="getheader('phomestreetcountryp',this.value)">&nbsp;<input type="text" name="phomestreetcountryp" id="phomestreetcountryp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Home street postcode</label></td>
		    <td><input type="text" name="thomestreetpostcodep" id="thomestreetpostcodep" size="5" onchange="getheader('phomestreetpostcodep',this.value)">&nbsp;<input type="text" name="phomestreetpostcodep" id="phomestreetpostcodep" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Home postbox number</label></td>
		    <td><input type="text" name="thomeponumberp" id="thomeponumberp" size="5" onchange="getheader('phomeponumberp',this.value)">&nbsp;<input type="text" name="phomeponumberp" id="phomeponumberp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Home postbox suburb</label></td>
		    <td><input type="text" name="thomeposuburbp" id="thomeposuburbp" size="5" onchange="getheader('phomeposuburbp',this.value)">&nbsp;<input type="text" name="phomeposuburbp" id="phomeposuburbp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Home postbox town</label></td>
		    <td><input type="text" name="thomepotownp" id="thomepotownp" size="5" onchange="getheader('phomepotownp',this.value)">&nbsp;<input type="text" name="phomepotownp" id="phomepotownp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Home postbox postcode</label></td>
		    <td><input type="text" name="thomepopostcodepp" id="thomepopostcode" size="5" onchange="getheader('phomepopostcodep',this.value)">&nbsp;<input type="text" name="phomepopostcodep" id="phomepopostcodep" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Home postbox country</label></td>
		    <td><input type="text" name="thomepocountryp" id="thomepocountryp" size="5" onchange="getheader('phomepocountryp',this.value)">&nbsp;<input type="text" name="phomepocountryp" id="phomepocountryp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Work street number</label></td>
		    <td><input type="text" name="tworkstreetnop" id="tworkstreetnop" size="5" onchange="getheader('pworkstreetnop',this.value)">&nbsp;<input type="text" name="pworkstreetnop" id="pworkstreetnop" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Work street name</label></td>
		    <td><input type="text" name="tworkstreetnamep" id="tworkstreetnamep" size="5" onchange="getheader('pworkstreetnamep',this.value)">&nbsp;<input type="text" name="pworkstreetnamep" id="pworkstreetnamep" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Work street suburb</label></td>
		    <td><input type="text" name="tworkstreetsuburbp" id="tworkstreetsuburbp" size="5" onchange="getheader('pworkstreetsuburbp',this.value)">&nbsp;<input type="text" name="pworkstreetsuburbp" id="pworkstreetsuburbp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Work street town</label></td>
		    <td><input type="text" name="tworkstreettownp" id="tworkstreettownp" size="5" onchange="getheader('pworkstreettownp',this.value)">&nbsp;<input type="text" name="pworkstreettownp" id="pworkstreettownp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Work street postcode</label></td>
		    <td><input type="text" name="tworkstreetpostcodep" id="tworkstreetpostcode" size="5" onchange="getheader('pworkstreetpostcodep',this.value)">&nbsp;<input type="text" name="pworkstreetpostcodep" id="pworkstreetpostcodep" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Work street country</label></td>
		    <td><input type="text" name="tworkstreetcountryp" id="tworkstreetcountryp" size="5" onchange="getheader('pworkstreetcountryp',this.value)">&nbsp;<input type="text" name="pworkstreetcountryp" id="pworkstreetcountryp" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Notes 1</label></td>
		    <td><input type="text" name="tnotes1p" id="tnotes1p" size="5" onchange="getheader('pnotes1p',this.value)">&nbsp;<input type="text" name="pnotes1p" id="pnotes1p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Notes 2</label></td>
		    <td><input type="text" name="tnotes2p" id="tnotes2p" size="5" onchange="getheader('pnotes2p',this.value)">&nbsp;<input type="text" name="pnotes2p" id="pnotes2p" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Partner Source Client Number</label></td>
		    <td><input type="text" name="tsourcenop" id="tsourcenop" size="5" onchange="getheader('psourcenop',this.value)">&nbsp;<input type="text" name="psourcenop" id="psourcenop" readonly></td>
	      </tr>
          
          
          
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 1 First name</label></td>
		    <td><input type="text" name="ta1firstname" id="ta1firstname" size="5" onchange="getheader('pa1firstname',this.value)">&nbsp;<input type="text" name="pa1firstname" id="pa1firstname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 1 Last name</label></td>
		    <td><input type="text" name="ta1lastname" id="ta1lastname" size="5" onchange="getheader('pa1lastname',this.value)">&nbsp;<input type="text" name="pa1lastname" id="pa1lastname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 1 Date of Birth</label></td>
		    <td><input type="text" name="ta1dob" id="ta1dob" size="5" onchange="getheader('pa1dob',this.value)">&nbsp;<input type="text" name="pa1dob" id="pa1dob" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 1 Gender</label></td>
		    <td><input type="text" name="ta1gender" id="ta1gender" size="5" onchange="getheader('pa1gender',this.value)">&nbsp;<input type="text" name="pa1gender" id="pa1gender" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 1 Relationship</label></td>
		    <td><input type="text" name="ta1relationship" id="ta1relationship" size="5" onchange="getheader('pa1relationship',this.value)">&nbsp;<input type="text" name="pa1relationship" id="pa1relationship" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 1 Preferred name</label></td>
		    <td><input type="text" name="ta1preferredname" id="ta1preferredname" size="5" onchange="getheader('pa1preferredname',this.value)">&nbsp;<input type="text" name="pa1preferredname" id="pa1preferredname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 2 First name</label></td>
		    <td><input type="text" name="ta2firstname" id="ta2firstname" size="5" onchange="getheader('pa2firstname',this.value)">&nbsp;<input type="text" name="pa2firstname" id="pa2firstname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 2 Last name</label></td>
		    <td><input type="text" name="ta2lastname" id="ta2lastname" size="5" onchange="getheader('pa2lastname',this.value)">&nbsp;<input type="text" name="pa2lastname" id="pa2lastname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 2 Date of Birth</label></td>
		    <td><input type="text" name="ta2dob" id="ta2dob" size="5" onchange="getheader('pa2dob',this.value)">&nbsp;<input type="text" name="pa2dob" id="pa2dob" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 2 Gender</label></td>
		    <td><input type="text" name="ta2gender" id="ta2gender" size="5" onchange="getheader('pa2gender',this.value)">&nbsp;<input type="text" name="pa2gender" id="pa2gender" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 2 Relationship</label></td>
		    <td><input type="text" name="ta2relationship" id="ta2relationship" size="5" onchange="getheader('pa2relationship',this.value)">&nbsp;<input type="text" name="pa2relationship" id="pa2relationship" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 2 Preferred name</label></td>
		    <td><input type="text" name="ta2preferredname" id="ta2preferredname" size="5" onchange="getheader('pa2preferredname',this.value)">&nbsp;<input type="text" name="pa2preferredname" id="pa2preferredname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 3 First name</label></td>
		    <td><input type="text" name="ta3firstname" id="ta3firstname" size="5" onchange="getheader('pa3firstname',this.value)">&nbsp;<input type="text" name="pa3firstname" id="pa3firstname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 3 Last name</label></td>
		    <td><input type="text" name="ta3lastname" id="ta3lastname" size="5" onchange="getheader('pa3lastname',this.value)">&nbsp;<input type="text" name="pa3lastname" id="pa3lastname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 3 Date of Birth</label></td>
		    <td><input type="text" name="ta3dob" id="ta3dob" size="5" onchange="getheader('pa3dob',this.value)">&nbsp;<input type="text" name="pa3dob" id="pa3dob" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 3 Gender</label></td>
		    <td><input type="text" name="ta3gender" id="ta3gender" size="5" onchange="getheader('pa3gender',this.value)">&nbsp;<input type="text" name="pa3gender" id="pa3gender" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 3 Relationship</label></td>
		    <td><input type="text" name="ta3relationship" id="ta3relationship" size="5" onchange="getheader('pa3relationship',this.value)">&nbsp;<input type="text" name="pa3relationship" id="pa3relationship" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 3 Preferred name</label></td>
		    <td><input type="text" name="ta3preferredname" id="ta3preferredname" size="5" onchange="getheader('pa3preferredname',this.value)">&nbsp;<input type="text" name="pa3preferredname" id="pa3preferredname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 4 First name</label></td>
		    <td><input type="text" name="ta4firstname" id="ta4firstname" size="5" onchange="getheader('pa4firstname',this.value)">&nbsp;<input type="text" name="pa4firstname" id="pa4firstname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 4 Last name</label></td>
		    <td><input type="text" name="ta4lastname" id="ta4lastname" size="5" onchange="getheader('pa4lastname',this.value)">&nbsp;<input type="text" name="pa4lastname" id="pa4lastname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 4 Date of Birth</label></td>
		    <td><input type="text" name="ta4dob" id="ta4dob" size="5" onchange="getheader('pa4dob',this.value)">&nbsp;<input type="text" name="pa4dob" id="pa4dob" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 4 Gender</label></td>
		    <td><input type="text" name="ta4gender" id="ta4gender" size="5" onchange="getheader('pa4gender',this.value)">&nbsp;<input type="text" name="pa4gender" id="pa4gender" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 4 Relationship</label></td>
		    <td><input type="text" name="ta4relationship" id="ta4relationship" size="5" onchange="getheader('pa4relationship',this.value)">&nbsp;<input type="text" name="pa4relationship" id="pa4relationship" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Associate 4 Preferred name</label></td>
		    <td><input type="text" name="ta4preferredname" id="ta4preferredname" size="5" onchange="getheader('pa4preferredname',this.value)">&nbsp;<input type="text" name="pa4preferredname" id="pa4preferredname" readonly></td>
	      </tr>
          
		  <tr>
		    <td class="boxlabel">&nbsp;</td>
		    <td><label>
		      <input type="button" name="btnprocess" id="btnprocess" value="Process" onclick="process()">
	        </label></td>
	      </tr>
	    </table>
  </div>
  		<div id="available" style="position:absolute;visibility:visible;top:51px;left:497px;height:670px;width:394px;background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid; overflow:scroll">
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
		
		
		  include_once("../includes/clientimport.php");
		  
		  $oImp = new clientimport;
		  
		  $oImp->xfirstname = strtoupper($_REQUEST['tfirstname']);
		  $oImp->xmiddlename = strtoupper($_REQUEST['tmiddlename']);
		  $oImp->xlastname = strtoupper($_REQUEST['tlastname']);
		  $oImp->xpreferredname = strtoupper($_REQUEST['tpreferredname']);
		  $oImp->xdob = strtoupper($_REQUEST['tdob']);
		  $oImp->xgender = strtoupper($_REQUEST['tgender']);
		  $oImp->xtitle = strtoupper($_REQUEST['ttitle']);
		  $oImp->xsmoker = strtoupper($_REQUEST['tsmoker']);
		  $oImp->xmaritalstatus = strtoupper($_REQUEST['tmaritalstatus']);
		  $oImp->xcountryoforigin = strtoupper($_REQUEST['tcountryoforigin']);
		  $oImp->xreviewmonth = strtoupper($_REQUEST['treviewmonth']);
		  $oImp->xcategory = strtoupper($_REQUEST['tcategory']);
		  $oImp->xownedby = strtoupper($_REQUEST['townedby']);
		  $oImp->xpadvisor = strtoupper($_REQUEST['tpadvisor']);
		  $oImp->xnextmeeting = strtoupper($_REQUEST['tnextmeeting']);
		  $oImp->xemployer = strtoupper($_REQUEST['temployer']);
		  $oImp->xcontract = strtoupper($_REQUEST['tcontract']);
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
		  $oImp->xhomeponumber = strtoupper($_REQUEST['thomeponumber']);
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
		  $oImp->xnotes1 = strtoupper($_REQUEST['tnotes1']);
		  $oImp->xnotes2 = strtoupper($_REQUEST['tnotes2']);
		  $oImp->xsource = strtoupper($_REQUEST['tsource']);
		  $oImp->xsourceno = strtoupper($_REQUEST['tsourceno']);
		  
		  $oImp->xfirstnamep = strtoupper($_REQUEST['tfirstnamep']);
		  $oImp->xmiddlenamep = strtoupper($_REQUEST['tmiddlenamep']);
		  $oImp->xlastnamep = strtoupper($_REQUEST['tlastnamep']);
		  $oImp->xpreferrednamep = strtoupper($_REQUEST['tpreferrednamep']);
		  $oImp->xdobp = strtoupper($_REQUEST['tdobp']);
		  $oImp->xgenderp = strtoupper($_REQUEST['tgenderp']);
		  $oImp->xtitlep = strtoupper($_REQUEST['ttitlep']);
		  $oImp->xsmokerp = strtoupper($_REQUEST['tsmokerp']);
		  $oImp->xmaritalstatusp = strtoupper($_REQUEST['tmaritalstatusp']);
		  $oImp->xcountryoforiginp = strtoupper($_REQUEST['tcountryoforiginp']);
		  $oImp->xreviewmonthp = strtoupper($_REQUEST['treviewmonthp']);
		  $oImp->xcategoryp = strtoupper($_REQUEST['tcategoryp']);
		  $oImp->xownedbyp = strtoupper($_REQUEST['townedbyp']);
		  $oImp->xpadvisorp = strtoupper($_REQUEST['tpadvisorp']);
		  $oImp->xnextmeetingp = strtoupper($_REQUEST['tnextmeetingp']);
		  $oImp->xemployerp = strtoupper($_REQUEST['temployerp']);
		  $oImp->xcontractp = strtoupper($_REQUEST['tcontractp']);
		  $oImp->xpositionp = strtoupper($_REQUEST['tpositionp']);
		  $oImp->xoccupationp = strtoupper($_REQUEST['toccupationp']);
		  $oImp->xphonehomecountry1p = strtoupper($_REQUEST['tphonehomecountry1p']);
		  $oImp->xphonehomearea1p = strtoupper($_REQUEST['tphonehomearea1p']);
		  $oImp->xphonehomenumber1p = strtoupper($_REQUEST['tphonehomenumber1p']);
		  $oImp->xphonehomecountry2p = strtoupper($_REQUEST['tphonehomecountry2p']);
		  $oImp->xphonehomearea2p = strtoupper($_REQUEST['tphonehomearea2p']);
		  $oImp->xphonehomenumber2p = strtoupper($_REQUEST['tphonehomenumber2p']);
		  $oImp->xmobilecountry1p = strtoupper($_REQUEST['tmobilecountry1p']);
		  $oImp->xmobilecode1p = strtoupper($_REQUEST['tmobilecode1p']);
		  $oImp->xmobilenumber1p = strtoupper($_REQUEST['tmobilenumber1p']);
		  $oImp->xmobilecountry2p = strtoupper($_REQUEST['tmobilecountry2p']);
		  $oImp->xmobilecode2p = strtoupper($_REQUEST['tmobilecode2p']);
		  $oImp->xmobilenumber2p = strtoupper($_REQUEST['tmobilenumber2p']);
		  $oImp->xphoneworkcountry1p = strtoupper($_REQUEST['tphoneworkcountry1p']);
		  $oImp->xphoneworkarea1p = strtoupper($_REQUEST['tphoneworkarea1p']);
		  $oImp->xphoneworknumber1p = strtoupper($_REQUEST['tphoneworknumber1p']);
		  $oImp->xphoneworkcountry2p = strtoupper($_REQUEST['tphoneworkcountry2p']);
		  $oImp->xphoneworkarea2p = strtoupper($_REQUEST['tphoneworkarea2p']);
		  $oImp->xphoneworknumber2p = strtoupper($_REQUEST['tphoneworknumber2p']);
		  $oImp->xphoneworkcountry3p = strtoupper($_REQUEST['tphoneworkcountry3p']);
		  $oImp->xphoneworkarea3p = strtoupper($_REQUEST['tphoneworkarea3p']);
		  $oImp->xphoneworknumber3p = strtoupper($_REQUEST['tphoneworknumber3p']);
		  $oImp->xphoneworkcountry4p = strtoupper($_REQUEST['tphoneworkcountry4p']);
		  $oImp->xphoneworkarea4p = strtoupper($_REQUEST['tphoneworkarea4p']);
		  $oImp->xphoneworknumber4p = strtoupper($_REQUEST['tphoneworknumber4p']);
		  $oImp->xfaxworkcountryp = strtoupper($_REQUEST['tfaxworkcountryp']);
		  $oImp->xfaxworkareap = strtoupper($_REQUEST['tfaxworkareap']);
		  $oImp->xfaxworknumberp = strtoupper($_REQUEST['tfaxworknumberp']);
		  $oImp->xemail1p = strtoupper($_REQUEST['temail1p']);
		  $oImp->xemail2p = strtoupper($_REQUEST['temail2p']);
		  $oImp->xhomestreetnop = strtoupper($_REQUEST['thomestreetnop']);
		  $oImp->xhomestreetnamep = strtoupper($_REQUEST['thomestreetnamep']);
		  $oImp->xhomestreetsuburbp = strtoupper($_REQUEST['thomestreetsuburbp']);
		  $oImp->xhomestreettownp = strtoupper($_REQUEST['thomestreettownp']);
		  $oImp->xhomestreetcountryp = strtoupper($_REQUEST['thomestreetcountryp']);
		  $oImp->xhomestreetpostcodep = strtoupper($_REQUEST['thomestreetpostcodep']);
		  $oImp->xhomeponumberp = strtoupper($_REQUEST['thomeponumberp']);
		  $oImp->xhomeposuburbp = strtoupper($_REQUEST['thomeposuburbp']);
		  $oImp->xhomepotownp = strtoupper($_REQUEST['thomepotownp']);
		  $oImp->xhomepopostcodep = strtoupper($_REQUEST['thomepopostcodep']);
		  $oImp->xhomepocountryp = strtoupper($_REQUEST['thomepocountryp']);
		  $oImp->xworkstreetnop = strtoupper($_REQUEST['tworkstreetnop']);
		  $oImp->xworkstreetnamep = strtoupper($_REQUEST['tworkstreetnamep']);
		  $oImp->xworkstreetsuburbp = strtoupper($_REQUEST['tworkstreetsuburbp']);
		  $oImp->xworkstreettownp = strtoupper($_REQUEST['tworkstreettownp']);
		  $oImp->xworkstreetcountryp = strtoupper($_REQUEST['tworkstreetcountryp']);
		  $oImp->xworkstreetpostcodep = strtoupper($_REQUEST['tworkstreetpostcodep']);
		  $oImp->xnotes1p = strtoupper($_REQUEST['tnotes1p']);
		  $oImp->xnotes2p = strtoupper($_REQUEST['tnotes2p']);
		  $oImp->xsourcenop = strtoupper($_REQUEST['tsourcenop']);
		  
		  $oImp->xa1firstname = strtoupper($_REQUEST['ta1firstname']);
		  $oImp->xa1lastname = strtoupper($_REQUEST['ta1lastname']);
		  $oImp->xa1dob = strtoupper($_REQUEST['ta1dob']);
		  $oImp->xa1gender = strtoupper($_REQUEST['ta1gender']);
		  $oImp->xa1relationship = strtoupper($_REQUEST['ta1relationship']);
		  $oImp->xa1preferredname = strtoupper($_REQUEST['ta1preferredname']);
		  $oImp->xa2firstname = strtoupper($_REQUEST['ta2firstname']);
		  $oImp->xa2lastname = strtoupper($_REQUEST['ta2lastname']);
		  $oImp->xa2dob = strtoupper($_REQUEST['ta2dob']);
		  $oImp->xa2gender = strtoupper($_REQUEST['ta2gender']);
		  $oImp->xa2relationship = strtoupper($_REQUEST['ta2relationship']);
		  $oImp->xa2preferredname = strtoupper($_REQUEST['ta2preferredname']);
		  $oImp->xa3firstname = strtoupper($_REQUEST['ta3firstname']);
		  $oImp->xa3lastname = strtoupper($_REQUEST['ta3lastname']);
		  $oImp->xa3dob = strtoupper($_REQUEST['ta3dob']);
		  $oImp->xa3gender = strtoupper($_REQUEST['ta3gender']);
		  $oImp->xa3relationship = strtoupper($_REQUEST['ta3relationship']);
		  $oImp->xa3preferredname = strtoupper($_REQUEST['ta3preferredname']);
		  $oImp->xa4firstname = strtoupper($_REQUEST['ta4firstname']);
		  $oImp->xa4lastname = strtoupper($_REQUEST['ta4lastname']);
		  $oImp->xa4dob = strtoupper($_REQUEST['ta4dob']);
		  $oImp->xa4gender = strtoupper($_REQUEST['ta4gender']);
		  $oImp->xa4relationship = strtoupper($_REQUEST['ta4relationship']);
		  $oImp->xa4preferredname = strtoupper($_REQUEST['ta4preferredname']);
		  
		  
		  $oImp->xsid = $subid;
		  $oImp->xfile = $uploadfile;
		  $oImp->xuserid = $user_id;

		  $oImp->updateClients();
		  
		  echo '<script>';
		  echo 'this.close();';
		  echo '</script>';
}
?>


</body>
</html>

