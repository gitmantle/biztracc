<?php
class accaddacc
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
	public $avgcost = 0;
	public $sellacc;
	public $purchacc;
	public $setsell;
	public $deftax;
	public $trackserial;
	public $trackstock;
	public $bom;
	public $batch;
	public $technotes;
	public $advertising;
	public $picture;
	
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
	function AddGL()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
		
		$db_acc->query("insert into ".$findb.".glmast (account,grp,accountno,branch,sub,ctrlacc,system,lastyear,recon,blocked,sc,paygst) values (:account,:grp,:accountno,:branch,:sub,:ctrlacc,:system,:lastyear,:recon,:blocked,:sc,:paygst)");
		$db_acc->bind(':account', $this->aname);
		$db_acc->bind(':grp', $this->grp);					   
		$db_acc->bind(':accountno', $this->aaccno);
		$db_acc->bind(':branch', $this->abranch);
		$db_acc->bind(':sub', $this->asub);
		$db_acc->bind(':ctrlacc', 'N');
		$db_acc->bind(':system', 'N');
		$db_acc->bind(':lastyear', $this->alastyear);
		$db_acc->bind(':recon', $this->arecon);
		$db_acc->bind(':blocked', $this->ablocked);
		$db_acc->bind(':sc', $this->asc);
		$db_acc->bind(':paygst', $this->apaygst);
	
		$db_acc->execute();
		
		if ($this->asub > 0) {
			$db_acc->query("update ".$findb.".glmast set blocked = 'Y' where accountno = ".$this->aaccno." and branch = '".$this->abranch."' and sub = 0");
			$db_acc->execute();
		}
		
		// if branch not 1000 and accountno in PL ( < 700) add to branch 1000
		if ($this->abranch != '1000' && $this->aaccno < 700) {
			$db_acc->query('select account from '.$findb.'.glmast where accountno = :accountno and branch = "1000" and sub = :sub');
			$db_acc->bind(':accountno', $this->aaccno);
			$db_acc->bind(':sub', $this->asub);
			$rows = $db_acc->resultset();
			$numrows = mysql_num_rows($r);
			if (count($rows) == 0) {
				$db_acc->query("insert into ".$findb.".glmast (account,grp,accountno,branch,sub,ctrlacc,system,lastyear,recon,blocked,sc,paygst) values (:account,:grp,:accountno,:branch,:sub,:ctrlacc,:system,:lastyear,:recon,:blocked,:sc,:paygst)");
				$db_acc->bind(':account', $this->aname);
				$db_acc->bind(':grp', $this->grp);					   
				$db_acc->bind(':accountno', $this->aaccno);
				$db_acc->bind(':branch', '1000');
				$db_acc->bind(':sub', $this->asub);
				$db_acc->bind(':ctrlacc', 'N');
				$db_acc->bind(':system', 'N');
				$db_acc->bind(':lastyear', $this->alastyear);
				$db_acc->bind(':recon', $this->arecon);
				$db_acc->bind(':blocked', $this->ablocked);
				$db_acc->bind(':sc', $this->asc);
				$db_acc->bind(':paygst', $this->apaygst);
			
				$db_acc->execute();
				
				if ($this->asub > 0) {
					$db_acc->query("update ".$findb.".glmast set blocked = 'Y' where accountno = ".$this->aaccno." and branch = '1000' and sub = 0");
					$db_acc->execute();
				}
			}
	
		}
		
		$db_acc->closeDB();
		
	} // addGL()


//**************************************************************
	function EditGL()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
	
		$db_acc->query("update ".$findb.".glmast set account = :account, lastyear = :lastyear, recon = :recon, blocked = :blocked, sc = :sc, ird = :ird, ird2 = :ird2, paygst = :paygst where uid = :uid");
		$db_acc->bind(':account', $this->aname);
		$db_acc->bind(':lastyear', $this->alastyear);
		$db_acc->bind(':recon', $this->arecon);
		$db_acc->bind(':blocked', $this->ablocked);
		$db_acc->bind(':sc', $this->asc);
		$db_acc->bind(':ird', $this->ird10a);
		$db_acc->bind(':ird2', $this->ird10a2);
		$db_acc->bind(':paygst', $this->apaygst);
		$db_acc->bind(':uid', $this->uid);
	
		$db_acc->execute();
		$db_acc->closeDB();
	
	} // editGL()

