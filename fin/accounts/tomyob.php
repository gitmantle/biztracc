<?php
 class ToMYOB
 {
 	public $sHostName;
	public $nPort;
	public $sConnectionString;
	public $sRefNo;
	public $dDate;
	public $sMemo;
	public $sAcc2Dr;
	public $nAmtIncTax;
	public $nAmtExTax;
	public $nAmtGST;
	public $sAcc2Cr;
	public $sSQLString;

	function Sales()
    {
	 $fToOpen = fsockopen($this->sHostName, $this->nPort, &$errno, &$errstr, 30);
	 if (!$fToOpen) {
	 //contruct error string to return
	 $sReturn = "<?xml version=\"1.0\"?>\r\n<result state=\"failure\">\r\n<error>$errstr</error>\r\n</result>\r\n";
	 }  else {

	//construct XML to send
	//search and replace HTML chars in SQL first

	   $this->sSQLString = "insert into Import_General_Journals (JournalNumber,JournalDate,Memo,GSTBASReporting,Inclusive,AccountNumber,DebitExTaxAmount,DebitIncTaxAmount,CreditExTaxAmount,CreditIncTaxAmount,TaxCode,GSTAmount) values ((";
	   $this->sSQLString .= $this->sRefNo . ",";
	   $this->sSQLString .= $this->dDate . ",";
	   $this->sSQLString .= $this->sMemo . ",";
	   $this->sSQLString .= "'S'" .",";
	   $this->sSQLString .= "'0'" .",";
	   $this->sSQLString .= $this->sAcc2Dr .",";
	   $this->sSQLString .= $this->nAmtExTax .",";
	   $this->sSQLString .= $this->nAmtIncTax .",";
	   $this->sSQLString .= "0.00" .",";
	   $this->sSQLString .= "0.00" .",";
	   $this->sSQLString .= "''" .",";
	   $this->sSQLString .= "0.00" ."),(";
	   $this->sSQLString .= $this->sRefNo . ",";
	   $this->sSQLString .= $this->dDate . ",";
	   $this->sSQLString .= $this->sMemo . ",";
	   $this->sSQLString .= "'S'" .",";
	   $this->sSQLString .= "'0'" .",";
	   $this->sSQLString .= $this->sAcc2Cr .",";
	   $this->sSQLString .= "0.00" .",";
	   $this->sSQLString .= "0.00" .",";
	   $this->sSQLString .= $this->nAmtExTax .",";
	   $this->sSQLString .= $this->nAmtIncTax .",";
	   $this->sSQLString .= "'GST'" .",";
	   $this->sSQLString .= $this->nAmtGST ."))";

	   $this->sSQLString = HTMLSpecialChars($this->sSQLString);
   	   $sSend = "<?xml version=\"1.0\"?>\r\n<request>\r\n<connectionstring>$this->sConnectionString</connectionstring>\r\n<sql>$this->sSQLString</sql>\r\n</request>\r\n";
	   //write request
	   fputs($fToOpen, $sSend);
	   //now read response
	   while (!feof($fToOpen))
	   {
	   $sReturn = $sReturn . fgets($fToOpen, 128);
	   }
	 }
	 fclose($fToOpen);
	 //return($this->sSQLString);
	 return $sReturn;
	}

	function Purchase()
    {
	 $fToOpen = fsockopen($this->sHostName, $this->nPort, &$errno, &$errstr, 30);
	 if (!$fToOpen) {
	 //contruct error string to return
	 $sReturn = "<?xml version=\"1.0\"?>\r\n<result state=\"failure\">\r\n<error>$errstr</error>\r\n</result>\r\n";
	 }  else {

	//construct XML to send
	//search and replace HTML chars in SQL first

	   $this->sSQLString = "insert into Import_General_Journals (JournalNumber,JournalDate,Memo,GSTBASReporting,Inclusive,AccountNumber,DebitExTaxAmount,DebitIncTaxAmount,CreditExTaxAmount,CreditIncTaxAmount,TaxCode,GSTAmount) values ((";
	   $this->sSQLString .= $this->sRefNo . ",";
	   $this->sSQLString .= $this->dDate . ",";
	   $this->sSQLString .= $this->sMemo . ",";
	   $this->sSQLString .= "'P'" .",";
	   $this->sSQLString .= "'0'" .",";
	   $this->sSQLString .= $this->sAcc2Dr .",";
	   $this->sSQLString .= $this->nAmtExTax .",";
	   $this->sSQLString .= $this->nAmtIncTax .",";
	   $this->sSQLString .= "0.00" .",";
	   $this->sSQLString .= "0.00" .",";
	   $this->sSQLString .= "'GST'" .",";
	   $this->sSQLString .= $this->nAmtGST ."),(";
	   $this->sSQLString .= $this->sRefNo . ",";
	   $this->sSQLString .= $this->dDate . ",";
	   $this->sSQLString .= $this->sMemo . ",";
	   $this->sSQLString .= "'P'" .",";
	   $this->sSQLString .= "'0'" .",";
	   $this->sSQLString .= $this->sAcc2Cr .",";
	   $this->sSQLString .= "0.00" .",";
	   $this->sSQLString .= "0.00" .",";
	   $this->sSQLString .= $this->nAmtExTax .",";
	   $this->sSQLString .= $this->nAmtIncTax .",";
	   $this->sSQLString .= "''" .",";
	   $this->sSQLString .= "0.00" ."))";

	   $this->sSQLString = HTMLSpecialChars($this->sSQLString);
   	   $sSend = "<?xml version=\"1.0\"?>\r\n<request>\r\n<connectionstring>$this->sConnectionString</connectionstring>\r\n<sql>$this->sSQLString</sql>\r\n</request>\r\n";
	   //write request
	   fputs($fToOpen, $sSend);
	   //now read response
	   while (!feof($fToOpen))
	   {
	   $sReturn = $sReturn . fgets($fToOpen, 128);
	   }
	 }
	 fclose($fToOpen);
	 //return($this->sSQLString);
	 return $sReturn;
	}
 }
?>

