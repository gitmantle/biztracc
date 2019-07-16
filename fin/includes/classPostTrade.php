<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>



<?php
class classPostTrade
{

	// general properties
	public $newid;
	public $transtype;
	public $ddate;
	public $descript1 = '';
	public $reference;
	public $acc;
	public $>asb;
	public $loc = 1;
	public $xref = '';
	public $postaladdress = '';
	public $deliveryaddress = '';
	public $client = '';
	public $paymentmethod = '';
	public $signature = '';
	
	
	

//*************************************************************
	function posttrade()
//*************************************************************
	{
		
		$this->transtype = strtoupper($this->transtype);
		$this->reference = strtoupper($this->reference);
		
		
		echo 'got here';

		session_start();
		//ini_set('display_errors', true);
		if ($_SESSION['s_server'] == 'localhost') {
			$pathdb = '../../db.php';
			require($pathdb);
		} else {
			$root = $_SERVER['DOCUMENT_ROOT'];
			$pathdb = $root.'/db.php';
			require($pathdb);
		}
		
		//$defbranch = $_SESSION['s_ubranch'];
		$coyno = $_SESSION['s_coyid'];
		
		$usersession = $_SESSION['usersession'];
		$admindb = $_SESSION['s_admindb'];
		date_default_timezone_set($_SESSION['s_timezone']);
		
		mysql_select_db($admindb) or die(mysql_error());
		$query = "select * from sessions where session = '".$usersession."'";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		extract($row);
		$uip = $userip;
		$unm = $uname;
		
		$table = 'ztmp'.$user_id.'_trading';
		date_default_timezone_set($_SESSION['s_timezone']);
		$serialtable = 'ztmp'.$user_id.'_serialnos';
		
		
		// work out current,d30,d60,d90,d120
		$today = date('Y-m-d');
		$date1 = new DateTime($this->ddate);
		$date2 = new DateTime($today);
		$interval = $date1->diff($date2);
		$days = $interval->days;
			
		if ($days < 31) {
			$aged = 'Current';
		}
		if ($days > 30 && $days < 61) {
			$aged = 'D30';
		}
		if ($days > 60 && $days < 91) {
			$aged = 'D60';
		}
		if ($days > 90) {
			$aged = 'D120';
		}
		
		switch ($this->transtype) {
			case 'C_S':
			$a2dr = $this->acc;
			$b2dr = '';
			$s2dr = $this->asb;
			$a2cr = 0;
			$b2cr = '';
			$s2cr = 0;
			break;
			case 'C_P':
			$a2cr = $this->acc;
			$b2cr = '';
			$s2cr = $this->asb;
			$a2dr = 0;
			$b2dr = '';
			$s2dr = 0;
			break;
			case 'INV':
			$a2dr = $this->acc;
			$b2dr = '';
			$s2dr = $this->asb;
			$a2cr = 0;
			$b2cr = '';
			$s2cr = 0;
			break;
			case 'CRN':
			$a2cr = $this->acc;
			$b2cr = '';
			$s2cr = $this->asb;
			$a2dr = 0;
			$b2dr = '';
			$s2dr = 0;
			break;
			case 'GRN':
			$a2cr = $this->acc;
			$b2cr = '';
			$s2cr = $this->asb;
			$a2dr = 0;
			$b2dr = '';
			$s2dr = 0;
			break;
			case 'RET':
			$a2dr = $this->acc;
			$b2dr = '';
			$s2dr = $this->asb;
			$a2cr = 0;
			$b2cr = '';
			$s2cr = 0;
			break;
			case 'REQ':
			$a2dr = $this->acc;
			$b2dr = '';
			$s2dr = $this->asb;
			$a2cr = 0;
			$b2cr = '';
			$s2cr = 0;
			break;
		
		}
		
		
		$moduledb = $_SESSION['s_findb'];
		mysql_select_db($moduledb) or die(mysql_error());
		
		// get branch from stklocs
		$q = "select branch from stklocs where uid = ".$this->loc;
		$r = mysql_query($q) or die(mysql_error());
		$row = mysql_fetch_array($r);
		extract($row);
		$defbranch = $branch;
		
		
		$q = "select sum(tax) as totaltax, sum(value) as totalamount from ".$table;
		$r = mysql_query($q) or die(mysql_error().' '.$q);
		$row = mysql_fetch_array($r);
		extract($row);
		$totalvalue = $totalamount + $totaltax;
		
			switch ($this->paymentmethod) {
			case 'csh':
				$cash = $totalvalue;
				$cheque = 0;
				$eftpos = 0;
				$ccard = 0;
				break;
			case 'chq':
				$cash = 0;
				$cheque = $totalvalue;
				$eftpos = 0;
				$ccard = 0;
				break;	
			case 'crd':
				$cash = 0;
				$cheque = 0;
				$ccard = $totalvalue;
				$eftpos = 0;
				break;		
			case 'eft':
				$cash = 0;
				$cheque = 0;
				$ccard = 0;
				$eftpos = $totalvalue;
				break;		
			default:
				$cash = 0;
				$cheque = 0;
				$ccard = 0;
				$eftpos = 0;
				break;			
			}
		
			if (empty($b2dr)) {$br2dr = ' ';}
			if (empty($s2dr)) {$sub2dr = 0;}
			if (empty($b2cr)) {$br2cr = ' ';}
			if (empty($s2cr)) {$sub2cr = 0;}	
			
			switch ($this->transtype) {
				case 'C_S':
					$ano = $a2dr;
					$bno = $b2dr;
					$sno = $s2dr;
					$taxdrcr = 'cr';
					break;
				case 'C_P':
					$ano = $a2cr;
					$bno = $b2cr;
					$sno = $s2cr;
					$taxdrcr = 'dr';
					break;
				case 'INV':
					$ano = $a2dr;
					$bno = $b2dr;
					$sno = $s2dr;
					$taxdrcr = 'cr';
					break;
				case 'CRN':
					$ano = $a2cr;
					$bno = $b2cr;
					$sno = $s2cr;
					$taxdrcr = 'dr';
					break;
				case 'GRN':
					$ano = $a2cr;
					$bno = $b2cr;
					$sno = $s2cr;
					$taxdrcr = 'dr';
					break;
				case 'CHQ':
					$ano = $a2cr;
					$bno = $b2cr;
					$sno = $s2cr;
					$taxdrcr = 'dr';
					break;			
				case 'RET':
					$ano = $a2dr;
					$bno = $b2dr;
					$sno = $s2dr;
					$taxdrcr = 'cr';
					break;
				case 'REC':
					$ano = $a2cr;
					$bno = $b2cr;
					$sno = $s2cr;
					$taxdrcr = '';
					break;
				case 'PAY':
					$ano = $a2dr;
					$bno = $b2dr;
					$sno = $s2dr;
					$taxdrcr = '';
					break;
				case 'REQ':
					$ano = $a2dr;
					$bno = $b2dr;
					$sno = $s2dr;
					$taxdrcr = '';
					break;
				case 'ADJ':
					$ano = $a2dr;
					$bno = $b2dr;
					$sno = $s2dr;
					$taxdrcr = '';
					break;
			}
		
		
			// insert invhead record
			$sSQLString = "insert into invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,xref,totvalue,tax,cash,cheque,eftpos,ccard,staff,postaladdress,deliveryaddress,client,signature) values ";
			$sSQLString .= "('".$this->ddate."',";
			$sSQLString .= $ano.",'";
			$sSQLString .= $bno."',";
			$sSQLString .= $sno.",'";
			$sSQLString .= $this->descript1."','";
			$sSQLString .= $this->transtype."','";
			$sSQLString .= $this->reference."','";
			$sSQLString .= $this->xref."',";
			$sSQLString .= $totalamount.",";
			$sSQLString .= $totaltax.",";
			$sSQLString .= $cash.",";
			$sSQLString .= $cheque.",";
			$sSQLString .= $eftpos.",";
			$sSQLString .= $ccard.",'";
			$sSQLString .= $uname."','";
			$sSQLString .= $this->postaladdress."','";
			$sSQLString .= $this->deliveryaddress."','";
			$sSQLString .= $this->signature."','";
			$sSQLString .= $this->client."')";
			
		
			$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				
			$q = "select * from ".$table;
			$result = mysql_query($q) or die(mysql_error());
			while ($row = mysql_fetch_array($result)) {
				extract($row);
				
				$pbr = $purchbr;
				
				// insert records into invtrans
				
					$sSQLString = "insert into invtrans (itemcode,item,price,quantity,unit,taxtype,taxpcent,tax,ref_no,value,discount,disc_type,grnlineno) values ";
					$sSQLString .= "('".$itemcode."','";
					$sSQLString .= $item."',";
					$sSQLString .= $price.",";
					$sSQLString .= $quantity.",'";
					$sSQLString .= $unit."','";
					$sSQLString .= $taxtype."',";
					$sSQLString .= $taxpcent.",";
					$sSQLString .= $tax.",'";
					$sSQLString .= $this->reference."',";
					$sSQLString .= $value.",";
					$sSQLString .= $discount.",";
					$sSQLString .= "'".$disctype."',";
					$sSQLString .= $uid.")";
					
					$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
			}
		
			if ($this->transtype == 'REQ') {
				$qq = "update invhead set branch = '".$pbr."' where ref_no = '".$this->reference."'";
				$rq = mysql_query($qq) or die(mysql_error());
			}
		
			// insert GL records	
			
			// Create entries between closing stock and stock on hand
			if ($this->transtype == 'INV' || $this->transtype == 'C_S' || $this->transtype == 'C_P' || $this->transtype == 'REQ' || $this->transtype == 'RET') {
				
				$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
				$sSQLString .= "('".$this->ddate."',";
				$sSQLString .= "187,";		// debit closing stock
				$sSQLString .= "'".$defbranch."',";			
				$sSQLString .= "0,";
				$sSQLString .= '825'.",";
				$sSQLString .= "'".$defbranch."',";			
				$sSQLString .= '0'.",";			
				$sSQLString .= $totalamount.",";		// with the amount excluding GST
				$sSQLString .= "0,'";
				$sSQLString .= $this->reference."','";					
				$sSQLString .= $taxtype."','";
				$sSQLString .= $this->descript1."',";
				$sSQLString .= $taxpcent.")";			
					
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal + ".$totalamount." where accountno = 187 and branch = '".$defbranch."' and sub = 0";
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
				
				$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
				$sSQLString .= "('".$this->ddate."',";
				$sSQLString .= "825,";		// credit stock on hand
				$sSQLString .= "'".$defbranch."',";			
				$sSQLString .= "0,";
				$sSQLString .= '187'.",";
				$sSQLString .= "'".$defbranch."',";			
				$sSQLString .= '0'.",";			
				$sSQLString .= "0,";		
				$sSQLString .= $totalamount.",'"; // with the amount excluding GST
				$sSQLString .= $this->reference."','";					
				$sSQLString .= $taxtype."','";
				$sSQLString .= $this->descript1."',";
				$sSQLString .= $taxpcent.")";			
					
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal - ".$totalamount." where accountno = 825 and branch = '".$defbranch."' and sub = 0";
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
				
			} else {
				
				$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
				$sSQLString .= "('".$this->ddate."',";
				$sSQLString .= "825,";		// debit stock on hand
				$sSQLString .= "'".$defbranch."',";			
				$sSQLString .= "0,";
				$sSQLString .= '187'.",";
				$sSQLString .= "'".$defbranch."',";			
				$sSQLString .= '0'.",";			
				$sSQLString .= $totalamount.",";		// with the amount excluding GST
				$sSQLString .= "0,'";
				$sSQLString .= $this->reference."','";					
				$sSQLString .= $taxtype."','";
				$sSQLString .= $this->descript1."',";
				$sSQLString .= $taxpcent.")";			
					
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal + ".$totalamount." where accountno = 825 and branch = '".$defbranch."' and sub = 0";
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
				
				$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
				$sSQLString .= "('".$this->ddate."',";
				$sSQLString .= "187,";		// credit closing stock
				$sSQLString .= "'".$defbranch."',";			
				$sSQLString .= "0,";
				$sSQLString .= '825'.",";
				$sSQLString .= "'".$defbranch."',";			
				$sSQLString .= '0'.",";			
				$sSQLString .= "0,";		
				$sSQLString .= $totalamount.",'"; // with the amount excluding GST
				$sSQLString .= $this->reference."','";					
				$sSQLString .= $taxtype."','";
				$sSQLString .= $this->descript1."',";
				$sSQLString .= $taxpcent.")";			
					
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal - ".$totalamount." where accountno = 187 and branch = '".$defbranch."' and sub = 0";
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
			
			}
			
			
			//*******************************************************************************************************************************************
			// Invoice
			//*******************************************************************************************************************************************
			if ($this->transtype == 'INV') {
					// debit debtor account
					$sSQLString = "insert into trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
					$sSQLString .= "('".$this->ddate."',";
					$sSQLString .= $a2dr.",";		// debit the debtor
					$sSQLString .= $s2dr.",";
					$sSQLString .= "0".",'";
					$sSQLString .= " "."',";			
					$sSQLString .= "0".",";			
					$sSQLString .= $totalvalue.",";		// with the amount including GST
					$sSQLString .= "0,'";
					$sSQLString .= $this->reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $this->descript1."',";
					$sSQLString .= $taxpcent.")";			
					
					$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
					$cltdb = $_SESSION['s_cltdb'];
					mysql_select_db($cltdb) or die(mysql_error());	
					
					if ($aged == 'Current') {
						$sql = "update client_company_xref set current = current + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr;
					}
					if ($aged == 'D30') {
						$sql = "update client_company_xref set d30 = d30 + ".$totalvalue." where company_id = ".$coyno." and drno = ".$acdr." and drsub = ".$s2dr;
					}
					if ($aged == 'D60') {
						$sql = "update client_company_xref set d60 = d60 + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr;
					}
					if ($aged == 'D90') {
						$sql = "update client_company_xref set d90 = d90 + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr;
					}
					if ($aged == 'D120') {
						$sql = "update client_company_xref set d120 = d120 + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr;
					}
					
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
					
					$moduledb = $_SESSION['s_findb'];
					mysql_select_db($moduledb) or die(mysql_error());	
					
					
					// debit debtor control account
					$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values ";
					$sSQLString .= "('".$this->ddate."',";
					$sSQLString .= "801,'";		// debit debtors control
					$sSQLString .= $defbranch."',";
					$sSQLString .= '0'.",'";
					$sSQLString .= ' '."',";	
					$sSQLString .= $totalvalue.",";		// with the amount including GST
					$sSQLString .= "0,'";
					$sSQLString .= $this->reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $this->descript1."',";
					$sSQLString .= $taxpcent.")";					
					
					$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
						
					$sql = "update glmast set obal = obal + ".$totalamount." where accountno = 801 and branch = '".$defbranch."' and sub = 0";
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
					
					// credit relevant income accounts
					$q = "select * from ".$table;
					$result = mysql_query($q) or die(mysql_error());
					while ($row = mysql_fetch_array($result)) {
						extract($row);
						$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
						$sSQLString .= "('".$this->ddate."',";
						$sSQLString .= $sellacc.",";		//credit the income account
						$sSQLString .= "'".$defbranch."',";
						$sSQLString .= $sellsub.",";
						$sSQLString .= $a2dr.",'";
						$sSQLString .= ' '."',";			
						$sSQLString .= $s2dr.",";			
						$sSQLString .= "0,";		
						$sSQLString .= $value.",'";  // with the amount excluding GST
						$sSQLString .= $this->reference."','";					
						$sSQLString .= $taxtype."','";
						$sSQLString .= $this->descript1."',";
						$sSQLString .= $taxpcent.")";			
						
						$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
						
						$sql = "update glmast set obal = obal - ".$totalamount." where accountno = ".$sellacc." and branch = '".$defbranch."' and sub = ".$sellsub;
						$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
		
					}
			}
		
			//*******************************************************************************************************************************************
			// Cash Sale
			//*******************************************************************************************************************************************
			if ($this->transtype == 'C_S') {
					// debit bank etc. account
					$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
					$sSQLString .= "('".$this->ddate."',";
					$sSQLString .= $a2dr.",";		// debit the debtor
					$sSQLString .= "'".$defbranch."',";			
					$sSQLString .= $s2dr.",";
					$sSQLString .= $sellacc.",";
					$sSQLString .= "'".$defbranch."',";			
					$sSQLString .= $sellsub.",";			
					$sSQLString .= $totalvalue.",";		// with the amount including GST
					$sSQLString .= "0,'";
					$sSQLString .= $this->reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $this->descript1."',";
					$sSQLString .= $taxpcent.")";			
					
					$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
					$sql = "update glmast set obal = obal + ".$totalamount." where accountno = ".$a2dr." and branch = '".$defbranch."' and sub = ".$s2dr;
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
					
					// credit relevant income accounts
					$q = "select * from ".$table;
					$result = mysql_query($q) or die(mysql_error());
					while ($row = mysql_fetch_array($result)) {
						extract($row);
						$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
						$sSQLString .= "('".$this->ddate."',";
						$sSQLString .= $sellacc.",";		//credit the income account
						$sSQLString .= "'".$defbranch."',";
						$sSQLString .= $sellsub.",";
						$sSQLString .= $a2dr.",";
						$sSQLString .= "'".$defbranch."',";
						$sSQLString .= $s2dr.",";			
						$sSQLString .= "0,";		
						$sSQLString .= $totalamount.",'";  // with the amount excluding GST
						$sSQLString .= $this->reference."','";					
						$sSQLString .= $taxtype."','";
						$sSQLString .= $this->descript1."',";
						$sSQLString .= $taxpcent.")";			
						
						$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
						
						$sql = "update glmast set obal = obal - ".$totalamount." where accountno = ".$sellacc." and branch = '".$defbranch."' and sub = ".$sellsub;
						$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
		
					}
			}
			
		//*******************************************************************************************************************************************
			// Cash Purchase
			//*******************************************************************************************************************************************
			if ($this->transtype == 'C_P') {
					// credit creditor account
					$sSQLString = "insert into trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
					$sSQLString .= "('".$this->ddate."',";
					$sSQLString .= $a2cr.",";		// credit the paying account
					$sSQLString .= $s2cr.",";
					$sSQLString .= '0'.",'";
					$sSQLString .= ' '."',";			
					$sSQLString .= '0'.",";			
					$sSQLString .= "0,";		
					$sSQLString .= $totalvalue.",'";  // with the amount including GST
					$sSQLString .= $this->reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $this->descript1."',";
					$sSQLString .= $taxpcent.")";			
					
					$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
					$sql = "update glmast set obal = obal - ".$totalamount." where accountno = 851 and branch = '".$defbranch."' and sub = 0";
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
					
					// debit relevant cos accounts
					$q = "select * from ".$table;
					$result = mysql_query($q) or die(mysql_error());
					while ($row = mysql_fetch_array($result)) {
						extract($row);
						$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values ";
						$sSQLString .= "('".$this->ddate."',";
						$sSQLString .= $purchacc.",";		//debit the cos account
						$sSQLString .= "'".$defbranch."',";
						$sSQLString .= $purchsub.",";
						$sSQLString .= $a2cr.",'";
						$sSQLString .= ' '."',";			
						$sSQLString .= $s2cr.",";			
						$sSQLString .= $totalamount.",";		// with the amount excluding GST
						$sSQLString .= "0,'";
						$sSQLString .= $this->reference."','";					
						$sSQLString .= $taxtype."','";
						$sSQLString .= $this->descript1."',";
						$sSQLString .= $taxpcent.",";			
						$sSQLString .= $uid.")";			
						
						$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
						
						$sql = "update glmast set obal = obal + ".$totalamount." where accountno = ".$purchacc." and branch = '".$defbranch."' and sub = ".$purchsub;
						$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
		
					}
					
			}
			
		
		
			//*******************************************************************************************************************************************
			// Goods Received
			//*******************************************************************************************************************************************
			if ($this->transtype == 'GRN') {
					// credit creditor account
					$sSQLString = "insert into trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
					$sSQLString .= "('".$this->ddate."',";
					$sSQLString .= $a2cr.",";		// credit the creditor
					$sSQLString .= $s2cr.",";
					$sSQLString .= '0'.",'";
					$sSQLString .= ' '."',";			
					$sSQLString .= '0'.",";			
					$sSQLString .= "0,";		
					$sSQLString .= $totalvalue.",'";  // with the amount including GST
					$sSQLString .= $this->reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $this->descript1."',";
					$sSQLString .= $taxpcent.")";			
					
					$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
					$cltdb = $_SESSION['s_cltdb'];
					mysql_select_db($cltdb) or die(mysql_error());	
					
					if ($aged == 'Current') {
						$sql = "update client_company_xref set current = current - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr;
					}
					if ($aged == 'D30') {
						$sql = "update client_company_xref set d30 = d30 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr;
					}
					if ($aged == 'D60') {
						$sql = "update client_company_xref set d60 = d60 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr;
					}
					if ($aged == 'D90') {
						$sql = "update client_company_xref set d90 = d90 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr;
					}
					if ($aged == 'D120') {
						$sql = "update client_company_xref set d120 = d120 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr;
					}
					
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
					
					$moduledb = $_SESSION['s_findb'];
					mysql_select_db($moduledb) or die(mysql_error());	
					
					
					// credit creditor control account
					$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values ";
					$sSQLString .= "('".$this->ddate."',";
					$sSQLString .= "851,'";		// credit creditor control
					$sSQLString .= $defbranch."',";
					$sSQLString .= '0'.",'";
					$sSQLString .= ' '."',";	
					$sSQLString .= "0,";		// with the amount including GST
					$sSQLString .= $totalvalue.",'";
					$sSQLString .= $this->reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $this->descript1."',";
					$sSQLString .= $taxpcent.")";					
					
					$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
						
					$sql = "update glmast set obal = obal - ".$totalamount." where accountno = 851 and branch = '".$defbranch."' and sub = 0";
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
					
					// debit relevant cos accounts
					$q = "select * from ".$table;
					$result = mysql_query($q) or die(mysql_error());
					while ($row = mysql_fetch_array($result)) {
						extract($row);
						$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values ";
						$sSQLString .= "('".$this->ddate."',";
						$sSQLString .= $purchacc.",";		//debit the cos account
						$sSQLString .= "'".$defbranch."',";
						$sSQLString .= $purchsub.",";
						$sSQLString .= $a2cr.",'";
						$sSQLString .= ' '."',";			
						$sSQLString .= $s2cr.",";			
						$sSQLString .= $totalamount.",";		// with the amount excluding GST
						$sSQLString .= "0,'";
						$sSQLString .= $this->reference."','";					
						$sSQLString .= $taxtype."','";
						$sSQLString .= $this->descript1."',";
						$sSQLString .= $taxpcent.",";			
						$sSQLString .= $uid.")";			
						
						$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
						
						$sql = "update glmast set obal = obal + ".$totalamount." where accountno = ".$purchacc." and branch = '".$defbranch."' and sub = ".$purchsub;
						$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
		
					}
					
			}
			
		
			//*******************************************************************************************************************************************
			// Requisition
			//*******************************************************************************************************************************************
			if ($this->transtype == 'REQ') {
				$q = "select * from ".$table;
				$result = mysql_query($q) or die(mysql_error());
				while ($row = mysql_fetch_array($result)) {
					extract($row);
		
					// debit relevant cos account
					$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
					$sSQLString .= "('".$this->ddate."',";
					$sSQLString .= $purchacc.",";		// debit the debtor
					$sSQLString .= "'".$purchbr."',";			
					$sSQLString .= $purchsub.",";
					$sSQLString .= $purchacc.",";
					$sSQLString .= "'".$defbranch."',";			
					$sSQLString .= $purchsub.",";			
					$sSQLString .= $value.",";		// with the amount including GST
					$sSQLString .= "0,'";
					$sSQLString .= $this->reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $this->descript1." ".$item."',";
					$sSQLString .= $taxpcent.")";			
					
					$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
					$sql = "update glmast set obal = obal + ".$totalamount." where accountno = ".$purchacc." and branch = '".$purchbr."' and sub = ".$purchsub;
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
					
					// credit relevant cos account
					$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
					$sSQLString .= "('".$this->ddate."',";
					$sSQLString .= $purchacc.",";		//credit the income account
					$sSQLString .= "'".$defbranch."',";
					$sSQLString .= $purchsub.",";
					$sSQLString .= $purchacc.",";
					$sSQLString .= "'".$purchbr."',";
					$sSQLString .= $purchsub.",";			
					$sSQLString .= "0,";		
					$sSQLString .= $value.",'";  // with the amount excluding GST
					$sSQLString .= $this->reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $this->descript1." ".$item."',";
					$sSQLString .= $taxpcent.")";			
						
					$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
						
					$sql = "update glmast set obal = obal - ".$value." where accountno = ".$purchacc." and branch = '".$defbranch."' and sub = ".$purchsub;
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
		
			
					// Inter branch Transactions
					if ($purchbr != $defbranch) {
					
						// credit inter branch transfer account
						$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
						$sSQLString .= "('".$this->ddate."',";
						$sSQLString .= "997".",'";		
						$sSQLString .= $purchbr."',";
						$sSQLString .= "0".",";
						$sSQLString .= $purchacc.",'";
						$sSQLString .= $defbranch."',";			
						$sSQLString .= $purchsub.",";		
						$sSQLString .= "0,";
						$sSQLString .= $value.",'";  // with the amount excluding GST
						$sSQLString .= $this->reference."','";					
						$sSQLString .= $taxtype."','";
						$sSQLString .= $this->descript1." ".$item."',";
						$sSQLString .= $taxpcent.")";			
						
						$r33 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
						
						$sql = "update glmast set obal = obal - ".$value." where accountno = 997 and branch = '".$purchbr."'";
						$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
						
						// debit inter branch transfer account
						$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
						$sSQLString .= "('".$this->ddate."',";
						$sSQLString .= "997".",'";		
						$sSQLString .= $defbranch."',";
						$sSQLString .= "0,";
						$sSQLString .= $purchacc.",'";
						$sSQLString .= $purchbr."',";			
						$sSQLString .= $purchsub.",";			
						$sSQLString .= $value.",";  // with the amount excluding GST
						$sSQLString .= "0,'";
						$sSQLString .= $this->reference."','";					
						$sSQLString .= $taxtype."','";
						$sSQLString .= $this->descript1." ".$item."',";
						$sSQLString .= $taxpcent.")";			
						
						$r34 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
						
						$sql = "update glmast set obal = obal + ".$value." where accountno = 997 and branch = '".$defbranch."'";
						$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
							
					} // if posting between different branches
							
				} // while
					
			} // if
			
			//*******************************************************************************************************************************************
			// Credit Note
			//*******************************************************************************************************************************************
			if ($this->transtype == 'CRN') {
				
					$qbr = "select branch from stklocs where uid = ".$this->loc;
					$rbr = mysql_query($qbr) or die(mysql_error());
					$row = mysql_fetch_array($rbr);
					extract($row);
					$defbranch = $branch;		
				
					// credit debtor account
					$sSQLString = "insert into trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
					$sSQLString .= "('".$this->ddate."',";
					$sSQLString .= $a2cr.",";		// credit the debtor
					$sSQLString .= $s2cr.",";
					$sSQLString .= $sellacc.",";
					$sSQLString .= "'".$defbranch."',";			
					$sSQLString .= $sellsub.",";			
					$sSQLString .= "0,";		
					$sSQLString .= $totalvalue.",'";  // with the amount including GST
					$sSQLString .= $this->reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $this->descript1."',";
					$sSQLString .= $taxpcent.")";			
					
					$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
					$cltdb = $_SESSION['s_cltdb'];
					mysql_select_db($cltdb) or die(mysql_error());	
					
					if ($aged == 'Current') {
						$sql = "update client_company_xref set current = current - ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2cr." and drsub = ".$s2cr;
					}
					if ($aged == 'D30') {
						$sql = "update client_company_xref set d30 = d30 - ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2cr." and drsub = ".$s2cr;
					}
					if ($aged == 'D60') {
						$sql = "update client_company_xref set d60 = d60 - ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2cr." and drsub = ".$s2cr;
					}
					if ($aged == 'D90') {
						$sql = "update client_company_xref set d90 = d90 - ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2cr." and drsub = ".$s2cr;
					}
					if ($aged == 'D120') {
						$sql = "update client_company_xref set d120 = d120 - ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2cr." and drsub = ".$s2cr;
					}
					
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
					
					$moduledb = $_SESSION['s_findb'];
					mysql_select_db($moduledb) or die(mysql_error());	
					
					
					// credit debtor control account
					$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values ";
					$sSQLString .= "('".$this->ddate."',";
					$sSQLString .= "801,'";		// credit debtor control
					$sSQLString .= $defbranch."',";
					$sSQLString .= '0'.",'";
					$sSQLString .= ' '."',";	
					$sSQLString .= "0,";		// with the amount including GST
					$sSQLString .= $totalvalue.",'";
					$sSQLString .= $this->reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $this->descript1."',";
					$sSQLString .= $taxpcent.")";					
					
					$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
						
					$sql = "update glmast set obal = obal - ".$totalvalue." where accountno = 801 and branch = '".$defbranch."' and sub = 0";
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
					
					// debit relevant income accounts
					$q = "select * from ".$table;
					$result = mysql_query($q) or die(mysql_error());
					while ($row = mysql_fetch_array($result)) {
						extract($row);
						$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values ";
						$sSQLString .= "('".$this->ddate."',";
						$sSQLString .= $sellacc.",";		//debit the income account
						$sSQLString .= "'".$defbranch."',";
						$sSQLString .= $sellsub.",";
						$sSQLString .= $a2cr.",'";
						$sSQLString .= ' '."',";			
						$sSQLString .= $s2cr.",";			
						$sSQLString .= $value.",";		// with the amount excluding GST
						$sSQLString .= "0,'";
						$sSQLString .= $this->reference."','";					
						$sSQLString .= $taxtype."','";
						$sSQLString .= $this->descript1."',";
						$sSQLString .= $taxpcent.",";			
						$sSQLString .= $uid.")";			
						
						$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
						
						$sql = "update glmast set obal = obal + ".$value." where accountno = ".$sellacc." and branch = '".$defbranch."' and sub = ".$sellsub;
						$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
		
					}
					
			}
			
		//*******************************************************************************************************************************************
			// Goods Returned
			//*******************************************************************************************************************************************
			if ($this->transtype == 'RET') {
				
					$qbr = "select branch from stklocs where uid = ".$this->loc;
					$rbr = mysql_query($qbr) or die(mysql_error());
					$row = mysql_fetch_array($rbr);
					extract($row);
					$defbranch = $branch;		
				
					// debit creditor account
					$sSQLString = "insert into trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
					$sSQLString .= "('".$this->ddate."',";
					$sSQLString .= $a2dr.",";		// debit the creditor
					$sSQLString .= $s2dr.",";
					$sSQLString .= $sellacc.",";
					$sSQLString .= "'".$defbranch."',";			
					$sSQLString .= $sellsub.",";			
					$sSQLString .= $totalvalue.",";  // with the amount including GST
					$sSQLString .= "0,'";		
					$sSQLString .= $this->reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $this->descript1."',";
					$sSQLString .= $taxpcent.")";			
					
					$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
					$cltdb = $_SESSION['s_cltdb'];
					mysql_select_db($cltdb) or die(mysql_error());	
					
					if ($aged == 'Current') {
						$sql = "update client_company_xref set current = current - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2dr." and crsub = ".$s2dr;
					}
					if ($aged == 'D30') {
						$sql = "update client_company_xref set d30 = d30 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2dr." and crsub = ".$s2dr;
					}
					if ($aged == 'D60') {
						$sql = "update client_company_xref set d60 = d60 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2dr." and crsub = ".$s2dr;
					}
					if ($aged == 'D90') {
						$sql = "update client_company_xref set d90 = d90 - ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and crsub = ".$s2dr;
					}
					if ($aged == 'D120') {
						$sql = "update client_company_xref set d120 = d120 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2dr." and crsub = ".$s2dr;
					}
					
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
					
					$moduledb = $_SESSION['s_findb'];
					mysql_select_db($moduledb) or die(mysql_error());	
					
					
					// debit creditor control account
					$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values ";
					$sSQLString .= "('".$this->ddate."',";
					$sSQLString .= "851,'";		// debit creditors control
					$sSQLString .= $defbranch."',";
					$sSQLString .= '0'.",'";
					$sSQLString .= ' '."',";	
					$sSQLString .= $totalvalue.",";
					$sSQLString .= "0,'";		// with the amount including GST
					$sSQLString .= $this->reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $this->descript1."',";
					$sSQLString .= $taxpcent.")";					
					
					$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
						
					$sql = "update glmast set obal = obal + ".$totalvalue." where accountno = 851 and branch = '".$defbranch."' and sub = 0";
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
					
					// debit relevant income accounts
					$q = "select * from ".$table;
					$result = mysql_query($q) or die(mysql_error());
					while ($row = mysql_fetch_array($result)) {
						extract($row);
						$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values ";
						$sSQLString .= "('".$this->ddate."',";
						$sSQLString .= $sellacc.",";		//debit the income account
						$sSQLString .= "'".$defbranch."',";
						$sSQLString .= $sellsub.",";
						$sSQLString .= $a2cr.",'";
						$sSQLString .= ' '."',";			
						$sSQLString .= $s2cr.",";			
						$sSQLString .= $value.",";		// with the amount excluding GST
						$sSQLString .= "0,'";
						$sSQLString .= $this->reference."','";					
						$sSQLString .= $taxtype."','";
						$sSQLString .= $this->descript1."',";
						$sSQLString .= $taxpcent.",";			
						$sSQLString .= $uid.")";			
						
						$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
						
						$sql = "update glmast set obal = obal + ".$value." where accountno = ".$sellacc." and branch = '".$defbranch."' and sub = ".$sellsub;
						$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
		
					}
					
			}
			
		
			//*************************************************************************************************************************************************************
			// Create double entries relating to GST for each line of trading document
			//*************************************************************************************************************************************************************
			if ($this->transtype != 'REQ') {					
				$q = "select * from ".$table;
				$result = mysql_query($q) or die(mysql_error());
				while ($row = mysql_fetch_array($result)) {
					extract($row);
					
					// insert each line's tax where applicable into trmain. If GRN add record anyway.
					if (($this->transtype == 'GRN') || (($this->transtype != 'GRN') && ($tax > 0))) {
						if ($taxdrcr == 'dr') {
							$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values ";
							$sSQLString .= "('".$this->ddate."',";
							$sSQLString .= "870,'";		// debit GST payable
							$sSQLString .= $defbranch."',";
							$sSQLString .= "0,";
							$sSQLString .= $a2cr.",'";
							$sSQLString .= $defbranch."',";	
							$sSQLString .= $s2cr.",";
							$sSQLString .= $tax.",";		// with the amount of the GST	
							$sSQLString .= "0,'";		
							$sSQLString .= $this->reference."','";					
							$sSQLString .= $taxtype."','";
							$sSQLString .= $this->descript1."',";
							$sSQLString .= $taxpcent.",";					
							$sSQLString .= $uid.")";					
						} else {
							$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values ";
							$sSQLString .= "('".$this->ddate."',";
							$sSQLString .= "870,'";		// credit GST payable
							$sSQLString .= $defbranch."',";
							$sSQLString .= "0,";
							$sSQLString .= $a2dr.",'";
							$sSQLString .= $defbranch."',";	
							$sSQLString .= $s2dr.",";
							$sSQLString .= "0,";		
							$sSQLString .= $tax.",'";		// with the amount of the GST
							$sSQLString .= $this->reference."','";					
							$sSQLString .= $taxtype."','";
							$sSQLString .= $this->descript1."',";
							$sSQLString .= $taxpcent.",";					
							$sSQLString .= $uid.")";					
						}// if
						$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
					} //if
					
				
				} //while
			}
		
			//***********************************************************************************************************************************
			// Create entries in stktrans for each stock recorded item
			//***********************************************************************************************************************************
			$q = "select * from ".$table;
			$result = mysql_query($q) or die(mysql_error());
			while ($row = mysql_fetch_array($result)) {
				extract($row);
				if ($this->transtype == 'INV' || $this->transtype == 'C_S' || $this->transtype == 'REQ' || $this->transtype == 'RET') {
					$increase = 0;
					$decrease = $quantity;
					$sql = "update stkmast set onhand = onhand - ".$decrease." where itemcode = '".$itemcode."'";
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
				} else {
					$increase = $quantity;
					$decrease = 0;
					$sql = "update stkmast set onhand = onhand + ".$increase." where itemcode = '".$itemcode."'";
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);
					if ($this->transtype == 'GRN' && $value == 0) {
						$sqlc = "update stkmast set uncosted = uncosted + ".$increase." where itemcode = '".$itemcode."'";
						$rsqlc = mysql_query($sqlc) or die(mysql_error().' '.$sqlc);
					} //if
					
					// calculate new average cost
					if ($this->transtype == 'GRN' || $this->transtype == 'CRN') {
						if ($value > 0) {
							$qav = "select avgcost,onhand-uncosted as tqty from stkmast where itemcode = '".$itemcode."'";
							$rav = mysql_query($qav) or die(mysql_error().' '.$qav);
							$row = mysql_fetch_array($rav);
							extract($row);
							$newtotval = ($avgcost*$tqty) + $value;  
							$newtotqty = $tqty ;			
							$newavgcost = $newtotval/$newtotqty;	
							$sqla = "update stkmast set avgcost = ".$newavgcost." where itemcode = '".$itemcode."'";
							$rsqla = mysql_query($sqla) or die(mysql_error().' '.$sqla);
						}
						
					} //if
					
				} // if
				
				
				$sSQLString = "insert into stktrans (groupid,catid,itemcode,item,locid,ddate,increase,decrease,ref_no,transtype,amount) values ";
				$sSQLString .= "(".$groupid.",";
				$sSQLString .= $catid.",";
				$sSQLString .= "'".$itemcode."','";
				$sSQLString .= $item."','";
				$sSQLString .= $this->loc."','";
				$sSQLString .= $this->ddate."',";
				$sSQLString .= $increase.",";
				$sSQLString .= $decrease.",'";
				$sSQLString .= $this->reference."','";
				$sSQLString .= $this->transtype."',";
				$sSQLString .= $value.")";
				
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
			} // while
		
