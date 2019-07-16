<?php

class subadmin
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
	public $dir_level = 1;
	public $coy_id = 0;
	public $module;
	public $userid;

	// properties for adding/editing staff/contacts
	public $fname;
	public $lname;
	public $email;
	public $username = 'N';
	public $password = 'N';
	public $menugroup = 0;
	public $role_id = 0;
	public $phone;
	public $mobile;
	public $guser;
	public $gpwd;
	public $admin = "N";
	public $coyowner = "N";
	
	// private for internal use only
	private $sSQLString;
	


//*************************************************************
	function updt()
//*************************************************************
	{
	
	if ($this->dir_level == 1) {
		require("../db.php");
	}
	if ($this->dir_level == 2) {
		require("../../db.php");
	}
	$dbs = $_SESSION['s_admindb'];
	mysql_select_db($dbs) or die(mysql_error());	
	mysql_query("SET NAMES utf8");
	$result = mysql_query($this->sSQLString) or die(mysql_error().' '.$this->sSQLString);
	
	$this->newid = mysql_insert_id();

	//return 'SQL is '.$this->sSQLString;
	
	} //updt()

//**************************************************************
	function AddStaff()
//**************************************************************
	{

		$this->sSQLString = "insert into users (ufname,ulname,coyowner,uemail,uphone,umobile,uadmin,ug_user,ug_pwd,username,upwd,sub_id) values ";
		$this->sSQLString .= "('".mysql_real_escape_string($this->fname)."','";
		$this->sSQLString .= mysql_real_escape_string($this->lname)."','";
		$this->sSQLString .= $this->coyowner."','";
		$this->sSQLString .= $this->email."','";
		$this->sSQLString .= $this->phone."','";
		$this->sSQLString .= $this->mobile."','";
		$this->sSQLString .= $this->admin."','";
		$this->sSQLString .= $this->guser."','";
		$this->sSQLString .= $this->gpwd."','";
		$this->sSQLString .= md5($this->username)."','";
		$this->sSQLString .= md5($this->password)."',";
		$this->sSQLString .= $this->sub_id.")";	
		
		$this->updt();
		
		$this->userid = $this->newid;
		
		if($this->module == 'fin') {
			$this->sSQLString = "insert into access (staff_id,module,usergroup,subid,coyid) values ";
			$this->sSQLString .= "('".$this->userid."','";
			$this->sSQLString .= "clt',";
			$this->sSQLString .= $this->menugroup.",";
			$this->sSQLString .= $this->sub_id.",";
			$this->sSQLString .= $this->coy_id.")";
			
			$this->updt();
			
			$this->sSQLString = "insert into access (staff_id,module,usergroup,subid,coyid) values ";
			$this->sSQLString .= "('".$this->userid."','";
			$this->sSQLString .= "fin',";
			$this->sSQLString .= $this->menugroup.",";
			$this->sSQLString .= $this->sub_id.",";
			$this->sSQLString .= $this->coy_id.")";
			
			$this->updt();
			
		} else {
			
			$this->sSQLString = "insert into access (staff_id,module,usergroup) values ";
			$this->sSQLString .= "('".$this->userid."','";
			$this->sSQLString .= "clt',";
			$this->sSQLString .= $this->menugroup.")";
			
			$this->updt();
			
		}

	} //addstaff()
	
//**************************************************************
	function EditStaff()
//**************************************************************
	{

		$this->sSQLString = "update users set ";
		$this->sSQLString .= "ufname = '".$this->fname."',";
		if ($this->username != 'N') {
			$this->sSQLString .= "username = '".md5($this->username)."',";
		}
		if ($this->password != 'N') {
			$this->sSQLString .= "upwd = '".md5($this->password)."',";
		}
		$this->sSQLString .= "uemail = '".$this->email."',";
		$this->sSQLString .= "uphone = '".$this->phone1."',";
		$this->sSQLString .= "umobile = '".$this->phone2."',";
		$this->sSQLString .= "uadmin = '".$this->admin."',";
		$this->sSQLString .= "ug_user = '".$this->guser."',";
		$this->sSQLString .= "ug_pwd = '".$this->gpwd."',";
		$this->sSQLString .= "ulname = '".mysql_real_escape_string($this->lname)."'";
		$this->sSQLString .= " where uid = ".$this->uid;
		
		$this->updt();
		
		$this->sSQLString = "update access set ";
		$this->sSQLString .= "usergroup = ".$this->menugroup;
		$this->sSQLString .= " where staff_id = ".$this->uid." and module = 'clt'";
		
		$this->updt();
		
	} //editstaff()
		
//**************************************************************
	function EditPassword()
//**************************************************************
	{
	
		$this->sSQLString = "update staff set ";
		$this->sSQLString .= "password = '".md5($this->password)."'";
		$this->sSQLString .= " where staff_id = ".$this->uid;
		
		$this->updt();
		
	} //editpassword()
		




} //class

?>