//**************************************************************
	function DelGL()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
	
		$db_acc->query("delete from ".$findb.".glmast where uid = ".$this->uid);
	
		$db_acc->execute();
		$db_acc->closeDB();
	
	} // delGL()

//*************************************************************
	function AddAsset()
//*************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
	
		$this->ddate = $this->abought;
		$this->formatdate();
		
		$db_acc->query("insert into ".$findb.".fixassets (hcode,branch,asset,cost,lastyrbv,totdep,blocked,way,rate,bought,notes) values (:hcode,:branch,:asset,:cost,:lastyrbv,:totdep,:blocked,:way,:rate,:bought,:notes)");
		$db_acc->bind(':hcode', $this->ahcode);
		$db_acc->bind(':branch', $this->abranch);
		$db_acc->bind(':asset', $this->aname);
		$db_acc->bind(':cost', $this->acost);
		$db_acc->bind(':lastyrbv', $this->alastyrbv);
		$db_acc->bind(':totdep', $this->atotdep);
		$db_acc->bind(':blocked', $this->ablocked);
		$db_acc->bind(':way', $this->away);
		$db_acc->bind(':rate', $this->arate);
		$db_acc->bind(':bought', $this->ddate);
		$db_acc->bind(':notes', $this->anotes);
	
		$db_acc->execute();
		$this->newaccountno = $db_acc->lastInsertId();
	
		switch ($this->ahcode) {
			case 'LD':
				$as = 10000000 + $this->newaccountno;
				break;
			case 'BL':
				$as = 11000000 + $this->newaccountno;
				break;
			case 'MV':
				$as = 12000000 + $this->newaccountno;
				break;
			case 'PE':
				$as = 13000000 + $this->newaccountno;
				break;
			case 'FF':
				$as = 14000000 + $this->newaccountno;
				break;
			case 'MS':
				$as = 15000000 + $this->newaccountno;
				break;
			case 'S1':
				$as = 16000000 + $this->newaccountno;
				break;
			case 'S2':
				$as = 17000000 + $this->newaccountno;
				break;
			case 'S3':
				$as = 18000000 + $this->newaccountno;
				break;
			case 'S4':
				$as = 19000000 + $this->newaccountno;
				break;
		}
	
		$db_acc->query("update ".$findb.".fixassets set accountno = ".$as." where uid = ".$this->newaccountno);
		
		$db_acc->execute();
		$db_acc->closeDB();

	} //addasset()

//*************************************************************
	function EditAsset()
//*************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
	
		$this->ddate = $this->abought;
		$this->formatdate();
		
		$db_acc->query("update ".$findb.".fixassets set hcode = :hcode, branch = :branch, asset = :asset, blocked = :blocked, way = :way, rate = :rate, bought = :bought, lastyrbv = :lastyrbv, notes = :notes where uid = :uid");
		$db_acc->bind(':hcode', $this->ahcode);
		$db_acc->bind(':branch', $this->abranch);
		$db_acc->bind(':asset', $this->aname);
		$db_acc->bind(':lastyrbv', $this->alastyrbv);
		$db_acc->bind(':blocked', $this->ablocked);
		$db_acc->bind(':way', $this->away);
		$db_acc->bind(':rate', $this->arate);
		$db_acc->bind(':bought', $this->ddate);
		$db_acc->bind(':notes', $this->anotes);
		$db_acc->bind(':uid', $this->uid);
	
		$db_acc->execute();
		$db_acc->closeDB();
	
	} //editasset()

//**************************************************************
	function DelAsset()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
	
		$db_acc->query("delete from ".$findb.".fixassets where uid = ".$this->uid);
	
		$db_acc->execute();
		$db_acc->closeDB();
	
	} // delAsset()



