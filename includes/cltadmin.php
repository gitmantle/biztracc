<?php

class cltadmin
{

	// general properties
	public $uid;
	public $client_id = 0;
	public $member_id = 0;
	public $company_id = 0;
	public $contact_id = 0;
	public $staff_id = 0;
	public $sub_id = 0;
	public $newid;

	// properties for adding/editing staff/contacts
	public $fname;
	public $lname;
	public $email;
	public $username = 'N';
	public $password = 'N';
	public $menugroup;
	public $role_id = 0;
	public $phone1;
	public $phone2;
	public $fax;
	public $web;
	public $guser;
	public $gpwd;
	
	// properties for adding/editing addresses
	public $loc;
	public $address_type_id = 0;
	public $street_no = '';
	public $ad1 = '';
	public $ad2 = '';
	public $suburb = '';
	public $town = '';
	public $state = '';
	public $postcode = '';
	public $country = '';
	public $preferredp = '';
	public $preferredv = '';
	public $billadd = '';
	public $deladd = '';
	
	// properties for adding/editing communications
	public $country_code;
	public $area_code;
	public $comm;
	public $comms_type_id = 0;
	public $ebilladd = '';
	
	// properties of adding/editing activities
	public $activity_status;
	public $ddate;
	public $ttime;
	public $activity;
	public $contact;
	
	// properties for adding/editing members
	public $firstname = '';
	public $lastname = '';
	public $preferredname = '';
	public $middlename = '';
	public $staff = '';
	public $title = '';
	public $dob = '0000-00-00';
	public $industry_id = 0;
	public $occupation = '';
	public $gender = '';
	public $position = '';
	public $smoker = '';
	public $relationship = '';
	public $age;
	public $checked;
	public $status = '';
	public $nextmeeting = '0000-00-00';
	public $priceband = 0;

	// properties for campaigns
	public $campname;
	public $campstart;
	public $campadvisor;
	public $campdescript;
	public $campgoal;
	public $campitem;
	public $campcost;
	public $outprovid;
	
	// properties for emails
	public $emdate;
	public $emtime;
	public $emfrom;
	public $emsubject;
	public $emmessage;
	
	// properties for referrals
	public $note;
	
	// properties for industries
	public $industry;
	
	// properties for complaint natures
	public $nature;
	
	// properties for client types
	public $clienttype;

	// properties for client status categories
	public $clientstatus;

	// properties for accounting categories
	public $acccat;
	public $acat;

	// properties referred by source
	public $source;

	// properties database object
	public $dbobj;

// private for internal use only
	private $sSQLString;
	



//**************************************************************
	function blankprefad()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("update ".$cltdb.".addresses set preferred = ' ' where member_id = ".$this->uid);
		$db_cad->execute();
		
		$db_cad->closeDB();
		
	} //blankprefad()

//**************************************************************
	function blankprefco()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];

		$db_cad->query("update ".$cltdb.".comms set preferred = ' ' where member_id = ".$this->uid);
		$db_cad->execute();
		
		$db_cad->closeDB();
		
	} //blankprefad()


