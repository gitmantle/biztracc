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


$fl = "documents/".$coyid."__st.xlsx";

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


$uploadfile = "ztmp".$user_id."_uploadst";

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
<title>Process Stock Spreadsheet</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

<script>
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function process() {
	var acode = document.getElementById('tcode').value;
	var item = document.getElementById('titem').value;
	
	var ok = "Y";
	
	if (acode == "") {
		alert('Please enter column that contains the stock code');
		ok = 'N';
		return false;
	}
	
	if (item == "") {
		alert('Please enter column that contains stock item name');
		ok = 'N';
		return false;
	}
	
	if (ok == 'Y') {
	  document.getElementById('savebutton').value = "Y";
	  document.getElementById('formst').submit();
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

<form name="formst" id="formst" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">

  <div id="heading" style="position:absolute;visibility:visible;top:200px;left:1px;height:49px;width:890px;background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
  <table width="900" border="0" align="center">
    <tr>
      <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u>Process Stock Item Upload</u></label></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;">Type relevant column letter from Data Available against Data Accepted</label></td>
    </tr>
  </table>
  </div>
  <div id="accepted" style="position:absolute; visibility:visible; top:251px; left:3px; height:670px; width:440px; background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid; overflow:scroll">
		<table width="450" border="0">
		  <tr>
		    <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u>Data accepted by system</u></label></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Stock Group</label></td>
		    <td><input type="text" name="tgroup" id="tgroup" size="5" onchange="getheader('pgroup',this.value)">&nbsp;<input type="text" name="pgroup" id="pgroup" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Stock Category within Group</label></td>
		    <td><input type="text" name="tcategory" id="tcategory" size="5" onchange="getheader('pcategory',this.value)">&nbsp;<input type="text" name="pcategory" id="pcategory" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Item Code</label></td>
		    <td><input type="text" name="tcode" id="tcode" size="5" onchange="getheader('pcode',this.value)">&nbsp;<input type="text" name="pcode" id="pcode" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Item Name</label></td>
		    <td><input type="text" name="titem" id="titem" size="5" onchange="getheader('pitem',this.value)">&nbsp;<input type="text" name="pitem" id="pitem" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Average Cost</label></td>
		    <td><input type="text" name="tavgcost" id="tavgcost" size="5" onchange="getheader('pavgcost',this.value)">&nbsp;<input type="text" name="pavgcost" id="pavgcost" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Unit</label></td>
		    <td><input type="text" name="tunit" id="tunit" size="5" onchange="getheader('punit',this.value)">&nbsp;<input type="text" name="punit" id="punit" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Sales Account</label></td>
		    <td><input type="text" name="tsellacc" id="tsellacc" size="5" onchange="getheader('psellacc',this.value)">&nbsp;<input type="text" name="psellacc" id="psellacc" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Sales Sub Account</label></td>
		    <td><input type="text" name="tsellsub" id="tsellsub" size="5" onchange="getheader('psellsub',this.value)">&nbsp;<input type="text" name="psellsub" id="psellsub" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Purchase Account</label></td>
		    <td><input type="text" name="tpurchacc" id="tpurchacc" size="5" onchange="getheader('ppurchacc',this.value)">&nbsp;<input type="text" name="ppurchacc" id="ppurchacc" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Purchase Sub Account</label></td>
		    <td><input type="text" name="tpurchsub" id="tpurchsub" size="5" onchange="getheader('ppurchsub',this.value)">&nbsp;<input type="text" name="ppurchsub" id="ppurchsub" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Default Trading Tax</label></td>
		    <td><input type="text" name="tdeftax" id="tdeftax" size="5" onchange="getheader('pdeftax',this.value)">&nbsp;<input type="text" name="pdeftax" id="pdeftax" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Active</label></td>
		    <td><input type="text" name="tactive" id="tactive" size="5" onchange="getheader('pactive',this.value)">&nbsp;<input type="text" name="pactive" id="pactive" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Include in Stock Take</label></td>
		    <td><input type="text" name="tstock" id="tstock" size="5" onchange="getheader('pstock',this.value)">&nbsp;<input type="text" name="pstock" id="pstock" readonly></td>
	      </tr>
		  <tr>
		    <td class="boxlabel">&nbsp;</td>
		    <td><label>
		      <input type="button" name="btnprocess" id="btnprocess" value="Process" onclick="process()">
	        </label></td>
	      </tr>
	    </table>
  </div>
  		<div id="available" style="position:absolute;visibility:visible;top:251px;left:451px;height:670px;width:440px;background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid; overflow:scroll">
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
		
		  include_once("stkimport.php");

		  $oImp = new stkimport;
		  
		  $oImp->xgroup = strtoupper($_REQUEST['tgroup']);
		  $oImp->xcategory = strtoupper($_REQUEST['tcategory']);
		  $oImp->xcode = strtoupper($_REQUEST['tcode']);
		  $oImp->xitem = strtoupper($_REQUEST['titem']);
		  $oImp->xavgcost = strtoupper($_REQUEST['tavgcost']);
		  $oImp->xunit = strtoupper($_REQUEST['tunit']);
		  $oImp->xsellacc = strtoupper($_REQUEST['tsellacc']);
		  $oImp->xsellsub = strtoupper($_REQUEST['tsellsub']);
		  $oImp->xpurchacc = strtoupper($_REQUEST['tpurchacc']);
		  $oImp->xpurchsub = strtoupper($_REQUEST['tpurchsub']);
		  $oImp->xdeftax = strtoupper($_REQUEST['tdeftax']);
		  $oImp->xactive = strtoupper($_REQUEST['tactive']);
		  $oImp->xstock = strtoupper($_REQUEST['tstock']);

		  $oImp->xfile = $uploadfile;

		  $oImp->updateStock();
		  
		  echo '<script>';
		  echo 'this.close();';
		  echo '</script>';

	}
?>


</body>
</html>

