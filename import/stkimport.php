<?php

class stkimport
{


	// properties for adding client details
	public $xgroup;
	public $xcategory ;
	public $xcode;
	public $xitem;
	public $xavgcost;
	public $xunit;
	public $xsellacc;
	public $xsellsub;
	public $xpurchacc;
	public $xpurchsub;
	public $xdeftax;
	public $xactive;
	public $xstock;
	
	// private for internal use only
	private $sSQLString;
	
	
//**************************************************************
	function updateStock()
//**************************************************************
	{
		$uploadfile = $this->xfile;

		// get data from generic Members table - our Excel spreadsheet
		$query = 'Select * from '.$uploadfile;
		$result = mysql_query($query) or die (mysql_error().' '.$query);
		while ($row = mysql_fetch_array($result) or die(mysql_error().' '.$result)) {
			extract($row);
			
			$acell = 'x'.$this->xgroup;
			$agroup = ${$acell}; 
				
			$acell = 'x'.$this->xcategory;
			$acategory = ${$acell};
				
			$acell = 'x'.$this->xcode;
			$acode = ${$acell};
			
			$acell = 'x'.$this->xitem;
			$aitem = ${$acell};

			$acell = 'x'.$this->xavgcost;
			$aavgcost = ${$acell};
			
			$acell = 'x'.$this->xunit;
			$aunit = ${$acell};

			$acell = 'x'.$this->xsellacc;
			$asellacc = ${$acell};

			$acell = 'x'.$this->xsellsub;
			$asellsub = ${$acell};

			$acell = 'x'.$this->xpurchacc;
			$apurchacc = ${$acell};

			$acell = 'x'.$this->xpurchsub;
			$apurchsub = ${$acell};

			$acell = 'x'.$this->xdeftax;
			$adeftax = ${$acell};

			$acell = 'x'.$this->xactive;
			$aactive = ${$acell};

			$acell = 'x'.$this->xstock;
			$astock = ${$acell};
			
	
			if (trim($acode) != "") {
				$q = 'select itemcode from stkmast where itemcode = "'.$acode.'"';
				$r = mysql_query($q) or die (mysql_error().' '.$q);
				$memrows = mysql_num_rows($r);
				if ($memrows == 0) {

					$qi = "insert into stkmast (itemcode) values ('".$acode."')";
					$ri = mysql_query($qi) or die(mysql_error().' '.$qi);
					$memid = mysql_insert_id();
		
					// update members
					$qmu = "update stkmast set ";
					$qmu .= 'groupid = '.$agroup.',';
					$qmu .= 'catid = '.$acategory.',';
					$qmu .= 'item = "'.$aitem.'",';
					$qmu .= 'avgcost = '.$aavgcost.',';
					$qmu .= 'unit = "'.$aunit.'",';
					$qmu .= 'sellacc = '.$asellacc.',';
					$qmu .= 'sellsub = '.$asellsub.',';
					$qmu .= 'purchacc = '.$apurchacc.',';
					$qmu .= 'purchsub = '.$apurchsub.',';
					$qmu .= 'deftax = '.$adeftax.',';
					$qmu .= 'active = "'.$aactive.'",';
					$qmu .= 'stock = "'.$astock.'"';
					$qmu .= ' where itemid = '.$memid;
					
					$rmu = mysql_query($qmu) or die(mysql_error().' '.$qmu);
				
					
				} // memrows = 0
			} // trim($alastname) != ""
		} // while
	} // function clientimport

} //class

?>