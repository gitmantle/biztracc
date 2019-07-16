<?php
	session_start();
	
	$coyidno = $_SESSION['s_coyid'];
	$dc = $_REQUEST['drcr'];
	$reftype = $_REQUEST['reftype'];
	$crdr = $_REQUEST['crdr'];
	$cl = explode('-',$_REQUEST['client']);
	$clientno = $cl[0];
	$clsub = $cl[2];

	require_once("../../db.php");

	$moduledb = $_SESSION['s_findb'];
	mysql_select_db($moduledb) or die(mysql_error());
	

	
	if ($clientno >= 30000000 and $crdr == 'cr') {
		$xreftype = 'inv';
	} 
	if ($clientno >= 20000000 and $clientno <30000000 and $crdr == 'dr') {
		$xreftype = 'grn';
	}
	
	switch ($xreftype) {
	case 'inv':
		$query = "select uid,reference, (debit-paid) as outstanding from trmain where accountno = ".$clientno." and sub = ".$clsub." and substr(reference,1,3) = 'INV'";
		$result = mysql_query($query) or die($query);
		$newxref = "<option value=\"0#N/A\">N/A</option>";
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			if ($outstanding > 0) {
				$newxref .= "<option value=\"".$uid."#".$reference."\">".$reference."&nbsp;:&nbsp;".$outstanding."</option>";
			}
		}
		break;
	case 'grn':
		$query = "select uid,reference, (credit-paid) as outstanding from trmain where accountno = ".$clientno." and sub = ".$clsub." and substr(reference,1,3) = 'GRN'";
		$result = mysql_query($query) or die($query);
		$newxref = "<option value=\"0#N/A\">N/A</option>";
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			if ($outstanding > 0) {
				$newxref .= "<option value=\"".$uid."#".$reference."\">".$reference."-".$outstanding."</option>";
			}
		}
	 	break;
	default:
		$newxref = "<option value=\"0#N/A\">N/A</option>";
	}
	
	echo $newxref;

?>