//**************************************************************
	function AddAddress()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		if ($this->billadd == 'Y') {
			$sql = "update ".$cltdb.".addresses set ";
			$sql .= "billing = 'N'";
			$sql .= " where member_id = ".$this->client_id;
			$db_cad->query($sql);
			$db_cad->execute();
		}
			
			
			$db_cad->query("insert into ".$cltdb.".addresses (member_id,staff_id,location,address_type_id,street_no,ad1,ad2,suburb,town,postcode,preferredp,preferredv,country,billing,delivery) values (:member_id,:staff_id,:location,:address_type_id,:street_no,:ad1,:ad2,:suburb,:town,:postcode,:preferredp,:preferredv,:country,:billing,:delivery)");
		$db_cad->bind(':member_id', $this->client_id);
		$db_cad->bind(':staff_id', $this->staff_id);
		$db_cad->bind(':location', $this->loc);
		$db_cad->bind(':address_type_id', $this->address_type_id);
		$db_cad->bind(':street_no', $this->street_no);
		$db_cad->bind(':ad1', $this->ad1);
		$db_cad->bind(':ad2', $this->ad2);
		$db_cad->bind(':suburb', $this->suburb);
		$db_cad->bind(':town', $this->town);
		$db_cad->bind(':postcode', $this->postcode);
		$db_cad->bind(':preferredp', $this->preferredp);
		$db_cad->bind(':preferredv', $this->preferredv);
		$db_cad->bind(':country', $this->country);
		$db_cad->bind(':billing', $this->billadd);
		$db_cad->bind(':delivery', $this->deladd);
		
		$db_cad->execute();
		
		$newuid = $db_cad->lastInsertId();
		

		if ($this->billadd == 'Y') {
			$sql = "update ".$cltdb.".client_company_xref set ";
			$sql .= "sendstatement = 'Post',";
			$sql .= "billing = ".$newuid;
			$sql .= " where client_id = ".$this->client_id;
			$db_cad->query($sql);
			$db_cad->execute();
		}
		
		$db_cad->closeDB();
		
		return $newuid;
		

	} //addaddress()


//**************************************************************
	function EditAddress()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		if ($this->billadd == 'Y') {
			$sql = "update ".$cltdb.".addresses set ";
			$sql .= "billing = 'N'";
			$sql .= " where member_id = ".$this->client_id;
			$db_cad->query($sql);
			$db_cad->execute();
		}
			
			
		$db_cad->query("update ".$cltdb.".addresses set street_no = :street_no, ad1 = :ad1, ad2 = :ad2, suburb = :suburb, town = :town, postcode = :postcode, preferredp = :preferredp, preferredv = :preferredv, country = :country, location = :location, address_type_id = :address_type_id, billing = :billing, delivery = :delivery where address_id = :address_id");
		$db_cad->bind(':street_no', $this->street_no);
		$db_cad->bind(':ad1', $this->ad1);
		$db_cad->bind(':ad2', $this->ad2);
		$db_cad->bind(':suburb', $this->suburb);
		$db_cad->bind(':town', $this->town);
		$db_cad->bind(':postcode', $this->postcode);
		$db_cad->bind(':preferredp', $this->preferredp);
		$db_cad->bind(':preferredv', $this->preferredv);
		$db_cad->bind(':country', $this->country);
		$db_cad->bind(':location',$this->loc);
		$db_cad->bind(':address_type_id', $this->address_type_id);
		$db_cad->bind(':billing', $this->billadd);
		$db_cad->bind(':delivery', $this->deladd);
		$db_cad->bind(':address_id', $this->uid);
		
		$db_cad->execute();
		
		if ($this->billadd == 'Y') {
			$sql = "update ".$cltdb.".client_company_xref set ";
			$sql .= "sendstatement = 'Post',";
			$sql .= "billing = ".$this->uid;
			$sql .= " where client_id = ".$this->client_id;
			$db_cad->query($sql);
			$db_cad->execute();
		}
		
		$db_cad->closeDB();

	} //editaddress()
	
	
//**************************************************************
	function DelAddress()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		$db_cad->query("delete from ".$cltdb.".addresses where address_id = ".$this->uid);
		
		$db_cad->execute();
		
		$db_cad->closeDB();
	
	} //deladdress()	
	

