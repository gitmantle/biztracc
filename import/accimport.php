<?php

class accimport
{
	// properties for adding/updating policy details
	public $xfile;
	public $xaccountname;
	public $xdrbal;
	public $xcrbal;
	

	// private for internal use only
	private $sSQLString;
	

//**************************************************************
	function updateGL()
//**************************************************************
	{
		$uploadfile = $this->xfile;
		
		
		$query = 'Select * from '.$uploadfile;
		$result = mysql_query($query) or die (mysql_error().' '.$query);
		while ($row = mysql_fetch_array($result)) {
			extract($row);

			$acell = 'x'.$this->xaccountname;
			$aaccountname = ${$acell};

			$acell = 'x'.$this->xdrbal;
			$adrbal = ${$acell};
			if ($adrbal == "" || $adrbal == "-") {
				$adrbal = 0;
			}
			if (!is_numeric($adrbal)) {
				$adrbal = 0;
			}
			
			$acell = 'x'.$this->xcrbal;
			$acrbal = ${$acell};
			if ($acrbal == "" || $acrbal == "-") {
				$acrbal = 0;
			}
			if (!is_numeric($acrbal)) {
				$acrbal = 0;
			}
			

			$q = "insert into glmast (account,branch,sub,obal) values (";
			$q .= "'".$aaccountname."',";
			$q .= "'0001',0,";
			if ($adrbal > 0) {
				$q.= $adrbal.")";
			} else {
				$q.= ($acrbal*-1).")";
			}
			
			$r = mysql_query($q) or die(mysql_error().' '.$q);
			
		} // while
	} // function uploadpolicy



} //class

?>