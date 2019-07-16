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

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());


$fl = "documents/".$coyid."__gl.xlsx";

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


$uploadfile = "ztmp".$user_id."_uploadgl";

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
<title>Process General Ledger Spreadsheet</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

<script>
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function process() {
	var acname = document.getElementById('taccountname').value;
	var dr = document.getElementById('tdrbal').value;
	var cr = document.getElementById('tcrbal').value;
	
	var ok = "Y";
	
	if (acname == "") {
		alert('Please enter column that contains the account name');
		ok = 'N';
		return false;
	}
	
	if (dr == "") {
		alert('Please enter column that contains debit balances');
		ok = 'N';
		return false;
	}
	
	if (cr == "") {
		alert('Please enter column that contains credit balances');
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
      <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u>Process General Ledger Upload</u></label></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;">Type relevant column letter from Data Available against Data Accepted</label></td>
    </tr>
  </table>
  </div>
  <div id="accepted" style="position:absolute; visibility:visible; top:51px; left:3px; height:670px; width:440px; background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid; overflow:scroll">
		<table width="450" border="0">
		  <tr>
		    <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u>Data accepted by system</u></label></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Account Name</label></td>
		    <td><input type="text" name="taccountname" id="taccountname" size="5" onchange="getheader('paccountname',this.value)">&nbsp;<input type="text" name="paccountname" id="paccountname" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Debit Balance</label></td>
		    <td><input type="text" name="tdrbal" id="tdrbal" size="5" onchange="getheader('pdrbal',this.value)">&nbsp;<input type="text" name="pdrbal" id="pdrbal" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Credit Balance</label></td>
		    <td><input type="text" name="tcrbal" id="tcrbal" size="5" onchange="getheader('pcrbal',this.value)">&nbsp;<input type="text" name="pcrbal" id="pcrbal" readonly></td>
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
		    <td colspan="2"align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u>Data Available from General Ledger  spreadsheet</u></label></td>
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
		
		// TODO
		// code to allocate account type and account numbers to accounts
		
		

		  include_once("accimport.php");

		  $oImp = new accimport;
		  
		  
		  $oImp->xaccountname = strtoupper($_REQUEST['taccountname']);
		  $oImp->xdrbal = strtoupper($_REQUEST['tdrbal']);
		  $oImp->xcrbal = strtoupper($_REQUEST['tcrbal']);
		  $oImp->xfile = $uploadfile;

		  $x = $oImp->updateGL();
		  
		  
		  echo 'x is '.$x;
		  
		  echo '<script>';
		  echo 'this.close();';
		  echo '</script>';

	}
?>


</body>
</html>

