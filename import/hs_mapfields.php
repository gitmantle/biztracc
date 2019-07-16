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

$ledger = $_REQUEST['ledger'];

$fl = "../import/documents/".$coyid.'__'.$ledger.".xlsx";

if (file_exists($fl)) {
	// PHPExcel_IOFactory 
	require_once '../includes/phpexcel/Classes/PHPExcel/IOFactory.php';
	
	$inputFileName = $fl;
	
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	/**  Advise the Reader that we only want to load cell data  **/
	$objReader->setReadDataOnly(true);
	try {
		// Load $inputFileName to a PHPExcel Object 
		$objPHPExcel = $objReader->load($fl); 
	} catch(Exception $e) {
		die('Error loading file: '.$e->getMessage());
	}

} else { // file not exists
  echo '<script>';
  echo 'alert("'.$fl.' spreadsheet does not exist");';
  echo 'this.close();';
  echo '</script>';
} // file exists


$uploadfile = "ztmp".$user_id."_uploadpolicy";

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
<title>Process Provider Policy Spreadsheet</title>
<link rel="stylesheet" href="../../includes/kenny.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

<script>
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function process() {
	var policyno = document.getElementById('tpolicynumber').value;
	var olname = document.getElementById('tolastname').value;
	var ilname = document.getElementById('tilastname').value;
	
	var ok = "Y";
	
	if (policyno == "") {
		alert('Please enter column that contains the policy number');
		ok = 'N';
		return false;
	}
	
	if (olname+ilname == "") {
		alert('Please enter at least one owner or insured last name');
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

<script>
	var x = confirm("Confirm you have run a backup immediately before you entered this routine");
	if (x == false) {
		this.close();
	}
</script>



  <div id="heading" style="position:absolute;visibility:visible;top:1px;left:1px;height:49px;width:890px;background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
  <table width="900" border="0" align="center">
    <tr>
      <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u>Process <?php echo $provider; ?></u></label></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;">Type relevant column letter from Data Available against Data Accepted</label></td>
    </tr>
  </table>
  </div>
  <div id="accepted" style="position:absolute; visibility:visible; top:51px; left:3px; height:670px; width:440px; background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid; overflow:scroll">
		<table width="450" border="0">
		  <tr>
		    <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u>Data Accepted by Kenny</u></label></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Policy Number</label></td>
		    <td><input type="text" name="tpolicynumber" id="tpolicynumber" size="5" onchange="getheader('ppolicynumber',this.value)">&nbsp;<input type="text" name="ppolicynumber" id="ppolicynumber" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Advisor</label></td>
		    <td><input type="text" name="tadvisor" id="tadvisor" size="5" onchange="getheader('padvisor',this.value)">&nbsp;<input type="text" name="padvisor" id="padvisor" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Commencement Date</label></td>
		    <td><input type="text" name="tcommencedate" id="tcommencedate" size="5" onchange="getheader('pcommencedate',this.value)">&nbsp;<input type="text" name="pcommencedate" id="pcommencedate" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Ceases</label></td>
		    <td><input type="text" name="tceases" id="tceases" size="5" onchange="getheader('pceases',this.value)">&nbsp;<input type="text" name="pceases" id="pceases" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Renewal Date</label></td>
		    <td><input type="text" name="trenewdate" id="trenewdate" size="5" onchange="getheader('prenewdate',this.value)">&nbsp;<input type="text" name="prenewdate" id="prenewdate" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Review Month</label></td>
		    <td><input type="text" name="treviewmonth" id="treviewmonth" size="5" onchange="getheader('ppreviewmonth',this.value)">&nbsp;<input type="text" name="previewmonth" id="previewmonth" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Policy Status</label></td>
		    <td><input type="text" name="tstatus" id="tstatus" size="5" onchange="getheader('pstatus',this.value)">&nbsp;<input type="text" name="pstatus" id="pstatus" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Payment Period</label></td>
		    <td><input type="text" name="tpayperiod" id="tpayperiod" size="5" onchange="getheader('ppayperiod',this.value)">&nbsp;<input type="text" name="ppayperiod" id="ppayperiod" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Cover Type</label></td>
		    <td><input type="text" name="tcovertype" id="tcovertype" size="5" onchange="getheader('pcovertype',this.value)">&nbsp;<input type="text" name="pcovertype" id="pcovertype" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Benefit</label></td>
		    <td><input type="text" name="tbenefit" id="tbenefit" size="5" onchange="getheader('pbenefit',this.value)">&nbsp;<input type="text" name="pbenefit" id="pbenefit" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">FUM</label></td>
		    <td><input type="text" name="tfum" id="tfum" size="5" onchange="getheader('pfum',this.value)">&nbsp;<input type="text" name="pfum" id="pfum" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Annual Premium</label></td>
		    <td><input type="text" name="tpremium" id="tpremium" size="5" onchange="getheader('ppremium',this.value)">&nbsp;<input type="text" name="ppremium" id="ppremium" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Benefit Period</label></td>
		    <td><input type="text" name="tbenefitperiod" id="tbenefitperiod" size="5" onchange="getheader('pbenefitperiod',this.value)">&nbsp;<input type="text" name="pbenefitperiod" id="pbenefitperiod" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Stand Down Period</label></td>
		    <td><input type="text" name="tstanddown" id="tstanddown" size="5" onchange="getheader('pstanddown',this.value)">&nbsp;<input type="text" name="pstanddown" id="pstanddown" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owner Title</label></td>
		    <td><input type="text" name="totitle" id="totitle" size="5" onchange="getheader('potitle',this.value)">&nbsp;<input type="text" name="potitle" id="potitle" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owner First Name</label></td>
		    <td><input type="text" name="tofirstname" id="tofirstname" size="5" onchange="getheader('pofirstname',this.value)">&nbsp;<input type="text" name="pofirstname" id="pofirstname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owner Middle Name</label></td>
		    <td><input type="text" name="tomiddlename" id="tomiddlename" size="5" onchange="getheader('pomiddlename',this.value)">&nbsp;<input type="text" name="pomiddlename" id="pomiddlename" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owner Last Name</label></td>
		    <td><input type="text" name="tolastname" id="tolastname" size="5" onchange="getheader('polastname',this.value)">&nbsp;<input type="text" name="polastname" id="polastname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owner DOB</label></td>
		    <td><input type="text" name="todob" id="todob" size="5" onchange="getheader('podob',this.value)">&nbsp;<input type="text" name="podob" id="podob" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owner Gender</label></td>
		    <td><input type="text" name="togender" id="togender" size="5" onchange="getheader('pogender',this.value)">&nbsp;<input type="text" name="pogender" id="pogender" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owner Address 1</label></td>
		    <td><input type="text" name="toad1" id="toad1" size="5" onchange="getheader('poad1',this.value)">&nbsp;<input type="text" name="poad1" id="poad1" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owner Address 2</label></td>
		    <td><input type="text" name="toad2" id="toad2" size="5" onchange="getheader('poad2',this.value)">&nbsp;<input type="text" name="poad2" id="poad2" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owner Suburb</label></td>
		    <td><input type="text" name="tosuburb" id="tosuburb" size="5" onchange="getheader('posuburb',this.value)">&nbsp;<input type="text" name="posuburb" id="posuburb" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owner Town</label></td>
		    <td><input type="text" name="totown" id="totown" size="5" onchange="getheader('potown',this.value)">&nbsp;<input type="text" name="potown" id="potown" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owner Post Code</label></td>
		    <td><input type="text" name="topostcode" id="topostcode" size="5" onchange="getheader('popostcode',this.value)">&nbsp;<input type="text" name="popostcode" id="popostcode" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owner Country</label></td>
		    <td><input type="text" name="tocountry" id="tocountry" size="5" onchange="getheader('pocountry',this.value)">&nbsp;<input type="text" name="pocountry" id="pocountry" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owner Phone Number</label></td>
		    <td><input type="text" name="tophone" id="tophone" size="5" onchange="getheader('pophone',this.value)">&nbsp;<input type="text" name="pophone" id="pophone" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Owner Email</label></td>
		    <td><input type="text" name="toemail" id="toemail" size="5" onchange="getheader('poemail',this.value)">&nbsp;<input type="text" name="poemail" id="poemail" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured Title</label></td>
		    <td><input type="text" name="tititle" id="tititle" size="5" onchange="getheader('pititle',this.value)">&nbsp;<input type="text" name="pititle" id="pititle" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured First Name</label></td>
		    <td><input type="text" name="tifirstname" id="tifirstname" size="5" onchange="getheader('pifirstname',this.value)">&nbsp;<input type="text" name="pifirstname" id="pifirstname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured Middle Name</label></td>
		    <td><input type="text" name="timiddlename" id="timiddlename" size="5" onchange="getheader('pimiddlename',this.value)">&nbsp;<input type="text" name="pimiddlename" id="pimiddlename" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured Last Name</label></td>
		    <td><input type="text" name="tilastname" id="tilastname" size="5" onchange="getheader('pilastname',this.value)">&nbsp;<input type="text" name="pilastname" id="pilastname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured Gender</label></td>
		    <td><input type="text" name="tigender" id="tigender" size="5" onchange="getheader('pigender',this.value)">&nbsp;<input type="text" name="pigender" id="pigender" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured DOB</label></td>
		    <td><input type="text" name="tidob" id="tidob" size="5" onchange="getheader('pidob',this.value)">&nbsp;<input type="text" name="pidob" id="pidob" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured Smoker</label></td>
		    <td><input type="text" name="tismoker" id="tismoker" size="5" onchange="getheader('pismoker',this.value)">&nbsp;<input type="text" name="pismoker" id="pismoker" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured Address 1</label></td>
		    <td><input type="text" name="tiad1" id="tiad1" size="5" onchange="getheader('piad1',this.value)">&nbsp;<input type="text" name="piad1" id="piad1" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured Address 2</label></td>
		    <td><input type="text" name="tiad2" id="tiad2" size="5" onchange="getheader('piad2',this.value)">&nbsp;<input type="text" name="piad2" id="piad2" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured Suburb</label></td>
		    <td><input type="text" name="tisuburb" id="tisuburb" size="5" onchange="getheader('pisuburb',this.value)">&nbsp;<input type="text" name="pisuburb" id="pisuburb" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured Town</label></td>
		    <td><input type="text" name="titown" id="titown" size="5" onchange="getheader('pitown',this.value)">&nbsp;<input type="text" name="pitown" id="pitown" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured Post Code</label></td>
		    <td><input type="text" name="tipostcode" id="tipostcode" size="5" onchange="getheader('pipostcode',this.value)">&nbsp;<input type="text" name="pipostcode" id="pipostcode" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured Country</label></td>
		    <td><input type="text" name="ticountry" id="ticountry" size="5" onchange="getheader('picountry',this.value)">&nbsp;<input type="text" name="picountry" id="picountry" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured Phone Number</label></td>
		    <td><input type="text" name="tiphone" id="tiphone" size="5" onchange="getheader('piphone',this.value)">&nbsp;<input type="text" name="piphone" id="piphone" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Insured Email</label></td>
		    <td><input type="text" name="tiemail" id="tiemail" size="5" onchange="getheader('piemail',this.value)">&nbsp;<input type="text" name="piemail" id="piemail" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel">&nbsp;</td>
		    <td><label>
		      <input type="button" name="btnprocess" id="btnprocess" value="Process" onclick="process()">
	        </label></td>
	      </tr>
	    </table>
  </div>
  		<div id="available" style="position:absolute;visibility:visible;top:51px;left:451px;height:670px;width:440px;background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid; overflow:scroll">
        <table width="450" border="0">
		  <tr>
		    <td colspan="2"align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u>Data Available from <?php echo $provider; ?> spreadsheet</u></label></td>
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

		  include_once("../includes/polimport.php");

		  $oImp = new polimport;
		  
		  
		  $oImp->xpolicynumber = strtoupper($_REQUEST['tpolicynumber']);
		  $oImp->xadvisor = strtoupper($_REQUEST['tadvisor']);
		  $oImp->xcommencedate = strtoupper($_REQUEST['tcommencedate']);
		  $oImp->xceases = strtoupper($_REQUEST['tceases']);
		  $oImp->xrenewdate = strtoupper($_REQUEST['trenewdate']);
		  $oImp->xreviewmonth = strtoupper($_REQUEST['treviewemonth']);
		  $oImp->xstatus = strtoupper($_REQUEST['tstatus']);
		  $oImp->xpayperiod = strtoupper($_REQUEST['tpayperiod']);
		  $oImp->xcovertype = strtoupper($_REQUEST['tcovertype']);
		  $oImp->xbenefit = strtoupper($_REQUEST['tbenefit']);
		  $oImp->xfum = strtoupper($_REQUEST['tfum']);
		  $oImp->xpremium = strtoupper($_REQUEST['tpremium']);
		  $oImp->xbenefitperiod = strtoupper($_REQUEST['tbenefitperiod']);
		  $oImp->xstanddown = strtoupper($_REQUEST['tstanddown']);
		  $oImp->xotitle = strtoupper($_REQUEST['totitle']);
		  $oImp->xofirstname = strtoupper($_REQUEST['tofirstname']);
		  $oImp->xomiddlename = strtoupper($_REQUEST['tomiddlename']);
		  $oImp->xolastname = strtoupper($_REQUEST['tolastname']);
		  $oImp->xodob = strtoupper($_REQUEST['todob']);
		  $oImp->xogender = strtoupper($_REQUEST['togender']);
		  $oImp->xoad1 = strtoupper($_REQUEST['toad1']);
		  $oImp->xoad2 = strtoupper($_REQUEST['toad2']);
		  $oImp->xosuburb = strtoupper($_REQUEST['tosuburb']);
		  $oImp->xotown = strtoupper($_REQUEST['totown']);
		  $oImp->xopostcode = strtoupper($_REQUEST['topostcode']);
		  $oImp->xocountry = strtoupper($_REQUEST['tocountry']);
		  $oImp->xophone = strtoupper($_REQUEST['tophone']);
		  $oImp->xititle = strtoupper($_REQUEST['tititle']);
		  $oImp->xifirstname = strtoupper($_REQUEST['tifirstname']);
		  $oImp->ximiddlename = strtoupper($_REQUEST['timiddlename']);
		  $oImp->xilastname = strtoupper($_REQUEST['tilastname']);
		  $oImp->xidob = strtoupper($_REQUEST['tidob']);
		  $oImp->xigender = strtoupper($_REQUEST['tigender']);
		  $oImp->xismoker = strtoupper($_REQUEST['tismoker']);
		  $oImp->xiad1 = strtoupper($_REQUEST['tiad1']);
		  $oImp->xiad2 = strtoupper($_REQUEST['tiad2']);
		  $oImp->xisuburb = strtoupper($_REQUEST['tisuburb']);
		  $oImp->xitown = strtoupper($_REQUEST['titown']);
		  $oImp->xipostcode = strtoupper($_REQUEST['tipostcode']);
		  $oImp->xicountry = strtoupper($_REQUEST['ticountry']);
		  $oImp->xiphone = strtoupper($_REQUEST['tiphone']);
		  $oImp->xiemail = strtoupper($_REQUEST['tiemail']);
		  $oImp->xoemail = strtoupper($_REQUEST['toemail']);
		  $oImp->xsid = $subid;
		  $oImp->xprovider = $provider;
		  $oImp->xfile = $uploadfile;
		  $oImp->xuserid = $user_id;

		  $x = $oImp->updatePolicy();
		  
		  
		  echo 'x is '.$x;
		  
		  echo '<script>';
		  echo 'this.close();';
		  echo '</script>';

	}
?>


</body>
</html>