//*************************************************************
	function assetHeadings()
//*************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
	
		$db_acc->query("update ".$findb.".assetheadings set heading = :heading where uid = :uid");
		$db_acc->bind(':heading', $this->heading);
		$db_acc->bind(':uid', $this->uid);
		
		$db_acc->execute();
		$db_acc->closeDB();
	
	} // assetheadings()

//*************************************************************
	function EditNumbers()
//*************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];

		$db_acc->query("update ".$findb.".numbers set inv = :inv, c_s = :c_s, req = :req, grn = :grn, crn = :crn, ret = :ret, chq = :chq, adj = :adj, tsf = :tsf, dep = :dep, jnl = :jnl, oth = :oth, rec = :oth, rec = :rec, crd = :crd, r-t = :r_t, p_c = :p_c, ebi = :ebi, ebo = :ebo, pur = :pur, sal = :sal, c_n = :c_n, pay = :pay");
		$db_acc->bind(':inv', $this->inv);
		$db_acc->bind(':c_s', $this->c_s);
		$db_acc->bind(':req', $this->req);
		$db_acc->bind(':grn', $this->grn);
		$db_acc->bind(':crn', $this->crn);
		$db_acc->bind(':ret', $this->ret);
		$db_acc->bind(':chq', $this->chq);
		$db_acc->bind(':adj', $this->adj);
		$db_acc->bind(':tsf', $this->tsf);
		$db_acc->bind(':dep', $this->dep);
		$db_acc->bind(':jnl', $this->jnl);
		$db_acc->bind(':oth', $this->oth);
		$db_acc->bind(':rec', $this->rec);
		$db_acc->bind(':crd', $this->crd);	
		$db_acc->bind(':r_t', $this->ebk);
		$db_acc->bind(':p_c', $this->p_c);
		$db_acc->bind(':ebi', $this->ebi);
		$db_acc->bind(':ebo', $this->ebo);
		$db_acc->bind(':pur', $this->pur);
		$db_acc->bind(':sal', $this->sal);
		$db_acc->bind(':c_n', $this->c_n);	
		$db_acc->bind(':pay', $this->pay);
	
		$db_acc->execute();
		$db_acc->closeDB();

	} //editnumbers()

//**************************************************************
	function AddGroup()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];

		$db_acc->query("insert into ".$findb.".stkgroup (groupname) values (:groupname)");
		$db_acc->bind(':groupname', $this->groupname);
	
		$db_acc->execute();
		$db_acc->closeDB();

	} //addgroup()

//**************************************************************
	function EditGroup()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];

		$db_acc->query("update ".$findb.".stkgroup set groupname = :groupname where groupid = :groupid");
		$db_acc->bind(':groupname', $this->groupname);
		$db_acc->bind(':groupid', $this->uid);

		$db_acc->execute();
		$db_acc->closeDB();
	
	} //editGroup()

//**************************************************************
	function DelGroup()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];

		$db_acc->query("delete from ".$findb.".stkgroup where groupid = ".$this->uid);
	
		$db_acc->execute();
		$db_acc->closeDB();
			
	} // delGroup()

//**************************************************************
	function AddCategory()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];

		$db_acc->query("insert into ".$findb.".stkcategory (groupid,category) values (:groupid,:category)");
		$db_acc->bind(':groupid', $this->groupid);
		$db_acc->bind(':category', $this->category);
	
		$db_acc->execute();
		$db_acc->closeDB();

	} //addcategory()

//**************************************************************
	function EditCategory()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
		
		$db_acc->query("update ".$findb.".stkcategory set groupid = :groupid, category = :category where catid = :catid");
		$db_acc->bind(':groupid', $this->groupid);
		$db_acc->bind(':category', $this->category);
		$db_acc->bind(':catid', $this->uid);

		$db_acc->execute();
		$db_acc->closeDB();
	
	} //editcategory()