//**************************************************************
	function AddComm()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		if ($this->preferred == 'Y') {
			$this->blankprefco();
		} else {
			$this->preferred = " ";	
		}

		$comm1 = str_replace(" ","",$this->comm);
		$comm2 = str_replace("-","",$comm1);
		$comm3 = str_replace(")","",$comm2);
		$comm4 = str_replace("(","",$comm3);
							   
		if ($this->ebilladd == 'Y') {
			$sql = "update ".$cltdb.".comms set ";
			$sql .= "billing = 'N'";
			$sql .= " where member_id = ".$this->client_id;
			$db_cad->query($sql);
			$db_cad->execute();
		}
			
		$db_cad->query("insert into ".$cltdb.".comms (member_id,staff_id,comms_type_id,country_code,area_code,preferred,comm,comm2,billing) values (:member_id,:staff_id,:comms_type_id,:country_code,:area_code,:preferred,:comm,:comm2,:billing)");
		$db_cad->bind(':member_id', $this->client_id); 
		$db_cad->bind(':staff_id', $this->staff_id); 
		$db_cad->bind(':comms_type_id', $this->comms_type_id); 
		$db_cad->bind(':country_code', $this->country_code); 
		$db_cad->bind(':area_code', $this->area_code); 
		$db_cad->bind(':preferred', $this->preferred); 
		$db_cad->bind(':comm', $this->comm); 
		$db_cad->bind(':comm2', $comm4); 
		$db_cad->bind(':billing', $this->ebilladd); 
		
		$db_cad->execute();
		
		$newuid = $db_cad->lastInsertId();
		
		if ($this->ebilladd == 'Y') {
			$sql = "update ".$cltdb.".client_company_xref set ";
			$sql .= "sendstatement = 'Email',";
			$sql .= "email = ".$this->newid;
			$sql .= " where client_id = ".$this->client_id;
			$db_cad->query($sql);
			$db_cad->execute();
		}

		return $newuid;
		
		$db_cad->closeDB();

	} //addcomm()


//**************************************************************
	function EditComm()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		if ($this->preferred == 'Y') {
			$this->blankprefco();
		} else {
			$this->preferred = " ";	
		}
		$comm1 = str_replace(" ","",$this->comm);
		$comm2 = str_replace("-","",$comm1);
		$comm3 = str_replace(")","",$comm2);
		$comm4 = str_replace("(","",$comm3);
	
		if ($this->ebilladd == 'Y') {
			$sql = "update ".$cltdb.".comms set ";
			$sql .= "billing = 'N'";
			$sql .= " where member_id = ".$this->client_id;
			$db_cad->query($sql);
			$db_cad->execute();
		}
			
		$db_cad->query("update ".$cltdb.".comms set country_code = :country_code, area_code = :area_code, comm = :comm, comm2 = :comm2, preferred = :preferred, comms_type_id = :comms_type_id, billing = :billing where comms_id = :comms_id");
		$db_cad->bind(':country_code', $this->country_code);
		$db_cad->bind(':area_code', $this->area_code);
		$db_cad->bind(':comm', $this->comm);
		$db_cad->bind(':comm2', $comm4);
		$db_cad->bind(':preferred', $this->preferred);
		$db_cad->bind(':comms_type_id', $this->comms_type_id);
		$db_cad->bind(':billing', $this->ebilladd);
		$db_cad->bind(':comms_id', $this->uid);

		$db_cad->execute();
		
		if ($this->ebilladd == 'Y') {
			$sql = "update ".$cltdb.".client_company_xref set ";
			$sql .= "sendstatement = 'Email',";
			$sql .= "email = ".$this->uid;
			$sql .= " where client_id = ".$this->client_id;
			$db_cad->query($sql);
			$db_cad->execute();
		}
		
		$db_cad->closeDB();

	} //editcomm()
	
	
//**************************************************************
	function DelComm()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("delete from ".$cltdb.".comms where comms_id = ".$this->uid);
		
		$db_cad->execute();

		$db_cad->closeDB();

	} //delcomm()	
	

//**************************************************************
	function DelEmail()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("delete from ".$cltdb.".emails where email_id = ".$this->uid);
		
		$db_cad->execute();

		$db_cad->closeDB();

	} //delemail()	

//**************************************************************
	function AddRecipient()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$sql = "insert into ".$cltdb.".subemails (email,recipient) values ";
		$sql .= "('".$this->email."','";	
		$sql .= $this->recipient."')";	
		$db_cad->query($sql);
		$db_cad->execute();

		$db_cad->closeDB();

	} //addrecipient()


