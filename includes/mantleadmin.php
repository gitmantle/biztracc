<?php

class mantleadmin
{

	// general properties
	public $uid;
	public $retmsg;
	public $sub_id = 0;
	public $newrecord;

	// public properties for add/edit owners
	public $owner;
	
	// public properties for add/edit providers
	public $provider;

	// public properties for add/edit workflow stages
	public $workflow;
	public $wforder;
	public $aide;
	
	// public properties for add/editclient types stages
	public $clienttype;

	// public properties for add/edit referred
	public $referred;

	// public properties for add/edit address types
	public $address_type;
	
	// public properties for add/edit comm types
	public $comm_type;

	// public properties for add/edit databases
	public $dbs;
	public $dbref;

	// public properties for add/edit owners to database
	public $pcent;

	// public properties for NZ post codes
	public $postoffice;
	public $city;
	public $postcode;
	public $rd;
	public $street;
	public $suburb;
	public $area;

	// public properties for links
	public $lnk;
	public $description;
	
	// public properties for outsource providers
	public $phone;
	public $address;
	public $email;
	public $web;
	
	// public properties for non-member email recipients
	public $recipient;

	// public properties for add/edit industries
	public $industry;

	// private for internal use only
	private $sSQLString;
	

//*************************************************************
	function updt()
//*************************************************************
	{

	require("../db.php");
	$dbase = $_SESSION['s_dbase'];
	mysql_select_db($dbase) or die(mysql_error());	
	$result = mysql_query($this->sSQLString) or die(mysql_error().' '.$this->sSQLString);
	
	$this->newrecord = mysql_insert_id();
	
	//echo 'SQL is '.$this->sSQLString;
	
	} //updt()

//**************************************************************
	function AddUser()
//**************************************************************
	{

		$this->sSQLString = "insert into staff (firstname,lastname,username,password,new_seclev) values ";
		$this->sSQLString .= "('".$this->fname."','";
		$this->sSQLString .= mysql_real_escape_string ($this->lname)."','";
		$this->sSQLString .= $this->username."','";
		$this->sSQLString .= md5($this->password)."',";
		$this->sSQLString .= $this->menugroup.")";	
		
		$this->updt();
		

	} //adduser()
	
//**************************************************************
	function EditUser()
//**************************************************************
	{
	
		$this->sSQLString = "update staff set ";
		$this->sSQLString .= "firstname = '".$this->fname."',";
		$this->sSQLString .= "lastname = '".mysql_real_escape_string ($this->lname)."',";
		$this->sSQLString .= "username = '".$this->username."',";
		$this->sSQLString .= "password = '".md5($this->password)."',";
		$this->sSQLString .= "new_seclev = ".$this->menugroup;
		$this->sSQLString .= " where uid = ".$this->uid;

		$this->updt();

	} //edituser()


//**************************************************************
	function AddOwner()
//**************************************************************
	{

		$this->sSQLString = "insert into owners (owner,sub_id) values ";
		$this->sSQLString .= "('".mysql_real_escape_string ($this->owner)."',";	
		$this->sSQLString .= $this->sub_id.")";	
		
		$this->updt();
		

	} //addowner()


//**************************************************************
	function EditOwner()
//**************************************************************
	{
	
		$this->sSQLString = "update owners set ";
		$this->sSQLString .= "owner = '".mysql_real_escape_string ($this->owner)."'";
		$this->sSQLString .= " where owner_id = ".$this->uid;

		$this->updt();
		
	} //editowner()

//**************************************************************
	function AddLink()
//**************************************************************
	{

		$this->sSQLString = "insert into links (link,description,sub_id) values ";
		$this->sSQLString .= "('".$this->lnk."','";	
		$this->sSQLString .= $this->description."',";	
		$this->sSQLString .= $this->sub_id.")";	
		
		$this->updt();
		

	} //addlink()


//**************************************************************
	function EditLink()
//**************************************************************
	{
	
		$this->sSQLString = "update links set ";
		$this->sSQLString .= "link = '".$this->lnk."',";
		$this->sSQLString .= "description = '".$this->description."'";
		$this->sSQLString .= " where link_id = ".$this->uid;

		$this->updt();
		
	} //editlink()

//**************************************************************
	function DelLink()
//**************************************************************
	{
		$this->sSQLString = "delete from links where link_id = ".$this->uid;
		
		$this->updt();
	
	} //dellink()

//**************************************************************
	function AddRecipient()
//**************************************************************
	{

		$this->sSQLString = "insert into subemails (email,recipient,sub_id) values ";
		$this->sSQLString .= "('".$this->email."','";	
		$this->sSQLString .= $this->recipient."',";	
		$this->sSQLString .= $this->sub_id.")";	
		
		$this->updt();
		

	} //addrecipient()


//**************************************************************
	function EditRecipient()
//**************************************************************
	{
	
		$this->sSQLString = "update subemails set ";
		$this->sSQLString .= "email = '".$this->email."',";
		$this->sSQLString .= "recipient = '".$this->recipient."'";
		$this->sSQLString .= " where subemail_id = ".$this->uid;

		$this->updt();
		
	} //editrecipient()


//**************************************************************
	function DelDoc()
//**************************************************************
	{
		$this->sSQLString = "delete from documents where doc_id = ".$this->uid;
		
		$this->updt();
	
	} //deldoc()

//**************************************************************
	function DelCampDoc()
//**************************************************************
	{
		$this->sSQLString = "delete from campaign_docs where campdoc_id = ".$this->uid;
		
		$this->updt();
	
	} //deldoc()

//**************************************************************
	function AddProvider()
//**************************************************************
	{

		$this->sSQLString = "insert into providers (provider,sub_id) values ";
		$this->sSQLString .= "('".$this->provider."',";	
		$this->sSQLString .= $this->sub_id.")";	
		
		$this->updt();
		

	} //addprovider()


//**************************************************************
	function EditProvider()
//**************************************************************
	{
	
		$this->sSQLString = "update providers set ";
		$this->sSQLString .= "provider = '".$this->provider."'";
		$this->sSQLString .= " where provider_id = ".$this->uid;

		$this->updt();
		
	} //editprovider()

//**************************************************************
	function AddOutprov()
//**************************************************************
	{

		$this->sSQLString = "insert into outprovs (provider,phone,address,email,web,sub_id) values ";
		$this->sSQLString .= "('".$this->provider."',";	
		$this->sSQLString .= "'".$this->phone."',";				   
		$this->sSQLString .= "'".$this->address."',";				   
		$this->sSQLString .= "'".$this->email."',";				   
		$this->sSQLString .= "'".$this->web."',";				   
		$this->sSQLString .= $this->sub_id.")";	
		
		$this->updt();
		

	} //addoutprov()


//**************************************************************
	function EditOutprov()
//**************************************************************
	{
	
		$this->sSQLString = "update outprovs set ";
		$this->sSQLString .= "provider = '".$this->provider."',";
		$this->sSQLString .= "phone = '".$this->phone."',";
		$this->sSQLString .= "address = '".$this->address."',";
		$this->sSQLString .= "email = '".$this->email."',";
		$this->sSQLString .= "web = '".$this->web."'";
		$this->sSQLString .= " where outprov_id = ".$this->uid;

		$this->updt();
		
	} //editoutprov()

//**************************************************************
	function DelOutprov()
//**************************************************************
	{
		$this->sSQLString = "delete from outprovs where outprov_id = ".$this->uid;
		
		$this->updt();
	
	} //deloutprov()


//**************************************************************
	function AddWorkflow()
//**************************************************************
	{

		$this->sSQLString = "insert into workflow (process,porder,aide_memoir,sub_id) values ";
		$this->sSQLString .= '("'.$this->workflow.'",';	
		$this->sSQLString .= $this->wforder.',"';	
		$this->sSQLString .= $this->aide.'",';	
		$this->sSQLString .= $this->sub_id.')';	
		
		$this->updt();
		

	} //addworkflow()


//**************************************************************
	function EditWorkflow()
//**************************************************************
	{
	
		$this->sSQLString = 'update workflow set ';
		$this->sSQLString .= 'process = "'.$this->workflow.'",';
		$this->sSQLString .= 'porder = '.$this->wforder.',';
		$this->sSQLString .= 'aide_memoir = "'.$this->aide.'"';
		$this->sSQLString .= ' where process_id = '.$this->uid;

		$this->updt();
		
	} //editworkflow()
	

	
//**************************************************************
	function DelWorkflow()
//**************************************************************
	{
		$this->sSQLString = "delete from workflow where process_id = ".$this->uid;
		
		$this->updt();
	
	} //delworkflow()

//**************************************************************
	function AddClientType()
//**************************************************************
	{

		$this->sSQLString = "insert into client_types (client_type,sub_id) values ";
		$this->sSQLString .= "('".$this->clienttype."',";	
		$this->sSQLString .= $this->sub_id.")";	
		
		$this->updt();
		

	} //addclienttype()


//**************************************************************
	function EditClientType()
//**************************************************************
	{
	
		$this->sSQLString = "update client_types set ";
		$this->sSQLString .= "client_type = '".$this->clienttype."'";
		$this->sSQLString .= " where client_type_id = ".$this->uid;

		$this->updt();
		
	} //editclienttype()
	
//**************************************************************
	function DelClientType()
//**************************************************************
	{
		$this->sSQLString = "delete from client_types where client_type_id = ".$this->uid;
		
		$this->updt();
	
	} //delcleinttype()


//**************************************************************
	function AddReferred()
//**************************************************************
	{

		$this->sSQLString = "insert into referred (referred,sub_id) values ";
		$this->sSQLString .= "('".$this->referred."',";	
		$this->sSQLString .= $this->sub_id.")";	
		
		$this->updt();
		$newref = $this->newrecord;
		return $newref;
		

	} //addreferred()


//**************************************************************
	function AddIndustry()
//**************************************************************
	{

		$this->sSQLString = "insert into industries (industry,sub_id) values ";
		$this->sSQLString .= "('".$this->industry."',";	
		$this->sSQLString .= $this->sub_id.")";	
		
		$this->updt();
		

	} //addindustry()


//**************************************************************
	function EditIndustry()
//**************************************************************
	{
	
		$this->sSQLString = "update industries set ";
		$this->sSQLString .= "industry = '".$this->industry."'";
		$this->sSQLString .= " where industry_id = ".$this->uid;

		$this->updt();
		
	} //editindustry()
	



//**************************************************************
	function AddAddress_type()
//**************************************************************
	{

		$this->sSQLString = "insert into address_type (address_type) values ";
		$this->sSQLString .= "('".$this->address_type."')";	
		
		$this->updt();
		

	} //addaddress_type()


//**************************************************************
	function EditAddress_type()
//**************************************************************
	{
	
		$this->sSQLString = "update address_type set ";
		$this->sSQLString .= "address_type = '".$this->address_type."'";
		$this->sSQLString .= " where address_type_id = ".$this->uid;

		$this->updt();

	} //editaddress_type()

//**************************************************************
	function AddBox()
//**************************************************************
	{

		$this->sSQLString = "insert into boxes (post_office,city,postcode) values ";
		$this->sSQLString .= "('".mysql_real_escape_string ($this->postoffice)."','";
		$this->sSQLString .= mysql_real_escape_string ($this->city)."','";				   
		$this->sSQLString .= $this->postcode."')";	
		
		$this->updt();
		

	} //addBox()


//**************************************************************
	function EditBox()
//**************************************************************
	{
	
		$this->sSQLString = "update boxes set ";
		$this->sSQLString .= "post_office = '".mysql_real_escape_string ($this->postoffice)."',";
		$this->sSQLString .= "city = '".mysql_real_escape_string ($this->city)."',";
		$this->sSQLString .= "postcode = '".$this->postcode."'";
		$this->sSQLString .= " where box_id = ".$this->uid;

		$this->updt();

	} //editBox()

//**************************************************************
	function DelBox()
//**************************************************************
	{
		$this->sSQLString = "delete from boxes where box_id = ".$this->uid;
		
		$this->updt();
	
	} //delbox()

//**************************************************************
	function AddStreet()
//**************************************************************
	{

		$this->sSQLString = "insert into streets (street,suburb,area,postcode) values ";
		$this->sSQLString .= "('".mysql_real_escape_string ($this->street)."','";
		$this->sSQLString .= mysql_real_escape_string ($this->suburb)."','";
		$this->sSQLString .= mysql_real_escape_string ($this->area)."','";
		$this->sSQLString .= $this->postcode."')";	
		
		$this->updt();
		

	} //addStreet()


//**************************************************************
	function EditStreet()
//**************************************************************
	{
	
		$this->sSQLString = "update streets set ";
		$this->sSQLString .= "street = '".mysql_real_escape_string ($this->street)."',";
		$this->sSQLString .= "suburb = '".mysql_real_escape_string ($this->suburb)."',";
		$this->sSQLString .= "area = '".mysql_real_escape_string ($this->area)."',";
		$this->sSQLString .= "postcode = '".$this->postcode."'";
		$this->sSQLString .= " where street_id = ".$this->uid;

		$this->updt();

	} //editStreet()

//**************************************************************
	function DelStreet()
//**************************************************************
	{
		$this->sSQLString = "delete from streets where street_id = ".$this->uid;
		
		$this->updt();
	
	} //delStreet()


//**************************************************************
	function AddRural()
//**************************************************************
	{

		$this->sSQLString = "insert into rural (rd,town,postcode) values ";
		$this->sSQLString .= "('".$this->rd."','";
		$this->sSQLString .= mysql_real_escape_string ($this->city)."','";				   
		$this->sSQLString .= $this->postcode."')";	
		
		$this->updt();
		

	} //addRural()


//**************************************************************
	function EditRural()
//**************************************************************
	{
	
		$this->sSQLString = "update rural set ";
		$this->sSQLString .= "rd = '".$this->rd."',";
		$this->sSQLString .= "town = '".mysql_real_escape_string ($this->city)."',";
		$this->sSQLString .= "postcode = '".$this->postcode."'";
		$this->sSQLString .= " where rural_id = ".$this->uid;

		$this->updt();

	} //editRural()

//**************************************************************
	function DelRural()
//**************************************************************
	{
		$this->sSQLString = "delete from rural where rural_id = ".$this->uid;
		
		$this->updt();
	
	} //delrural()


//**************************************************************
	function AddComm_type()
//**************************************************************
	{

		$this->sSQLString = "insert into comm_types (comm_type) values ";
		$this->sSQLString .= "('".$this->comm_type."')";	
		
		$this->updt();
		

	} //addcomm_type()


//**************************************************************
	function EditComm_type()
//**************************************************************
	{
	
		$this->sSQLString = "update comm_types set ";
		$this->sSQLString .= "comm_type = '".$this->comm_type."'";
		$this->sSQLString .= " where commt_type_id = ".$this->uid;

		$this->updt();

	} //editclient_type()


//**************************************************************
	function AddDatabase()
//**************************************************************
	{

		$this->sSQLString = "insert into dbs (database_name,db_name,sub_id) values ";
		$this->sSQLString .= "('".$this->dbs."','";	
		$this->sSQLString .= $this->dbref."',";	
		$this->sSQLString .= $this->sub_id.")";	
		$this->updt();
		

	} //addrole()


//**************************************************************
	function EditDatabase()
//**************************************************************
	{
	
		$this->sSQLString = "update dbs set ";
		$this->sSQLString .= "database_name = '".$this->dbs."',";
		$this->sSQLString .= "db_name = '".$this->dbref."'";
		$this->sSQLString .= " where dbs_id = ".$this->uid;

		$this->updt();
		
	} //editrole()
	





	
// send email to other centres
//*****************************************************************
	function SendEmail()
//*****************************************************************
	{

// email to Hamilton or Hellensville

	require_once("../db.php");
	mysql_select_db('kiwidotcom') or die(mysql_error());	
	$query = "select * from courses where uid = ".$this->courseid;
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);


