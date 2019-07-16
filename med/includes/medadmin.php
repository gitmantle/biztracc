<?php

class medadmin
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
	public $db;

	// properties for medicines
	public $medicine;
	public $required;
	public $dosage;
	public $instructions;
	public $edate;
	
	// properties for depots
	public $depot;
	public $contact;
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
	public $email;

	// private for internal use only
	private $sSQLString;
	
//*************************************************************
	function updt()
//*************************************************************
	{
	
	if ($this->onlineapp == 'N') {
		require("../db.php");
	} else {
		require("db1.php");
	}
	$moduledb = $this->db;
	mysql_select_db($moduledb) or die(mysql_error());
	
	$result = mysql_query($this->sSQLString) or die(mysql_error().' '.$this->sSQLString);
	
	$this->newid = mysql_insert_id();

	//return 'SQL is '.$this->sSQLString;
	
	echo $this->sSQLString;
	
	} //updt()

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
	function AddMedicine()
//**************************************************************
	{

		$this->sSQLString = "insert into requirements (patientid,medicineid,dosage,expdate,instructions,qty,sub_id) values ";
		$this->sSQLString .= "(".$this->client_id.",";
		$this->sSQLString .= $this->medicine.",";
		$this->sSQLString .= "'".$this->dosage."',";
		$this->sSQLString .= "'".$this->edate."',";
		$this->sSQLString .= "'".$this->instructions."',";
		$this->sSQLString .= $this->required.",";
		$this->sSQLString .= $this->sub_id.')';	
		
		$this->updt();
		//return $newuid;
		

	} //addmedicine()


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



} //class

?>