			//**************************************************************************************************************************************************************************
			// add serial numbers if applicable
			//**************************************************************************************************************************************************************************
					$qs = "select * from ".$serialtable;
					$rs = mysql_query($qs) or die(mysql_error().' '.$qs);
					$numrows = mysql_num_rows($rs);
					if ($numrows > 0) {
						while ($row = mysql_fetch_array($rs)) {
							extract($row);
							$qi = "insert into stkserials (itemcode,item,serialno,locationid,ref_no,sold,branch,date,activity) values (";
							$qi .= "'".$itemcode."','".$item."','".$serialno."',".$locationid.",'".$this->reference."','','".$defbranch."','".$this->ddate."','".$this->descript1."')";
							$ri = mysql_query($qi) or die(mysql_error().' '.$qi);
							
							if ($this->transtype == 'INV' || $this->transtype == 'C_S' || $this->transtype == 'REQ' || $this->transtype == 'CRN') {
								$qu = "update stkserials set sold = '".$this->reference."' where serialno = '".$serialno."'";
								$ru = mysql_query($qu) or die(mysql_error().' '.$qu);
							}
						}
					}
		
		
		
			//**************************************************************************************************************************************************************************
			// add transaction to audit trail
			//**************************************************************************************************************************************************************************
					$sSQLString = "insert into audit (ddate,acc2dr,brdr,subdr,acc2cr,brcr,subcr,descript1,reference,amount,tax,total,entrydate,entrytime,username,userip) values ";
					$sSQLString .= "('".$this->ddate."',";
					$sSQLString .= $a2dr.",'";		
					$sSQLString .= $defbranch."',";
					$sSQLString .= $s2dr.",";
					$sSQLString .= $a2cr.",'";
					$sSQLString .= $defbranch."',";			
					$sSQLString .= $s2cr.",'";			
					$sSQLString .= $this->descript1."','";
					$sSQLString .= $this->reference."',";		
					$sSQLString .= $totalamount.",";					
					$sSQLString .= $totaltax.",";
					$sSQLString .= $totalvalue.",'";
					$sSQLString .= date("Y-m-d")."','";
					$sSQLString .= date("H:i:s")."','";				
					$sSQLString .= $unm."','";
					$sSQLString .= $uip."')";		
					
					$r35 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		
		
		$sql = "delete from ".$table;
		$rst = mysql_query($sql) or die (mysql_error());
		$sql = "delete from ".$serialtable;
		$rst = mysql_query($sql) or die (mysql_error());
			
	}
}

?>

</body>
</html>