//**************************************************************
	function EditRecipient()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$sql = "update ".$cltdb.".subemails set ";
		$sql .= "email = '".$this->email."',";
		$sql .= "recipient = '".$this->recipient."'";
		$sql .= " where subemail_id = ".$this->uid;
		$db_cad->query($sql);
		$db_cad->execute();

		$db_cad->closeDB();

	} //editrecipient()



//**************************************************************
	function AddActivity()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("insert into ".$cltdb.".activities (member_id,staff_id,status,ddate,ttime,contact,activity) values (:member_id,:staff_id,:status,:ddate,:ttime,:contact,:activity)");
		$db_cad->bind(':member_id', $this->client_id);
		$db_cad->bind(':staff_id', $this->staff_id);
		$db_cad->bind(':status', $this->status);
		$db_cad->bind(':ddate', $this->ddate);
		$db_cad->bind(':ttime', $this->ttime);
		$db_cad->bind(':contact', $this->contact);
		$db_cad->bind(':activity', $this->activity);
		
		$db_cad->execute();
		$newuid = $db_cad->lastInsertId();
		$db_cad->closeDB();
		
		return $newuid;

	} //addactivity()


//**************************************************************
	function EditActivity()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("update ".$cltdb.".activities set status = :status, ddate = :ddate, ttime = :ttime, contact = :contact, activity = :activity where activities_id = :activities_id");
		$db_cad->bind(':status', $this->activity_status);
		$db_cad->bind(':ddate', $this->ddate);
		$db_cad->bind(':ttime', $this->ttime);
		$db_cad->bind(':contact', $this->contact);
		$db_cad->bind(':activity', $this->activity);
		$db_cad->bind(':activities_id',$this->uid);

		$db_cad->execute();
		$db_cad->closeDB();

	} //editactivity()

//**************************************************************
	function DelActivity()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("delete from ".$cltdb.".activities where activities_id = ".$this->uid);
		
		$db_cad->execute();
		$db_cad->closeDB();
	
	} //delcomm()	
	
//**************************************************************
	function AddIndustry()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("insert into ".$cltdb.".industries (industry,sub_id) values (:industry,:sub_id)");
		$db_cad->bind(':industry', $this->industry);
		$db_cad->bind(':sub_id', $this->sub_id);	
		
		$db_cad->execute();
		$newuid = $db_cad->lastInsertId();
		$db_cad->closeDB();
		return $newuid;
		

	} //addindustry()


//**************************************************************
	function EditIndustry()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("update ".$cltdb.".industries set industry = :industry where industry_id = :industry_id");
		$db_cad->bind(':industry', $this->industry);
		$db_cad->bind(':industry_id', $this->uid);

		$db_cad->execute();
		$db_cad->closeDB();

	} //editindustry()

//**************************************************************
	function AddComplaintNature()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("insert into ".$cltdb.".complaint_nature (nature) values (:nature)");
		$db_cad->bind(':nature', $this->nature);
		
		$db_cad->execute();
		$newuid = $db_cad->lastInsertId();
		$db_cad->closeDB();
		return $newuid;
		

	} //addComplaintNature()


//**************************************************************
	function EditComplaintNature()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("update ".$cltdb.".complaint_nature set nature = :nature where uid = :nature_id");
		$db_cad->bind(':nature', $this->nature);
		$db_cad->bind(':nature_id', $this->uid);

		$db_cad->execute();
		$db_cad->closeDB();

	} //editComplaintNature()

//**************************************************************
	function AddClientType()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("insert into ".$cltdb.".client_types (client_type,sub_id) values (:client_type,:sub_id)");
		$db_cad->bind(':client_type', $this->clienttype);
		$db_cad->bind(':sub_id', $this->sub_id);	
		
		$db_cad->execute();
		$newuid = $db_cad->lastInsertId();
		$db_cad->closeDB();
		return $newuid;

	} //addclienttype()


//**************************************************************
	function EditClientType()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("update ".$cltdb.".client_types set client_type = :client_type where client_type_id = :client_type_id");
		$db_cad->bind(':client_type', $this->clienttype);
		$db_cad->bind(':client_type_id', $this->uid);

		$db_cad->execute();
		$db_cad->closeDB();

	} //editclienttype()