//**************************************************************
	function DelCategory()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
		
		$db_acc->query("delete from ".$findb.".stkcategory where catid = ".$this->uid);
	
		$db_acc->execute();
		$db_acc->closeDB();
			
	} // delCategory()

//**************************************************************
	function AddItem()
//**************************************************************
	{
		if ($this->sellacc != '') {
			$sas = explode('~',$this->sellacc);
			$sa = $sas[1];
			$ss = $sas[2];
		} else {
			$sa = 0;
			$ss = 0;
		}
		if ($this->purchacc != '') {
			$pas = explode('~',$this->purchacc);		
			$pa = $pas[1];
			$ps = $pas[2];
		} else {
			$pa = 0;
			$ps = 0;
		}
		
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
		
		$db_acc->query("insert into ".$findb.".stkmast (groupid,catid,item,itemcode,barno,unit,sellacc,sellsub,purchacc,purchsub,setsell,deftax,active,trackserial,stock,batch,notes,advertising) values (:groupid,:catid,:item,:itemcode,:barno,:unit,:sellacc,:sellsub,:purchacc,:purchsub,:setsell,:deftax,:active,:trackserial,:stock,:batch,:notes,:advertising)");
		$db_acc->bind(':groupid', $this->groupid);
		$db_acc->bind(':catid', $this->catid);
		$db_acc->bind(':item', $this->item);
		$db_acc->bind(':itemcode', $this->itemcode);
		$db_acc->bind(':barno', $this->barno);
		$db_acc->bind(':unit', $this->unit);
		$db_acc->bind(':sellacc', $sa);
		$db_acc->bind(':sellsub', $ss);
		$db_acc->bind(':purchacc', $pa);
		$db_acc->bind(':purchsub', $ps);
		$db_acc->bind(':setsell', $this->setsell);
		$db_acc->bind(':deftax', $this->deftax);
		$db_acc->bind(':active', $this->blocked);
		$db_acc->bind(':trackserial', $this->trackserial);
		$db_acc->bind(':stock', $this->trackstock);
		$db_acc->bind(':batch', $this->batch);
		$db_acc->bind(':notes', $this->technotes);
		$db_acc->bind(':advertising', $this->advertising);
	
		$db_acc->execute();
		$db_acc->closeDB();

	} //additem()

//**************************************************************
	function EditItem()
//**************************************************************
	{
		$sas = explode('~',$this->sellacc);
		$sa = $sas[1];
		$ss = $sas[2];
		$pas = explode('~',$this->purchacc);		
		$pa = $pas[1];
		$ps = $pas[2];
			
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
		
		$db_acc->query("update ".$findb.".stkmast set groupid = :groupid, catid = :catid, item = :item, itemcode = :itemcode, barno = :barno, unit = :unit, sellacc = :sellacc, sellsub = :sellsub, purchacc = :purchacc, purchsub = :purchsub, setsell = :setsell, deftax = :deftax, active = :active, stock = :stock, bom = :bom, trackserial = :trackserial, batch = :batch, notes = :notes, advertising = :advertising where itemid = :itemid");
		$db_acc->bind(':groupid', $this->groupid);
		$db_acc->bind(':catid', $this->catid);
		$db_acc->bind(':item', $this->item);
		$db_acc->bind(':itemcode', $this->itemcode);
		$db_acc->bind(':barno', $this->barno);
		$db_acc->bind(':unit', $this->unit);
		$db_acc->bind(':sellacc', $sa);
		$db_acc->bind(':sellsub', $ss);
		$db_acc->bind(':purchacc', $pa);
		$db_acc->bind(':purchsub', $ps);
		$db_acc->bind(':setsell', $this->setsell);
		$db_acc->bind(':deftax', $this->deftax);
		$db_acc->bind(':active', $this->blocked);
		$db_acc->bind(':trackserial', $this->trackserial);
		$db_acc->bind(':stock', $this->trackstock);
		$db_acc->bind(':bom', $this->bom);
		$db_acc->bind(':itemid', $this->uid);
		$db_acc->bind(':batch', $this->batch);
		$db_acc->bind(':notes', $this->technotes);
		$db_acc->bind(':advertising', $this->advertising);
	
		$db_acc->execute();
		$db_acc->closeDB();
	
	} //edititem()


