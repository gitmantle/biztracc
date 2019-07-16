<?php
class addmed
{
	public $coy;
	public $uid;
	public $blocked;

	// properties for adding account
	public $aname;
	public $aaccno;
	public $abranch = '';
	public $asub = 0;
	public $alastyear = 0;
	public $arecon = 'N';
	public $ablocked = 'N';
	public $aactive = 'Y';
	public $apaygst = 'N';
	public $asc = '0';
	public $ird10a = '0';
	public $ird10a2 = '0';
	public $grp = '';
	
	// additional properties for adding assets
	public $ahcode;
	public $acost = 0;
	public $abought = '0000-00-00';
	public $arate = 0;
	public $away = 'D';
	public $alastyrcost = 0;
	public $alastyrbv = 0;
	public $atotdep = 0;
	public $anotes = '';
	
	// properties for asset headings
	public $heading;

	// properties for adding existing client as debtor or creditor
	public $clientid;
	public $companyid;
	public $drno = 0;
	public $drsub = 0;
	public $crno = 0;
	public $crsub = 0;
	
	// properties for adding new client as debtor or creditor
	public $slegalname='';
	public $sfirstname='';
	public $stradingname='';
	public $sphad1='';
	public $sphad2='';
	public $sphsuburb='';
	public $sphcity='';
	public $sphpostcode='';
	public $sphstate='';
	public $sphcountry='';
	public $sbilad1='';
	public $sbilad2='';
	public $sbilsuburb='';
	public $sbilcity='';
	public $sbilpostcode='';
	public $sbilstate='';
	public $sbilcountry='';
	public $sphone='';
	public $sfax='';
	public $smobile='';
	public $semail='';
	public $sortname;
	
	// properties for depots
	public $sad1;
	public $sad2;
	public $stown;
	public $spostcode;
	public $scountry;
	public $pad1;
	public $pad2;
	public $ptown;
	public $ppostcode;
	public $pcountry;
	public $phone;
	public $mobile;

	// properties for medicines
	public $medicine;
	public $required;
	public $dosage;
	public $instructions;
	public $edate;
	
	// additional properties for adding sub debtors/ceditors
	public $mainclientid;
	
	// propeties for editing reference numbers
	public $inv;
	public $c_s;
	public $req;
	public $grn;
	public $crn;
	public $ret;
	public $chq;
	public $adj;
	public $tsf;
	public $dep;
	public $jnl;
	public $oth;
	public $rec;
	public $crd;
	public $ebk;
	public $p_c;
	public $pur;
	public $sal;
	public $pay;
	public $c_n;
	public $r_t;	

	//properties for add/edit a stock group
	public $groupname;
	public $stock;
	
	//properties for add/edit a stock category
	public $groupid;
	public $category;

	//properties for add/edit stock item
	public $itemid;
	public $itemcode;
	public $catid;
	public $item;
	public $barno = ' ';
	public $unit;
	public $noinunit;
	public $avgcost = 0;
	public $sellacc;
	public $purchacc;
	public $setsell;
	public $deftax;
	public $trackserial;
	public $trackstock;
	public $bom;
	public $ggroupid;
	public $gcatid;
	public $gitem;
	public $gitemcode;
	public $gsetsell;
	public $gdeftax;
	public $gorigitemcode;
	public $gitemid;
	public $gunit;
	public $gnoinunit;
	public $supplier;
	public $gsupplier;
	
	// properties for add/edit branches
	public $branchcode;
	public $branchname;
	
	// properties for add/edit locations
	public $location;
	public $locad1;			
	public $locad2;			
	public $locad3;			
	