//**************************************************************
	function AddClientStatus()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("insert into ".$cltdb.".status (status) values (:status)");
		$db_cad->bind(':status', $this->clientstatus);
		
		$db_cad->execute();
		$newuid = $db_cad->lastInsertId();
		$db_cad->closeDB();
		return $newuid;

	} //addclientstatus()


//**************************************************************
	function EditClientStatus()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("update ".$cltdb.".status set status = :status where status_id = :status_id");
		$db_cad->bind(':status', $this->clientstatus);
		$db_cad->bind(':status_id', $this->uid);

		$db_cad->execute();
		$db_cad->closeDB();

	} //editclientstatus()


//**************************************************************
	function AddAccCat()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("insert into ".$cltdb.".acccats (acccat) values (:acccat)");
		$db_cad->bind(':acccat', $this->acccat);
		
		$db_cad->execute();
		$db_cad->closeDB();

	} //addacccat()


//**************************************************************
	function EditAccCat()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("update ".$cltdb.".acccats set acccat = :acccat where acccat_id = :acccat_id");
		$db_cad->bind(':acccat', $this->acccat);
		$db_cad->bind(':acccat_id', $this->uid);

		$db_cad->execute();
		$db_cad->closeDB();

	} //editacccat()

//**************************************************************
	function Addacat()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("insert into ".$cltdb.".acats (member_id,category) values (:member_id,:category)");
		$db_cad->bind(':member_id', $this->member_id);
		$db_cad->bind(':category', $this->acat);
		
		$db_cad->execute();
		$db_cad->closeDB();

	} //addacat()


//**************************************************************
	function AddWorkflow()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("insert into ".$cltdb.".workflow (process,porder,aide_memoir) values (:process,:porder,:aide_memoir)");
		$db_cad->bind(':process', $this->workflow);	
		$db_cad->bind(':porder', $this->wforder);	
		$db_cad->bind(':aide_memoir', $this->aide);	
		
		$db_cad->execute();
		$db_cad->closeDB();

	} //addworkflow()


//**************************************************************
	function EditWorkflow()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query('update '.$cltdb.'.workflow set process = :process, porder = :porder, aide_memoir = :aide_memoir where process_id = :process_id');
		$db_cad->bind(':process', $this->workflow);	
		$db_cad->bind(':porder', $this->wforder);	
		$db_cad->bind(':aide_memoir', $this->aide);	
		$db_cad->bind(':process_id', $this->uid);

		$db_cad->execute();
		$db_cad->closeDB();
		
	} //editworkflow()
	
//**************************************************************
	function DelWorkflow()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("delete from ".$cltdb.".workflow where process_id = ".$this->uid);
		
		$db_cad->execute();
		$db_cad->closeDB();
	
	} //delworkflow()

//**************************************************************
	function AddSource()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("insert into ".$cltdb.".referred (referred) values (:referred)");
		$db_cad->bind(':referred', $this->source);	
		
		$db_cad->execute();
		$db_cad->closeDB();

	} //addsource()


//**************************************************************
	function EditSource()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("update ".$cltdb.".referred set referred = :referred where referred_id = :referred_id");
		$db_cad->bind(':referred', $this->source);
		$db_cad->bind(':referred_id', $this->uid);

		$db_cad->execute();
		$db_cad->closeDB();
		
	} //editsource()

//**************************************************************
	function DelSource()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("delete from ".$cltdb.".referred where referred_id = ".$this->uid);
		
		$db_cad->execute();
		$db_cad->closeDB();
	
	} //delsource()