//**************************************************************
	function DelItem()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
		
		$db_acc->query("delete from ".$findb.".stkmast where itemid = ".$this->uid);
	
		$db_acc->execute();
		$db_acc->closeDB();
			
	} // delItem()

//**************************************************************
	function AddBranch()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
		
		$db_acc->query("insert into ".$findb.".branch (branchname) values (:branchname)");
		$db_acc->bind(':branchname', $this->branchname);
	
		$db_acc->execute();
		$this->newaccountno = $db_acc->lastInsertId();
		
		$this->branchcode = str_pad($this->newaccountno,4,"0",STR_PAD_LEFT);
		
		$db_acc->query("update ".$findb.".branch set branch = '".$this->branchcode."' where uid = ".$this->newaccountno);
		$db_acc->execute();

		// get array of system accounts
		$db_acc->query("select account,accountno,sub,blocked from ".$findb.".glmast where branch = '1000' and system = 'Y'");
		$rows = $db_acc->resultset();
		foreach ($rows as $row) {
			extract($row);
			$mname = $account;
			$macno = $accountno;
			$msub = $sub;
			$mblocked = $blocked;
			$db_acc->query("insert into ".$findb.".glmast (account,accountno,branch,sub,blocked,system,ctrlacc) values (:account,:accountno,:branch,:sub,:blocked,:system,:ctrlacc)");
			$db_acc->bind(':account', $mname);
			$db_acc->bind(':accountno', $macno);
			$db_acc->bind(':branch', $this->branchcode);
			$db_acc->bind('sub', $msub);
			$db_acc->bind(':blocked', $mblocked);
			$db_acc->bind(':system', 'Y');
			$db_acc->bind(':ctrlacc', 'Y');
			
			$db_acc->execute();
		}
		
		$db_acc->closeDB();

	} //addbranch()

//**************************************************************
	function EditBranch()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
		
		$db_acc->query("update ".$findb.".branch set branchname = :branchname where uid = :uid");
		$db_acc->bind(':branchname', $this->branchname);
		$db_acc->bind(':uid', $this->uid);

		$db_acc->execute();
		$db_acc->closeDB();
	
	} //editbranch()

//**************************************************************
	function DelBranch()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
		
		$db_acc->query("delete from ".$findb.".branch where uid = ".$this->uid);
	
		$db_acc->execute();
		
		$db_acc->query("delete from ".$findb.".glmast where branch = '".$this->branchcode."'");
	
		$db_acc->execute();
		$db_acc->closeDB();
			
	} // delBranch()


//**************************************************************
	function AddLoc()
//**************************************************************
	{

		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
		
		$db_acc->query("insert into ".$findb.".locations (location,locad1,locad2,locad3) values (:location,:locad1,:locad2,:locad3)");
		$db_acc->bind(':location', $this->location);
		$db_acc->bind(':locad1', $this->locad1);
		$db_acc->bind(':locad2', $this->locad2);
		$db_acc->bind(':locad3', $this->locad3);
	
		$this->updt();

	} //addloc()

//**************************************************************
	function EditLoc()
//**************************************************************
	{
		include_once("../includes/DBClass.php");
		$db_acc = new DBClass();
		
		$findb = $_SESSION['s_findb'];
		
		$db_acc->query("update ".$findb.".locations set location = :location, locad1 = :locad1, locad2 = :locad2, locad3 = :locad3 where locid = :locid");
		$db_acc->bind(':location', $this->location);
		$db_acc->bind(':locad1', $this->locad1);
		$db_acc->bind(':locad2', $this->locad2);
		$db_acc->bind(':locad3', $this->locad3);
		$db_acc->bind(':locid', $this->uid);

		$db_acc->execute();
		$db_acc->closeDB();
	
	} //editGroup()

} //class

?>