	// private for internal use only
	//private $sSQLString;
	private $sSQLString;
	private $newclientid;
	private $newaccountno;
	private $tdate;

	
//*************************************************************
	function formatdate()
//	 23/09/2007 to 2007-09-23
//*************************************************************
	{
		$strDay = substr($this->ddate,0,2);
		$strMonth = substr($this->ddate,3,2);
		$strYear = substr($this->ddate,6,4);
		$this->tdate = $strYear.'-'.$strMonth.'-'.$strDay;	
		
	} //formatdate()
	
//**************************************************************
	function AddGroup()
//**************************************************************
	{

		$this->sSQLString = "insert into stkgroup (groupname) values ";
		$this->sSQLString .= "('".$this->groupname."')";
	
		$this->updt();


	} //addgroup()

//**************************************************************
	function EditGroup()
//**************************************************************
	{
	
		$this->sSQLString = "update stkgroup set ";
		$this->sSQLString .= "groupname = '".$this->groupname."'";
		$this->sSQLString .= " where groupid = ".$this->uid;

		$this->updt();
	
	} //editGroup()

//**************************************************************
	function DelGroup()
//**************************************************************
	{
	
	$this->sSQLString = "delete from stkgroup where groupid = ".$this->uid;

	$this->updt();
			
	} // delGroup()

//**************************************************************
	function AddCategory()
//**************************************************************
	{

		$this->sSQLString = "insert into stkcategory (groupid,category) values ";
		$this->sSQLString .= "(".$this->groupid.",'";
		$this->sSQLString .= $this->category."')";
	
		$this->updt();

	} //addcategory()

//**************************************************************
	function EditCategory()
//**************************************************************
	{
	
		$this->sSQLString = "update stkcategory set ";
		$this->sSQLString .= "groupid = ".$this->groupid.",";
		$this->sSQLString .= "category = '".$this->category."'";
		$this->sSQLString .= " where catid = ".$this->uid;

		$this->updt();
	
	} //editcategory()

//**************************************************************
	function DelCategory()
//**************************************************************
	{
	
	$this->sSQLString = "delete from stkcategory where catid = ".$this->uid;

	$this->updt();
			
	} // delCategory()

//**************************************************************
	function AddItem()
//**************************************************************
	{
		
		if ($this->sellacc != '') {
			$sas = split('~',$this->sellacc);
			$sa = $sas[1];
			$ss = $sas[2];
		} else {
			$sa = 0;
			$ss = 0;
		}
		if ($this->purchacc != '') {
			$pas = split('~',$this->purchacc);		
			$pa = $pas[1];
			$ps = $pas[2];
		} else {
			$pa = 0;
			$ps = 0;
		}
		
		
		$this->sSQLString = "insert into stkmast (groupid,catid,item,itemcode,barno,unit,noinunit,sellacc,sellsub,purchacc,purchsub,setsell,deftax,active,trackserial,stock,supplier) values ";
		$this->sSQLString .= "(".$this->groupid.",";
		$this->sSQLString .= $this->catid.",'";
		$this->sSQLString .= $this->item."','";
		$this->sSQLString .= $this->itemcode."','";
		$this->sSQLString .= $this->barno."','";
		$this->sSQLString .= $this->unit."',";
		$this->sSQLString .= $this->noinunit.",";
		$this->sSQLString .= $sa.",";
		$this->sSQLString .= $ss.",";
		$this->sSQLString .= $pa.",";
		$this->sSQLString .= $ps.",";
		$this->sSQLString .= $this->setsell.",'";
		$this->sSQLString .= $this->deftax."','";
		$this->sSQLString .= $this->blocked."','";
		$this->sSQLString .= $this->trackserial."','";
		$this->sSQLString .= $this->trackstock."',";
		$this->sSQLString .= $this->supplier.")";
	
		$this->updt();
		$newstditemid = $this->newaccountno;
		
		if ($this->gitem <> '') {
			$this->sSQLString = "insert into stkmast (groupid,catid,item,itemcode,barno,unit,noinunit,sellacc,sellsub,purchacc,purchsub,setsell,deftax,active,trackserial,stock,supplier,xref,generic) values ";
			$this->sSQLString .= "(".$this->ggroupid.",";
			$this->sSQLString .= $this->gcatid.",'";
			$this->sSQLString .= $this->gitem."','";
			$this->sSQLString .= $this->gitemcode."','";
			$this->sSQLString .= $this->barno."','";
			$this->sSQLString .= $this->gunit."',";
			$this->sSQLString .= $this->gnoinunit.",";
			$this->sSQLString .= $sa.",";
			$this->sSQLString .= $ss.",";
			$this->sSQLString .= $pa.",";
			$this->sSQLString .= $ps.",";
			$this->sSQLString .= $this->gsetsell.",'";
			$this->sSQLString .= $this->gdeftax."','";
			$this->sSQLString .= $this->blocked."','";
			$this->sSQLString .= $this->trackserial."','";
			$this->sSQLString .= $this->trackstock."',";
			$this->sSQLString .= $this->gsupplier.",";
			$this->sSQLString .= $newstditemid.",";
			$this->sSQLString .= "'Y')";
		
			$this->updt();
			$newgstkitemid = $this->newaccountno;
			
			$this->sSQLString = "update stkmast set xref = ".$newgstkitemid." where itemid = ".$newstditemid;
			$this->updt();
		}

	} //additem()

//**************************************************************
	function EditItem()
//**************************************************************
	{
		$sas = split('~',$this->sellacc);
		$sa = $sas[1];
		$ss = $sas[2];
		$pas = split('~',$this->purchacc);		
		$pa = $pas[1];
		$ps = $pas[2];
			
		$this->sSQLString = "update stkmast set ";
		$this->sSQLString .= "groupid = ".$this->groupid.",";
		$this->sSQLString .= "catid = ".$this->catid.",";
		$this->sSQLString .= "item = '".$this->item."',";
		//$this->sSQLString .= "itemcode = '".$this->itemcode."',";
		$this->sSQLString .= "barno = '".$this->barno."',";
		$this->sSQLString .= "unit = '".$this->unit."',";
		$this->sSQLString .= "noinunit = ".$this->noinunit.",";
		$this->sSQLString .= "sellacc = ".$sa.",";
		$this->sSQLString .= "sellsub = ".$ss.",";
		$this->sSQLString .= "purchacc = ".$pa.",";
		$this->sSQLString .= "purchsub = ".$ps.",";
		$this->sSQLString .= "setsell = ".$this->setsell.",";
		$this->sSQLString .= "deftax = ".$this->deftax.",";
		$this->sSQLString .= "active = '".$this->blocked."',";
		$this->sSQLString .= "stock = '".$this->trackstock."',";
		$this->sSQLString .= "supplier = ".$this->supplier.",";
		$this->sSQLString .= "bom = '".$this->bom."',";
		$this->sSQLString .= "trackserial = '".$this->trackserial."'";
		$this->sSQLString .= " where itemid = ".$this->uid;
	
		$this->updt();
		
		if ($this->gitemcode <> '') {
			if ($this->gitemcode == $this->gorigitemcode) { //Generic item has not been changed, just update old one
				$this->sSQLString = "update stkmast set ";
				$this->sSQLString .= "groupid = ".$this->ggroupid.",";
				$this->sSQLString .= "catid = ".$this->gcatid.",";
				$this->sSQLString .= "item = '".$this->gitem."',";
				//$this->sSQLString .= "itemcode = '".$this->gitemcode."',";
				$this->sSQLString .= "barno = '".$this->barno."',";
				$this->sSQLString .= "unit = '".$this->gunit."',";
				$this->sSQLString .= "noinunit = ".$this->gnoinunit.",";
				$this->sSQLString .= "sellacc = ".$sa.",";
				$this->sSQLString .= "sellsub = ".$ss.",";
				$this->sSQLString .= "purchacc = ".$pa.",";
				$this->sSQLString .= "purchsub = ".$ps.",";
				$this->sSQLString .= "setsell = ".$this->gsetsell.",";
				$this->sSQLString .= "deftax = ".$this->gdeftax.",";
				$this->sSQLString .= "active = '".$this->blocked."',";
				$this->sSQLString .= "stock = '".$this->trackstock."',";
				$this->sSQLString .= "supplier = ".$this->gsupplier.",";
				$this->sSQLString .= "bom = '".$this->bom."',";
				$this->sSQLString .= "trackserial = '".$this->trackserial."'";
				$this->sSQLString .= " where itemid = ".$this->gitemid;
			
				$this->updt();
			
			} else { //Generic item has been changed so create new one associated with standard and dissassociate old one.
				if ($this->gitmeid <> 0) {
					$this->sSQLString = "update stkmast set ";
					$this->sSQLString .= "xref = 0 where itemid = ".$this->gitemid;
				}
				
				$this->updt();
				
				$this->sSQLString = "insert into stkmast (groupid,catid,item,itemcode,barno,unit,noinunit,sellacc,sellsub,purchacc,purchsub,setsell,deftax,active,trackserial,stock,supplier,xref,generic) values ";
				$this->sSQLString .= "(".$this->ggroupid.",";
				$this->sSQLString .= $this->gcatid.",'";
				$this->sSQLString .= $this->gitem."','";
				$this->sSQLString .= $this->gitemcode."','";
				$this->sSQLString .= $this->barno."','";
				$this->sSQLString .= $this->unit."',";
				$this->sSQLString .= $this->gnoinunit.",";
				$this->sSQLString .= $sa.",";
				$this->sSQLString .= $ss.",";
				$this->sSQLString .= $pa.",";
				$this->sSQLString .= $ps.",";
				$this->sSQLString .= $this->gsetsell.",'";
				$this->sSQLString .= $this->deftax."','";
				$this->sSQLString .= $this->blocked."','";
				$this->sSQLString .= $this->trackserial."','";
				$this->sSQLString .= $this->trackstock."',";
				$this->sSQLString .= $this->gsupplier.",";
				$this->sSQLString .= $this->uid.",";
				$this->sSQLString .= "'Y')";
			
				$this->updt();
				$newgstkitemid = $this->newaccountno;
				
				$this->sSQLString = "update stkmast set xref = ".$newgstkitemid." where itemid = ".$this->uid;
				$this->updt();
			}
		}
	
	} //edititem()


//**************************************************************
	function DelItem()
//**************************************************************
	{
	
	$this->sSQLString = "delete from stkmast where itemid = ".$this->uid;

	$this->updt();
			
	} // delItem()


//**************************************************************
	function AddLoc()
//**************************************************************
	{

		$this->sSQLString = "insert into locations (location,locad1,locad2,locad3) values ";
		$this->sSQLString .= "('".$this->location."','";
		$this->sSQLString .= $this->locad1."','";
		$this->sSQLString .= $this->locad2."','";
		$this->sSQLString .= $this->locad3."')";
	
		$this->updt();

	} //addloc()

//**************************************************************
	function EditLoc()
//**************************************************************
	{
	
		$this->sSQLString = "update locations set ";
		$this->sSQLString .= "location = '".$this->location."', ";
		$this->sSQLString .= "locad1 = '".$this->locad1."', ";
		$this->sSQLString .= "locad2 = '".$this->locad2."', ";
		$this->sSQLString .= "locad3 = '".$this->locad3."'";
		$this->sSQLString .= " where locid = ".$this->uid;

		$this->updt();
	
	} //editGroup()

//**************************************************************
	function AddDepot()
//**************************************************************
	{

		$this->sSQLString = "insert into depots (depot,contact,sad1,sad2,stown,spostcode,scountry,pad1,pad2,ptown,ppostcode,pcountry,phone,mobile,email,sub_id) values ";
		$this->sSQLString .= "('".$this->depot."',";
		$this->sSQLString .= "'".$this->contact."',";
		$this->sSQLString .= "'".$this->sad1."',";
		$this->sSQLString .= "'".$this->sad2."',";
		$this->sSQLString .= "'".$this->stown."',";
		$this->sSQLString .= "'".$this->spostcode."',";
		$this->sSQLString .= "'".$this->scountry."',";
		$this->sSQLString .= "'".$this->pad1."',";
		$this->sSQLString .= "'".$this->pad2."',";
		$this->sSQLString .= "'".$this->ptown."',";
		$this->sSQLString .= "'".$this->ppostcode."',";
		$this->sSQLString .= "'".$this->pcountry."',";
		$this->sSQLString .= "'".$this->phone."',";
		$this->sSQLString .= "'".$this->mobile."',";
		$this->sSQLString .= "'".$this->email."',";
		$this->sSQLString .= $this->sub_id.')';	
		
		$this->updt();
		//return $newuid;
		

	} //adddepot()


//**************************************************************
	function EditDepot()
//**************************************************************
	{
	
		$this->sSQLString = "update depots set ";
		$this->sSQLString .= 'depot = "'.$this->depot.'",';
		$this->sSQLString .= 'sad1 = "'.$this->sad1.'",';
		$this->sSQLString .= 'sad2 = "'.$this->sad2.'",';
		$this->sSQLString .= 'stown = "'.$this->stown.'",';
		$this->sSQLString .= 'spostcode = "'.$this->spostcode.'",';
		$this->sSQLString .= 'scountry = "'.$this->scountry.'",';
		$this->sSQLString .= 'pad1 = "'.$this->pad1.'",';
		$this->sSQLString .= 'pad2 = "'.$this->pad2.'",';
		$this->sSQLString .= 'ptown = "'.$this->ptown.'",';
		$this->sSQLString .= 'ppostcode = "'.$this->ppostcode.'",';
		$this->sSQLString .= 'pcountry = "'.$this->pcountry.'",';
		$this->sSQLString .= 'phone = "'.$this->phone.'",';
		$this->sSQLString .= 'mobile = "'.$this->mobile.'",';
		$this->sSQLString .= 'email = "'.$this->email.'",';
		$this->sSQLString .= 'contact = "'.$this->contact.'"';
		$this->sSQLString .= ' where depot_id = '.$this->uid;

		$this->updt();

	} //editdepot()

//**************************************************************
	function EditMedicine()
//**************************************************************
	{
	
		$this->sSQLString = "update requirements set ";
		$this->sSQLString .= 'medicineid = '.$this->medicine.',';
		$this->sSQLString .= 'dosage = "'.$this->dosage.'",';
		$this->sSQLString .= 'expdate = "'.$this->edate.'",';
		$this->sSQLString .= 'instructions = "'.$this->instructions.'",';
		$this->sSQLString .= 'qty = '.$this->required;
		$this->sSQLString .= ' where req_id = '.$this->uid;

		$this->updt();

	} //editmedicine()


//**************************************************************
	function AddClientType()
//**************************************************************
	{

		$this->sSQLString = "insert into client_types (client_type,sub_id) values ";
		$this->sSQLString .= "('".$this->clienttype."',";
		$this->sSQLString .= $this->sub_id.')';	
		
		$this->updt();
		return $newuid;
		

	} //addclienttype()

//*************************************************************
	function updt()
//*************************************************************
	{

	require_once("../db.php");
	$moduledb = $_SESSION['s_findb'];

	mysql_select_db($moduledb) or die(mysql_error());
	
	$result = mysql_query($this->sSQLString) or die(mysql_error().' - '.$this->sSQLString);
	
	$this->newaccountno = mysql_insert_id();


	//echo $this->sSQLString;


	}// updt()


} //class

?>