//**************************************************************
	function AddEmail()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];
		
		$db_cad->query("insert into emails (member_id,email_date,email_time,email_from,email_subject,email_message,staff_id,sub_id) values (:member_id,:email_date,:email_time,:email_from,:email_subject,:email_message,:staff_id,:sub_id)");
		$db_cad->bind(':member_id', $this->client_id);
		$db_cad->bind(':email_date', $this->emdate);
		$db_cad->bind(':email_time', $this->emtime);
		$db_cad->bind(':email_from', $this->emfrom);
		$db_cad->bind(':email_subject', $this->emsubject);
		$db_cad->bind(':email_message', $this->emmessage);
		$db_cad->bind(':staff_id', $this->staff_id);
		$db_cad->bind(':sub_id', $this->sub_id);	
		
		$db_cad->execute();
		$db_cad->closeDB();

	} //addemail()



//**************************************************************
	function AddMember()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];

		$db_cad->query("insert into ".$cltdb.".members (title,dob,age,firstname,middlename,lastname,preferredname,staff,industry_id,occupation,gender,position,status,sub_id) values (:title,:dob,:age,:firstname,:middlename,:lastname,:preferredname,:staff,:industry_id,:occupation,:gender,:position,:status,:sub_id)");
		$db_cad->bind(':title', $this->title);
		$db_cad->bind(':dob', $this->dob);
		$db_cad->bind(':age', $this->age);
		$db_cad->bind(':firstname', $this->firstname);
		$db_cad->bind(':middlename', $this->middlename);
		$db_cad->bind(':lastname', $this->lastname);
		$db_cad->bind(':preferredname', $this->preferredname);
		$db_cad->bind(':staff', $this->staff);
		$db_cad->bind(':industry_id', $this->industry_id);
		$db_cad->bind(':occupation', $this->occupation);
		$db_cad->bind(':gender', $this->gender);
		$db_cad->bind(':position', $this->position);
		$db_cad->bind(':status', $this->status);
		$db_cad->bind(':sub_id', $this->sub_id);
		
		$db_cad->execute();
		$newuid = $db_cad->lastInsertId();
		$db_cad->closeDB();
		return $newuid;
		
	} //addmember()
	
	
//**************************************************************
	function EditMember()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];

		$db_cad->query("update ".$cltdb.".members set firstname = :firstname, middlename = :middlename, lastname = :lastname, preferredname = :preferredname, staff = :staff, title = :title, dob = :dob, age = :age, gender = :gender, status = :status, next_meeting = :next_meeting, industry_id = :industry_id, occupation = :occupation, client_type = :client_type, position = :position, checked = :checked where member_id = :member_id");
		$db_cad->bind(':firstname', $this->firstname);
		$db_cad->bind(':middlename', $this->middlename);
		$db_cad->bind(':lastname', $this->lastname);
		$db_cad->bind(':preferredname', $this->preferredname);
		$db_cad->bind(':staff', $this->staff);
		$db_cad->bind(':title', $this->title);
		$db_cad->bind(':dob', $this->dob);
		$db_cad->bind(':age', $this->age);
		$db_cad->bind(':gender', $this->gender);
		$db_cad->bind(':status', $this->status);
		$db_cad->bind(':next_meeting',$this->nextmeeting);
		$db_cad->bind(':industry_id', $this->industry_id);
		$db_cad->bind(':occupation', $this->occupation);
		$db_cad->bind(':client_type', $this->clienttype);
		$db_cad->bind(':position', $this->position);
		$db_cad->bind(':checked', $this->checked);
		$db_cad->bind(':member_id', $this->uid);

		$db_cad->execute();
		$db_cad->closeDB();

	} //editclient()

//**************************************************************
	function DelMember()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];

		$db_cad->query("delete from ".$cltdb.".candidates where member_id = ".$this->uid);
		$db_cad->execute();
		$db_cad->query("delete from ".$cltdb.".activities where member_id = ".$this->uid);
		$db_cad->execute();
		$db_cad->query("delete from ".$cltdb.".comms where member_id = ".$this->uid);
		$db_cad->execute();
		$db_cad->query("delete from ".$cltdb.".addresses where member_id = ".$this->uid);
		$db_cad->execute();
		$db_cad->query("delete from ".$cltdb.".members where member_id = ".$this->uid);
		$db_cad->execute();
	
		$db_cad->closeDB();

} //delclient()	
	