	if ($this->stcentre == 'Hamilton') {
		$to = "sharron@kiwidotcom.co.nz";
	}
	if ($this->stcentre == 'Hellensville') {
		$to = "pete@kiwidotcom.co.nz";
	}
	$subject = $this->stname;
	if ($this->status == 'Query') {
		$msg = ' is interested in ';
	} else {
		$msg = ' has enrolled for ';
	}
	$message = $this->stname.$msg.' '.$course.' starting on '.$this->startdate.' at '.$this->firsttime.'. '.'Contact details:-  '.$this->staddress.', '.$this->stphoneh.', '.$this->stphonew.', '.$this->stmobile.', '.$this->stemail.'  ';
	

	 require("phpMailer/class.phpmailer.php");  
	   
	 $mail = new PHPMailer();  
	   
	 $mail->IsSMTP();  // telling the class to use SMTP  
	 $mail->Host     = "70.87.187.178"; // SMTP server  
	   
	 $mail->From     = "office@kiwidotcom.co.nz"; 
	 $mail->AddAddress($to);  
	   
	 $mail->Subject  = $subject;  
	 $mail->Body     = $message;  
	 $mail->WordWrap = 50;  
	   
	 if(!$mail->Send()) {  
	   $this->retmsg = 'Message was not sent.'.' Mailer error: ' . $mail->ErrorInfo;  ;  
	 } else {  
	   $this->retmsg = 'Message has been sent to '.$to;  
	 }  
	return $this->retmsg;

	} // sendemail()




} //class

?>