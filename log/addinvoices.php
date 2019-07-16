<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;


date_default_timezone_set($_SESSION['s_timezone']);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

$table = 'ztmp'.$user_id.'_trans';

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());
$q = "select truckportion,trailerportion from params";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$kpcent = $truckportion;
$lpcent = $trailerportion;

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$table;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$table." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, acc2dr int(11) default 0,subdr int(11) default 0,brdr char(4) default '',drindex int(10) default 0,acc2cr int(11) default 0,subcr int(11) default 0,brcr char(4)default '',crindex int(10) default 0,ddate date default '0000-00-00',descript1 varchar(60),reference char(9) default '',refindex int(10) default 0,amount double(16,2) default 0,depdr int(11),depbrdr char(4),depcr int(11),depbrcr char(4),nallocate int(11),tax double(16,2),taxtype char(3),taxpcent double(5,2),applytax char(1),total double(16,2) default 0, done int(11) default 0,type char(1),grn char(10),inv char(10),currency char(3), rate double(7,3),a2d varchar(45),a2c varchar(45),taxindex int(10),drgst char(1) default 'N', crgst char(1) default 'N',debtor varchar(80) default '')  engine myisam";

$calc = mysql_query($query) or die(mysql_error());

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Add Invoices</title>

<script>
function post() {
	var ok = 'Y';
	
	var truckportion = document.getElementById('truckpcent').value;
	var trailerportion = document.getElementById('trailerpcent').value;
	
	if (parseFloat(truckportion) + parseFloat(trailerportion) != 100) {
		alert('Truck plus trailer percentages must total 100');
		ok = 'N';
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
	
	
}


</script>


</head>

<body>

<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
 <table width="700" border="0" align="center" bgcolor="<?php echo $bgcolor; ?>">
	<tr><td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>"><strong>Create Invoices from Dockets</strong></label></td></tr>
    <tr><td colspan="2">This routine will create invoices from all dockets that have an associated amount and have not yet been invoiced.</td></tr>
    <tr>
      <td colspan="2">Where a single docket is for a truck plus trailer, the value will be split between truck and trailer as follows:-</td>
    </tr>
    <tr>
      <td>Truck
      <input type="text" name="truckpcent" id="truckpcent" value="<?php echo $kpcent; ?>"/> 
      %</td>
      <td>Trailer
      <input type="text" name="trailerpcent" id="trailerpcent"  value="<?php echo $lpcent; ?>"/>  
      %</td>
    </tr>
    <tr>
      <td>All GST to go against Admin Branch<input type="radio" name="radio" id="bgst" value="admin" checked/></td>
      <td>GST to go against Branch associated with Docket Truck<input type="radio" name="radio" id="bgst" value="brch" /></td>
    </tr>
      <tr>
        <td colspan="2" align="right">
        <input type="button" value="Create Invoices" name="save" onClick="post()"  >
      </td>
      </tr>


 </table>
</form>

<?php

if($_REQUEST['savebutton'] == "Y") {
	$moduledb = $_SESSION['s_logdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$kpcent = $_REQUEST['truckpcent'];
	$lpcent = $_REQUEST['trailerpcent'];
	$postgst = $_REQUEST['radio'];
	
	// get next invoice number but don't save (in case postgst = admin)
	$moduledb = $_SESSION['s_findb'];
	mysql_select_db($moduledb) or die(mysql_error());
	$query = "lock tables numbers write";
	$result = mysql_query($query) or die($query);
	$query = "select inv from numbers";
	$result = mysql_query($query) or die($query);
	$row = mysql_fetch_array($result);
	extract($row);
	$postrefno = $inv + 1;
	$postref = 'INV'.$postrefno;
	$query = "unlock tables";
	$result = mysql_query($query) or die($query);

	$moduledb = $_SESSION['s_logdb'];
	mysql_select_db($moduledb) or die(mysql_error());
	
	$q = "update params set truckportion = ".$kpcent.", trailerportion = ".$lpcent;
	$r = mysql_query($q) or die(mysql_error());

	$q = "select docket_no,ddate,debtor,truckbranch,trailerbranch,truck,trailer,amount,customer from dockets where invoice = 'N' and amount > 0";
	$r = mysql_query($q) or die(mysql_error().' '.$q);
	if (mysql_num_rows($r) > 0) {
		while ($row = mysql_fetch_array($r)) {
			extract($row);
			$idocket_no = $docket_no;
			$idr = split('~',$debtor);
			$idrno = $idr[0];
			$idrsub = $idr[1];
			$itruckbr = $truckbranch;
			$itrailerbr = $trailerbranch;
			$iamt = $amount;
			$idate = $ddate;
			$trk = $truck;
			$trl = $trailer;
			$client = $customer;
			
			$moduledb = $_SESSION['s_findb'];
			mysql_select_db($moduledb) or die(mysql_error());
			
			$ok = 'Y';
			// check that relevant income accounts exist
			if ($itruckbr != '') {
				$qb = "select accountno from glmast where accountno < 40 and branch = '".$itruckbr."'";
				$rb = mysql_query($qb) or die(mysql_error().' - '.$qb);
				$numrows = mysql_num_rows($rb);
				if ($numrows == 0) {
					$ok = 'N';
				} else {
					$ok = 'Y';
				}
			}
			if ($itrailerbr != '') {
				$qb = "select accountno from glmast where accountno < 40 and branch = '".$itrailerbr."'";
				$rb = mysql_query($qb) or die(mysql_error().' - '.$qb);
				$numrows = mysql_num_rows($rb);
				if ($numrows == 0) {
					$ok = 'N';
				} else {
					$ok = 'Y';
				}
			}
			
			if ($ok == 'Y') {
				$query = "select tax,taxpcent from taxtypes where tax = 'GST'";
				$result = mysql_query($query) or die($query);
				$row = mysql_fetch_array($result);
				extract($row);
				$taxtype = $tax;
				
				// if both truck and trailer involved split amounts 60% to trailer and 40% to truck.
				if (trim($itruckbr) != '' && trim($itrailerbr) != '') {
					$itruckamt	= round($iamt*$kpcent/100,2);
					$itraileramt = $iamt - $itruckamt;
					$itrucktax = round($itruckamt * $taxpcent / 100,2);
					$itrucktotal = $itruckamt + $itrucktax;
					$itrailertax = round($itraileramt * $taxpcent / 100,2);
					$itrailertotal = $itraileramt + $itrailertax;
					
					$moduledb = $_SESSION['s_findb'];
					mysql_select_db($moduledb) or die(mysql_error());
					$query = "lock tables numbers write";
					$result = mysql_query($query) or die($query);
					$query = "select inv from numbers";
					$result = mysql_query($query) or die($query);
					$row = mysql_fetch_array($result);
					extract($row);
					$refno = $inv + 1;
					$query = "update numbers set inv = ".$refno;
					$result = mysql_query($query) or die($query);
					$query = "unlock tables";
					$result = mysql_query($query) or die($query);
					
					$reference = 'INV'.$refno;
					
				
					// entry for truck
					
					// get relevant income account
					$qb = "select accountno from glmast where accountno < 40 and branch = '".$itruckbr."'";
					$rb = mysql_query($qb) or die(mysql_error().' - '.$qb);
					$row = mysql_fetch_array($rb);
					extract($row);
					$icrno = $accountno;
	
	
					$sql = "insert into ".$table." (acc2dr,subdr,brdr,acc2cr,subcr,brcr,ddate,descript1,reference,amount,tax,taxtype,taxpcent,applytax,total,debtor) values (";
					$sql .= $idrno.",";
					$sql .= $idrsub.",";
					$sql .= "'',";
					$sql .= $icrno.",";
					$sql .= "0,";
					$sql .= "'".$itruckbr."',";
					$sql .= "'".$idate."',";
					$sql .= "'Docket ".$idocket_no." - ".$trk."',";
					$sql .= "'".$reference."',";
					$sql .= $itruckamt.",";
					$sql .= $itrucktax.",";
					$sql .= "'".$taxtype."',";
					$sql .= $taxpcent.",";
					$sql .= "'Y',";
					$sql .= $itrucktotal.",";
					$sql .= "'".$client."')";
					
					$result = mysql_query($sql) or die(mysql_error().' - '.$sql);
		
					// entry for trailer
					
					$query = "lock tables numbers write";
					$result = mysql_query($query) or die($query);
					$query = "select inv from numbers";
					$result = mysql_query($query) or die($query);
					$row = mysql_fetch_array($result);
					extract($row);
					$refno = $inv + 1;
					$query = "update numbers set inv = ".$refno;
					$result = mysql_query($query) or die($query);
					$query = "unlock tables";
					$result = mysql_query($query) or die($query);
					
					$reference = 'INV'.$refno;
					
					
					// get relevant income account
					$qb = "select accountno from glmast where accountno < 40 and branch = '".$itrailerbr."'";
					$rb = mysql_query($qb) or die(mysql_error().' - '.$qb);
					$row = mysql_fetch_array($rb);
					extract($row);
					$icrno = $accountno;
					
					$sql = "insert into ".$table." (acc2dr,subdr,brdr,acc2cr,subcr,brcr,ddate,descript1,reference,amount,tax,taxtype,taxpcent,applytax,total,debtor) values (";
					$sql .= $idrno.",";
					$sql .= $idrsub.",";
					$sql .= "'',";
					$sql .= $icrno.",";
					$sql .= "0,";
					$sql .= "'".$itrailerbr."',";
					$sql .= "'".$idate."',";
					$sql .= "'Docket ".$idocket_no." - ".$trl."',";
					$sql .= "'".$reference."',";
					$sql .= $itraileramt.",";
					$sql .= $itrailertax.",";
					$sql .= "'".$taxtype."',";
					$sql .= $taxpcent.",";
					$sql .= "'Y',";
					$sql .= $itrailertotal.",";
					$sql .= "'".$client."')";
					
					$result = mysql_query($sql) or die(mysql_error().' - '.$sql);
					
					$moduledb = $_SESSION['s_logdb'];
					mysql_select_db($moduledb) or die(mysql_error());
					
					$d = "update dockets set invoice = '".$reference."' where docket_no = ".$idocket_no;
					$rd = mysql_query($d) or die(mysql_error().' - '.$d);
	
		
				}
		
				// if only truck .
				
				
				if (trim($itruckbr) != '' && trim($itrailerbr) == '') {
					$itruckamt	= $iamt;
					$itrucktax = round($itruckamt * $taxpcent / 100,2);
					$itrucktotal = $itruckamt + $itrucktax;
					
					$moduledb = $_SESSION['s_findb'];
					mysql_select_db($moduledb) or die(mysql_error());
					$query = "lock tables numbers write";
					$result = mysql_query($query) or die($query);
					$query = "select inv from numbers";
					$result = mysql_query($query) or die($query);
					$row = mysql_fetch_array($result);
					extract($row);
					$refno = $inv + 1;
					$query = "update numbers set inv = ".$refno;
					$result = mysql_query($query) or die($query);
					$query = "unlock tables";
					$result = mysql_query($query) or die($query);
					
					$reference = 'INV'.$refno;
				
					// entry for truck
					
					// get relevant income account
					$qb = "select accountno from glmast where accountno < 40 and branch = '".$itruckbr."'";
					$rb = mysql_query($qb) or die(mysql_error().' - '.$qb);
					$row = mysql_fetch_array($rb);
					extract($row);
					$icrno = $accountno;
					
					$sql = "insert into ".$table." (acc2dr,subdr,brdr,acc2cr,subcr,brcr,ddate,descript1,reference,amount,tax,taxtype,taxpcent,applytax,total,debtor) values (";
					$sql .= $idrno.",";
					$sql .= $idrsub.",";
					$sql .= "'',";
					$sql .= $icrno.",";
					$sql .= "0,";
					$sql .= "'".$itruckbr."',";
					$sql .= "'".$idate."',";
					$sql .= "'Docket ".$idocket_no." - ".$trk."',";
					$sql .= "'".$reference."',";
					$sql .= $itruckamt.",";
					$sql .= $itrucktax.",";
					$sql .= "'".$taxtype."',";
					$sql .= $taxpcent.",";
					$sql .= "'Y',";
					$sql .= $itrucktotal.",";
					$sql .= "'".$client."')";
					
					$result = mysql_query($sql) or die(mysql_error().' - '.$sql);
					
					$moduledb = $_SESSION['s_logdb'];
					mysql_select_db($moduledb) or die(mysql_error());
					
					$d = "update dockets set invoice = '".$reference."' where docket_no = ".$idocket_no;
					$rd = mysql_query($d) or die(mysql_error().' - '.$d);
					
		
				}
				
				// if only trailer involved .
				if (trim($itruckbr) == '' && trim($itrailerbr) != '') {
					$itraileramt = $iamt;
					$itrailertax = round($itraileramt * $taxpcent / 100,2);
					$itrailertotal = $itraileramt + $itrailertax;
					
					$moduledb = $_SESSION['s_findb'];
					mysql_select_db($moduledb) or die(mysql_error());
					$query = "lock tables numbers write";
					$result = mysql_query($query) or die($query);
					$query = "select inv from numbers";
					$result = mysql_query($query) or die($query);
					$row = mysql_fetch_array($result);
					extract($row);
					$refno = $inv + 1;
					$query = "update numbers set inv = ".$refno;
					$result = mysql_query($query) or die($query);
					$query = "unlock tables";
					$result = mysql_query($query) or die($query);
					
					$reference = 'INV'.$refno;
				
					// entry for trailer
					
					// get relevant income account
					$qb = "select accountno from glmast where accountno < 40 and branch = '".$itruckbr."'";
					$rb = mysql_query($qb) or die(mysql_error().' - '.$qb);
					$row = mysql_fetch_array($rb);
					extract($row);
					$icrno = $accountno;
					
					$sql = "insert into ".$table." (acc2dr,subdr,brdr,acc2cr,subcr,brcr,ddate,descript1,reference,amount,tax,taxtype,taxpcent,applytax,total,debtor) values (";
					$sql .= $idrno.",";
					$sql .= $idrsub.",";
					$sql .= "'',";
					$sql .= $icrno.",";
					$sql .= "0,";
					$sql .= "'".$itrailerbr."',";
					$sql .= "'".$idate."',";
					$sql .= "'Docket ".$idocket_no." - ".$trl."',";
					$sql .= "'".$reference."',";
					$sql .= $itraileramt.",";
					$sql .= $itrailertax.",";
					$sql .= "'".$taxtype."',";
					$sql .= $taxpcent.",";
					$sql .= "'Y',";
					$sql .= $itrailertotal.",";
					$sql .= "'".$client."')";
					
					$result = mysql_query($sql) or die(mysql_error().' - '.$sql);
					
					$moduledb = $_SESSION['s_logdb'];
					mysql_select_db($moduledb) or die(mysql_error());
					
					$d = "update dockets set invoice = '".$reference."' where docket_no = ".$idocket_no;
					$rd = mysql_query($d) or die(mysql_error().' - '.$d);
		
				}
			}
		}
		
		$_SESSION['s_fromlogging'] = 'Y';
		include("../fin/includes/ajaxPostTrans.php");
		
		// if gst to all go to admin branch
		if ($postgst == 'admin') {
			$postreference = $reference;
			$moduledb = $_SESSION['s_findb'];
			mysql_select_db($moduledb) or die(mysql_error());
			
			$q = "select ddate as dt,debit,credit,branch,reference as ref,descript1,taxpcent,gsttype from trmain where accountno = 870 and (reference >= '".$postref."' and reference <= '".$postreference."')";
			$r = mysql_query($q) or die(mysql_error().' '.$q);
			while ($row = mysql_fetch_array($r)) {
				extract($row);
				
				if ($debit > 0) {
					$amt = $debit;
				} else {
					$amt = $credit;
				}
			
				$sql = "insert into ".$table." (acc2dr,subdr,brdr,acc2cr,subcr,brcr,ddate,descript1,reference,amount,tax,taxtype,taxpcent,applytax,total,debtor) values (";
				$sql .= "870,";
				$sql .= "0,";
				$sql .= "'".$branch."',";
				$sql .= "870,";
				$sql .= "0,";
				$sql .= "'0001',";
				$sql .= "'".$dt."',";
				$sql .= "'".$descript1."',";
				$sql .= "'".$ref."',";
				$sql .= $amt.",";
				$sql .= "0,";
				$sql .= "'".$gsttype."',";
				$sql .= $taxpcent.",";
				$sql .= "'N',";
				$sql .= $amt.",";
				$sql .= "'')";
						
				$result = mysql_query($sql) or die(mysql_error().' - '.$sql);
			
			}
			
			include("../fin/includes/ajaxPostTrans.php");
			
		}

		echo "<script>";
	  	echo 'window.open("","dockets").jQuery("#docketlist").trigger("reloadGrid");';
		echo "this.close();";
		echo "</script>";
		
		$_SESSION['s_fromlogging'] = 'N';
		

	}

}


?>


</body>
</html>