//**************************************************************
	function AddCampaign()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];

		$db_cad->query("insert into ".$cltdb.".campaigns (name,startdate,staff,description,goals) values (:name,:startdate,:staff,:description,:goals)");
		$db_cad->bind(':name', $this->campname);
		$db_cad->bind(':startdate', $this->campstart);
		$db_cad->bind(':staff', $this->campadvisor);
		$db_cad->bind(':description', $this->campdescript);
		$db_cad->bind(':goals', $this->campgoal);
		
		$db_cad->execute();
		$db_cad->closeDB();

	} //addcampaign()

//**************************************************************
	function EditCampaign()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];

		$db_cad->query("update ".$cltdb.".campaigns set name = :name, startdate = :startdate, staff = :staff, description = :description, goals = :goals where campaign_id = :campaign_id");
		$db_cad->bind(':name', $this->campname);
		$db_cad->bind(':startdate',$this->campstart);
		$db_cad->bind(':staff', $this->campadvisor);
		$db_cad->bind(':description', $this->campdescript);
		$db_cad->bind(':goals', $this->campgoal);
		$db_cad->bind(':campaign_id', $this->uid);

		$db_cad->execute();
		$db_cad->closeDB();

	} //editcampaign()

//**************************************************************
	function DelCampaign()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];

		$db_cad->query("delete from ".$cltdb.".campaigns where campaign_id = ".$this->uid);
		
		$db_cad->execute();
		$db_cad->closeDB();
	
	} //delcampaign()	
	
//**************************************************************
	function AddCampCost()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];

		$db_cad->query("insert into ".$cltdb.".campaign_costs (item,cost,campaign_id,sub_id) values (:item,:cost,:campaign_id,:sub_id)");
		$db_cad->bind(':item', $this->campitem);
		$db_cad->bind(':cost', $this->campcost);
		$db_cad->bind(':campaign_id', $this->uid);
		$db_cad->bind(':sub_id', $this->sub_id);
		
		$db_cad->execute();
		$db_cad->closeDB();
	

	} //addcampcost()

//**************************************************************
	function EditCampCost()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];

		$db_cad->query("update ".$cltdb.".campaign_costs set item = :item, cost = :cost where costs_id = :costs_id");
		$db_cad->bind(':item', $this->campitem);
		$db_cad->bind(':cost', $this->campcost);
		$db_cad->bind(':costs_id', $this->uid);
		
		$db_cad->execute();
		$db_cad->closeDB();
		
	} //editcampcost()

//**************************************************************
	function DelCampCost()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];

		$db_cad->query("delete from ".$cltdb.".campaign_costs where costs_id = ".$this->uid);
		
		$db_cad->execute();
		$db_cad->closeDB();
	
	} //delcampcost()	

//**************************************************************
	function AddCand()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];

		$db_cad->query("insert into ".$cltdb.".members (firstname,lastname,sub_id) values (:firstname,:lastname,:sub_id)");
		$db_cad->bind(':firstname', $this->firstname);
		$db_cad->bind(':lastname', $this->lastname);
		$db_cad->bind(':sub_id', $this->sub_id);	
		
		$db_cad->execute();
		$newuid = $db_cad->lastInsertId();
		$db_cad->closeDB();
		return $newuid;
		
	} //addmember()

//**************************************************************
	function AddReferral()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];

		$db_cad->query("insert into ".$cltdb.".referrals (staff_id,title,firstname,middlename,lastname,preferred,gender,referred_id,commtype,country,area,comm,commtype2,country2,area2,comm2,location,addresstype,streetno,street,suburb,town,postcode,ddate,note,sub_id) values (:staff_id,:title,:firstname,:middlename,:lastname,:preferred,:gender,:referred_id,:commtype,:country,:area,:comm,:commtype2,:country2,:area2,:comm2,:location,:addresstype,:streetno,:street,:suburb,:town,:postcode,:ddate,:note,:sub_id)");
		$db_cad->bind(':staff_id', $this->staff_id);
		$db_cad->bind(':title', $this->title);
		$db_cad->bind(':firstname', $this->firstname);
		$db_cad->bind(':middlename', $this->middlename);
		$db_cad->bind(':lastname', $this->lastname);
		$db_cad->bind(':preferred', $this->preferredname);
		$db_cad->bind(':gender', $this->gender);
		$db_cad->bind(':referred_id', $this->referred_id);
		$db_cad->bind(':commtype', $this->comms_type_id);
		$db_cad->bind(':country', $this->country_code);
		$db_cad->bind(':area', $this->area_code);
		$db_cad->bind(':comm', $this->comm);
		$db_cad->bind(':commtype2', $this->comms_type_id2);
		$db_cad->bind(':country2', $this->country_code2);
		$db_cad->bind(':area2', $this->area_code2);
		$db_cad->bind(':comm2', $this->comm2);
		$db_cad->bind(':location', $this->loc);
		$db_cad->bind(':addresstype', $this->address_type_id);
		$db_cad->bind(':streetno', $this->street_no);
		$db_cad->bind(':street', $this->ad1);
		$db_cad->bind(':suburb', $this->suburb);
		$db_cad->bind(':town', $this->town);
		$db_cad->bind(':postcode', $this->postcode);
		$db_cad->bind(':ddate', $this->ddate);
		$db_cad->bind(':note', $this->note);
		$db_cad->bind(':sub_id', $this->sub_id);	
		
		$db_cad->execute();
		$newuid = $db_cad->lastInsertId();
		$db_cad->closeDB();
		return $newuid;
		
	} //addreferral()

//**************************************************************
	function EditReferral()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];

		$db_cad->query("update ".$cltdb.".referrals set title =:title, firstname = :firstname, middlename = :middlename, lastname = :lastname, preferred = :preferred, gender = :gender, commtype = :commtype, country = :country, area = :area, comm = :comm, commtype2 = :commtype2, country2 = :country2, area2 = :area2, comm2 = :comm2, location = :location, addresstype = :addresstype, streetno = :streetno, street = :street, suburb = :suburb, town = :town, postcode = :postcode, note = :note where referral_id = :referral_id");
		$db_cad->bind(':title', $this->title);
		$db_cad->bind(':firstname', $this->firstname);
		$db_cad->bind(':middlename', $this->middlename);
		$db_cad->bind(':lastname', $this->lastname);
		$db_cad->bind(':preferred', $this->preferredname);
		$db_cad->bind(':gender', $this->gender);
		$db_cad->bind(':commtype', $this->comms_type_id);
		$db_cad->bind(':country', $this->country_code);
		$db_cad->bind(':area', $this->area_code);
		$db_cad->bind(':comm', $this->comm);
		$db_cad->bind(':commtype2', $this->comms_type_id2);
		$db_cad->bind(':country2', $this->country_code2);
		$db_cad->bind(':area2', $this->area_code2);
		$db_cad->bind(':comm2', $this->comm2);
		$db_cad->bind(':location', $this->loc);
		$db_cad->bind(':addresstype', $this->address_type_id);
		$db_cad->bind(':streetno', $this->street_no);
		$db_cad->bind(':street', $this->ad1);
		$db_cad->bind(':suburb', $this->suburb);
		$db_cad->bind(':town', $this->town);
		$db_cad->bind(':postcode', $this->postcode);
		$db_cad->bind(':note', $this->note);
		$db_cad->bind(':referral_id', $this->referralid);
		
		$db_cad->execute();
		$db_cad->closeDB();
		
	} //editreferral()

//**************************************************************
	function DelReferral()
//**************************************************************
	{
		include_once("DBClass.php");
		$db_cad = new DBClass();

		$cltdb = $_SESSION['s_cltdb'];

		$db_cad->query("delete from ".$cltdb.".referrals where referral_id = ".$this->referralid);
		$db_cad->execute();
		
		$db_cad->query("delete from ".$cltdb.".referrals_phone where referral_id = ".$this->referralid);
		$db_cad->execute();
		$db_cad->closeDB();
	
	} //delreferral()	


} //class

?>