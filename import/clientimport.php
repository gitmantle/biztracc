<?php

class clientimport
{


	// properties for adding client details
	public $xfirstname;
	public $xmiddlename;
	public $xlastname;
	public $xpreferredname;
	public $xdob;
	public $xtitle;
	public $xcategory;
	public $xpadvisor;
	public $xposition;
	public $xoccupation;
	public $xphonehomecountry1;
	public $xphonehomearea1;
	public $xphonehomenumber1;
	public $xphonehomecountry2;
	public $xphonehomearea2; 
	public $xphonehomenumber2;
	public $xmobilecountry1;
	public $xmobilecode1;
	public $xmobilenumber1; 
	public $xmobilecountry2;
	public $xmobilecode2;
	public $xmobilenumber2;
	public $xphoneworkcountry1;
	public $xphoneworkarea1;
	public $xphoneworknumber1;
	public $xphoneworkcountry2;
	public $xphoneworkarea2;
	public $xphoneworknumber2;
	public $xphoneworkcountry3;
	public $xphoneworkarea3;
	public $xphoneworknumber3;
	public $xphoneworkcountry4;
	public $xphoneworkarea4;
	public $xphoneworknumber4;
	public $xfaxworkcountry;
	public $xfaxworkarea;
	public $xfaxworknumber;
	public $xemail1;
	public $xemail2;
	public $xhomestreetno;
	public $xhomestreetname; 
	public $xhomestreetsuburb;
	public $xhomestreettown;
	public $xhomestreetcountry; 
	public $xhomestreetpostcode;
	public $xhomepono;
	public $xhomeposuburb;
	public $xhomepotown;
	public $xhomepopostcode;
	public $xhomepocountry;
	public $xworkstreetno;
	public $xworkstreetname; 
	public $xworkstreetsuburb;
	public $xworkstreettown;
	public $xworkstreetcountry;
	public $xworkstreetpostcode;
	public $xbillstreetno;
	public $xbillstreetname; 
	public $xbillstreetsuburb;
	public $xbillstreettown;
	public $xbillstreetcountry;
	public $xbillstreetpostcode;
	public $xnotes1;
	public $xnotes2;
	public $xsourceno;

	public $xa1firstname; 
	public $xa1lastname;
	public $xa1dob;
	public $xa1preferredname;
	
	public $xsid;
	public $xfile;
	public $xcoyid;
	public $xuserid;
	public $xdc;

	
	// private for internal use only
	private $sSQLString;
	
//**************************************************************
	function updateClients()
//**************************************************************


	{
		$uploadfile = $this->xfile;

		// get data from generic Members table - our Excel spreadsheet
		$query = 'Select * from '.$uploadfile;
		$result = mysql_query($query) or die (mysql_error().' '.$query);
		while ($row = mysql_fetch_array($result) or die(mysql_error().' '.$result)) {
			extract($row);
			
			$acell = 'x'.$this->xfirstname;
			$afirstname = ${$acell}; 
				
			$acell = 'x'.$this->xmiddlename;
			$amiddlename = ${$acell};
				
			$acell = 'x'.$this->xlastname;
			$alastname = ${$acell};
			
			$acell = 'x'.$this->xpreferredname;
			$apreferredname = ${$acell};

			$acell = 'x'.$this->xdob;
			$excel_date = ${$acell};
			
			if (substr($excel_date,2,1) == "/") {
				$dt = explode("/",$excel_date);
				$adob = $dt[2]."-".$dt[1]."-".$dt[0];
			} elseif (substr($excel_date,4,1) == "-") {
				$adob = $excel_date;
			} else {
				$adob = date("Y-m-d", strtotime("01/01/1900 + $excel_date days - 2 days"));
			}

			
			$acell = 'x'.$this->xtitle;
			$atitle = ${$acell};
			
			
			$acell = 'x'.$this->xcategory;
			$acategory = ${$acell};

			$acell = 'x'.$this->xpadvisor;
			$apadvisor = ${$acell};

			$acell = 'x'.$this->xposition;
			$aposition = ${$acell};

			$acell = 'x'.$this->xoccupation;
			$aoccupation = ${$acell};

			$acell = 'x'.$this->xphonehomecountry1;
			$aphonehomecountry1 = ${$acell};

			$acell = 'x'.$this->xphonehomearea1;
			$aphonehomearea1 = ${$acell};

			$acell = 'x'.$this->xphonehomenumber1;
			$aphonehomenumber1 = ${$acell};

			$acell = 'x'.$this->xphonehomecountry2;
			$aphonehomecountry2 = ${$acell};

			$acell = 'x'.$this->xphonehomearea2;
			$aphonehomearea2 = ${$acell};

			$acell = 'x'.$this->xphonehomenumber2;
			$aphonehomenumber2 = ${$acell};

			$acell = 'x'.$this->xmobilecountry1;
			$amobilecountry1 = ${$acell};

			$acell = 'x'.$this->xmobilecode1;
			$amobilecode1 = ${$acell};

			$acell = 'x'.$this->xmobilenumber1;
			$amobilenumber1 = ${$acell};

			$acell = 'x'.$this->xmobilecountry2;
			$amobilecountry2 = ${$acell};

			$acell = 'x'.$this->xmobilecode2;
			$amobilecode2 = ${$acell};

			$acell = 'x'.$this->xmobilenumber2;
			$amobilenumber2 = ${$acell};

			$acell = 'x'.$this->xphoneworkcountry1;
			$aphoneworkcountry1 = ${$acell};

			$acell = 'x'.$this->xphoneworkarea1;
			$aphoneworkarea1 = ${$acell};

			$acell = 'x'.$this->xphoneworknumber1;
			$aphoneworknumber1 = ${$acell};

			$acell = 'x'.$this->xphoneworkcountry2;
			$aphoneworkcountry2 = ${$acell};

			$acell = 'x'.$this->xphoneworkarea2;
			$aphoneworkarea2 = ${$acell};

			$acell = 'x'.$this->xphoneworknumber2;
			$aphoneworknumber2 = ${$acell};

			$acell = 'x'.$this->xphoneworkcountry3;
			$aphoneworkcountry3 = ${$acell};

			$acell = 'x'.$this->xphoneworkarea3;
			$aphoneworkarea3 = ${$acell};

			$acell = 'x'.$this->xphoneworknumber3;
			$aphoneworknumber3 = ${$acell};

			$acell = 'x'.$this->xphoneworkcountry4;
			$aphoneworkcountry4 = ${$acell};

			$acell = 'x'.$this->xphoneworkarea4;
			$aphoneworkarea4 = ${$acell};

			$acell = 'x'.$this->xphoneworknumber4;
			$aphoneworknumber4 = ${$acell};

			$acell = 'x'.$this->xfaxworkcountry;
			$afaxworkcountry = ${$acell};

			$acell = 'x'.$this->xfaxworkarea;
			$afaxworkarea = ${$acell};

			$acell = 'x'.$this->xfaxworknumber;
			$afaxworknumber = ${$acell};

			$acell = 'x'.$this->xemail1;
			$aemail1 = ${$acell};

			$acell = 'x'.$this->xemail2;
			$aemail2 = ${$acell};

			$acell = 'x'.$this->xhomestreetno;
			$ahomestreetno = ${$acell};

			$acell = 'x'.$this->xhomestreetname;
			$ahomestreetname = ${$acell};

			$acell = 'x'.$this->xhomestreetsuburb;
			$ahomestreetsuburb = ${$acell};

			$acell = 'x'.$this->xhomestreettown;
			$ahomestreettown = ${$acell};

			$acell = 'x'.$this->xhomestreetpostcode;
			$ahomestreetpostcode = ${$acell};

			$acell = 'x'.$this->xhomestreetcountry;
			$ahomestreetcountry = ${$acell};

			$acell = 'x'.$this->xhomepono;
			$ahomepono = ${$acell};

			$acell = 'x'.$this->xhomeposuburb;
			$ahomestreetsuburb = ${$acell};

			$acell = 'x'.$this->xhomepotown;
			$ahomestreettown = ${$acell};

			$acell = 'x'.$this->xhomepopostcode;
			$ahomestreetpostcode = ${$acell};

			$acell = 'x'.$this->xhomestreetcountry;
			$ahomestreetcountry = ${$acell};

			$acell = 'x'.$this->xworkstreetno;
			$aworkstreetno = ${$acell};

			$acell = 'x'.$this->xworkstreetname;
			$aworkstreetname = ${$acell};

			$acell = 'x'.$this->xworkstreetsuburb;
			$aworkstreetsuburb = ${$acell};

			$acell = 'x'.$this->xworkstreettown;
			$aworkstreettown = ${$acell};

			$acell = 'x'.$this->xworkstreetpostcode;
			$aworkstreetpostcode = ${$acell};

			$acell = 'x'.$this->xworkstreetcountry;
			$aworkstreetcountry = ${$acell};

			$acell = 'x'.$this->xbillstreetno;
			$abillstreetno = ${$acell};

			$acell = 'x'.$this->xbillstreetname;
			$abillstreetname = ${$acell};

			$acell = 'x'.$this->xbillstreetsuburb;
			$abillstreetsuburb = ${$acell};

			$acell = 'x'.$this->xbillstreettown;
			$abillstreettown = ${$acell};

			$acell = 'x'.$this->xbillstreetpostcode;
			$abillstreetpostcode = ${$acell};

			$acell = 'x'.$this->xbillstreetcountry;
			$abillstreetcountry = ${$acell};

			$acell = 'x'.$this->xnotes1;
			$anotes1 = ${$acell};

			$acell = 'x'.$this->xnotes2;
			$anotes2 = ${$acell};

			$acell = 'x'.$this->xsourceno;
			$asourceno = ${$acell};
			$asourceno = intval($asourceno);


			// Associates
			
			$acell = 'x'.$this->xa1firstname;
			$aa1firstname = ${$acell};
			
			$acell = 'x'.$this->xa1lastname;
			$aa1lastname = ${$acell};
			
			$acell = 'x'.$this->xa1dob;
			$aa1dob = ${$acell};
			
			$acell = 'x'.$this->xa1preferredname;
			$aa1preferredname = ${$acell};
			
			date_default_timezone_set($_SESSION['s_timezone']);
			$hdate = date("Y-m-d");
			$ttime = strftime("%H:%M", time());
	
			if (trim($alastname) != "") {
				if ($adob != "") {
					$q = 'select member_id as mid,firstname,lastname from members where firstname = "'.$afirstname.'" and lastname = "'.$alastname.'"  and dob = "'.$adob.'"';
				} else {
					$q = 'select member_id as mid,firstname,lastname from members where firstname = "'.$afirstname.'" and lastname = "'.$alastname.'"';
					$adob = '0000-00-00';
				}
				$r = mysql_query($q) or die (mysql_error().' '.$q);
				$memrows = mysql_num_rows($r);
				if ($memrows == 0) {

			
					$q = "insert into members (sub_id,alt_id) values (".$this->xsid.",".$asourceno.")";
					$r = mysql_query($q) or die(mysql_error().' '.$q);
					$memid = mysql_insert_id();
		
					// update members
					$qmu = "update members set ";
					$qmu .= 'firstname = "'.htmlentities($afirstname).'",';
					$qmu .= 'lastname = "'.htmlentities($alastname).'",';
					$qmu .= 'middlename = "'.htmlentities($amiddlename).'",';
					$qmu .= 'preferredname = "'.htmlentities($apreferredname).'",';
					$qmu .= 'dob = "'.$adob.'",';
					$qmu .= 'title = "'.$atitle.'",';
					$qmu .= 'status = "Client",';
					$qmu .= 'staff = "'.$apadvisor.'",';
					$qmu .= 'position = "'.$aposition.'",';
					$qmu .= 'occupation = "'.$aoccupation.'",';
					$qmu .= 'checked = "No"';
					$qmu .= ' where member_id = '.$memid;
					
					$rmu = mysql_query($qmu) or die(mysql_error().' '.$qmu);
				
					//*********************************************************************************************************************
					// Cross reference debtors and creditors
					//*********************************************************************************************************************
					
					if ($this->xdc == 'd') {					
						$dracno = 30000000 + $memid;
					} else {
						$cracno = 20000000 + $emid;
					}
										
					$qdc = "select lastname from members where member_id = ".$memid;
					$rdc = mysql_query($qdc) or die($qdc);
					$row = mysql_fetch_array($rdc);
					extract($row);
					
					if ($this->xdc == 'd') {					
						$SQLString = 'insert into client_company_xref (client_id,company_id,drno,sortcode,member) values ';
						$SQLString .= '('.$memid.',';
						$SQLString .= $this->xcoyid.',';
						$SQLString .= $dracno.',"';
						$SQLString .= htmlentities($lastname).$dracno.'-0","';
						$SQLString .= htmlentities($lastname).'")';
					} else {
						$SQLString = 'insert into client_company_xref (client_id,company_id,crno,sortcode,member) values ';
						$SQLString .= '('.$memid.',';
						$SQLString .= $this->xcoyid.',';
						$SQLString .= $cracno.',"';
						$SQLString .= htmlentities($lastname).$cracno.'-0","';
						$SQLString .= htmlentities($lastname).'")';
					}
					$res = mysql_query($SQLString) or die(mysql_error().' - '.$SQLString);					
				
					// update comms
					
					if ($aphonehomenumber1 != "") {
						$comm1 = str_replace(" ","",$aphonehomenumber1);
						$comm2 = str_replace("-","",$comm1);
						$comm3 = str_replace(")","",$comm2);
						$comm4 = str_replace("(","",$comm3);
						$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '1,';
						$q .= '"'.$aphonehomecountry1.'",';
						$q .= '"'.$aphonehomearea1.'",';
						$q .= '"'.$aphonehomenumber1.'",';
						$q .= '"'.$comm4.'",';
						$q .= $this->xsid.')';
						
						$r = mysql_query($q) or die(mysql_error().' '.$q);
					}
				
					if ($aphonehomenumber2 != "") {
						$comm1 = str_replace(" ","",$aphonehomenumber2);
						$comm2 = str_replace("-","",$comm1);
						$comm3 = str_replace(")","",$comm2);
						$comm4 = str_replace("(","",$comm3);
						$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '1,';
						$q .= '"'.$aphonehomecountry2.'",';
						$q .= '"'.$aphonehomearea2.'",';
						$q .= '"'.$aphonehomenumber2.'",';
						$q .= '"'.$comm4.'",';
						$q .= $this->xsid.')';
						
						$r = mysql_query($q) or die(mysql_error().' '.$q);
					}
				
					if ($amobilenumber1 != "") {
						$comm1 = str_replace(" ","",$amobilenumber1);
						$comm2 = str_replace("-","",$comm1);
						$comm3 = str_replace(")","",$comm2);
						$comm4 = str_replace("(","",$comm3);
						$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '3,';
						$q .= '"'.$amobilecountry1.'",';
						$q .= '"'.$amobilecode1.'",';
						$q .= '"'.$amobilenumber1.'",';
						$q .= '"'.$comm4.'",';
						$q .= $this->xsid.')';
						
						$r = mysql_query($q) or die(mysql_error().' '.$q);
					}
				
					if ($amobilenumber2 != "") {
						$comm1 = str_replace(" ","",$amobilenumber2);
						$comm2 = str_replace("-","",$comm1);
						$comm3 = str_replace(")","",$comm2);
						$comm4 = str_replace("(","",$comm3);
						$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '3,';
						$q .= '"'.$amobilecountry2.'",';
						$q .= '"'.$amobilecode2.'",';
						$q .= '"'.$amobilenumber2.'",';
						$q .= '"'.$comm4.'",';
						$q .= $this->xsid.')';
						
						$r = mysql_query($q) or die(mysql_error().' '.$q);
					}
		
					if ($aphoneworknumber1 != "") {
						$comm1 = str_replace(" ","",$aphoneworknumber1);
						$comm2 = str_replace("-","",$comm1);
						$comm3 = str_replace(")","",$comm2);
						$comm4 = str_replace("(","",$comm3);
						$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '2,';
						$q .= '"'.$aphoneworkcountry1.'",';
						$q .= '"'.$aphoneworkarea1.'",';
						$q .= '"'.$aphoneworknumber1.'",';
						$q .= '"'.$comm4.'",';
						$q .= $this->xsid.')';
						
						$r = mysql_query($q) or die(mysql_error().' '.$q);
					}
				
					if ($aphoneworknumber2 != "") {
						$comm1 = str_replace(" ","",$aphoneworknumber2);
						$comm2 = str_replace("-","",$comm1);
						$comm3 = str_replace(")","",$comm2);
						$comm4 = str_replace("(","",$comm3);
						$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '2,';
						$q .= '"'.$aphoneworkcountry2.'",';
						$q .= '"'.$aphoneworkarea2.'",';
						$q .= '"'.$aphoneworknumber2.'",';
						$q .= '"'.$comm4.'",';
						$q .= $this->xsid.')';
						
						$r = mysql_query($q) or die(mysql_error().' '.$q);
					}
		
					if ($aphoneworknumber3 != "") {
						$comm1 = str_replace(" ","",$aphoneworknumber3);
						$comm2 = str_replace("-","",$comm1);
						$comm3 = str_replace(")","",$comm2);
						$comm4 = str_replace("(","",$comm3);
						$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '2,';
						$q .= '"'.$aphoneworkcountry3.'",';
						$q .= '"'.$aphoneworkarea3.'",';
						$q .= '"'.$aphoneworknumber3.'",';
						$q .= '"'.$comm4.'",';
						$q .= $this->xsid.')';
						
						$r = mysql_query($q) or die(mysql_error().' '.$q);
					}
		
					if ($aphoneworknumber4 != "") {
						$comm1 = str_replace(" ","",$aphoneworknumber4);
						$comm2 = str_replace("-","",$comm1);
						$comm3 = str_replace(")","",$comm2);
						$comm4 = str_replace("(","",$comm3);
						$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '2,';
						$q .= '"'.$aphoneworkcountry4.'",';
						$q .= '"'.$aphoneworkarea4.'",';
						$q .= '"'.$aphoneworknumber4.'",';
						$q .= '"'.$comm4.'",';
						$q .= $this->xsid.')';
						
						$r = mysql_query($q) or die(mysql_error().' '.$q);
					}
		
					if ($afaxworknumber != "") {
						$comm1 = str_replace(" ","",$afaxworknumber);
						$comm2 = str_replace("-","",$comm1);
						$comm3 = str_replace(")","",$comm2);
						$comm4 = str_replace("(","",$comm3);
						$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '6,';
						$q .= '"'.$afaxworkcountry.'",';
						$q .= '"'.$afaxworkarea.'",';
						$q .= '"'.$afaxworknumber.'",';
						$q .= '"'.$comm4.'",';
						$q .= $this->xsid.')';
						
						$r = mysql_query($q) or die(mysql_error().' '.$q);
					}
		
					if ($aemail1 != "") {
						$q = "insert into comms (member_id,staff_id,comms_type_id,comm,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '4,';
						$q .= '"'.$aemail1.'",';
						$q .= $this->xsid.')';
						
						$r = mysql_query($q) or die(mysql_error().' '.$q);
					}
				
					if ($aemail2 != "") {
						$q = "insert into comms (member_id,staff_id,comms_type_id,comm,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '4,';
						$q .= '"'.$aemail2.'",';
						$q .= $this->xsid.')';
						
						$r = mysql_query($q) or die(mysql_error().' '.$q);
					}
		
					// update addresses
					//Home Street address
					
					if ($ahomestreettown != "") {
						$q = "insert into addresses (member_id,staff_id,location,address_type_id,street_no,ad1,suburb,town,country,postcode,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '"Street",';
						$q .= '1,';
						$q .= '"'.$ahomestreetno.'",';
						$q .= '"'.htmlentities($ahomestreetname).'",';
						$q .= '"'.htmlentities($ahomestreetsuburb).'",';
						$q .= '"'.htmlentities($ahomestreettown).'",';
						$q .= '"'.htmlentities($ahomestreetcountry).'",';
						$q .= '"'.$ahomestreetpostcode.'",';
						$q .= $this->xsid.')';
				
						$r = mysql_query($q) or die(mysql_error().' '.$q);
						
					}
						
					//Home Post Box address
					
					if ($ahomepono != "") {
						$q = "insert into addresses (member_id,staff_id,location,address_type_id,street_no,suburb,town,country,postcode,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '"Post Box",';
						$q .= '1,';
						$q .= '"'.$ahomepono.'",';
						$q .= '"'.htmlentities($ahomeposuburb).'",';
						$q .= '"'.htmlentities($ahomepotown).'",';
						$q .= '"'.htmlentities($ahomepocountry).'",';
						$q .= '"'.$ahomepopostcode.'",';
						$q .= $this->xsid.')';
				
						$r = mysql_query($q) or die(mysql_error().' '.$q);
						
					}
						
					//Work Street address
					
					if ($aworkstreettown != "") {
						$q = "insert into addresses (member_id,staff_id,location,address_type_id,street_no,ad1,suburb,town,country,postcode,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '"Street",';
						$q .= '2,';
						$q .= '"'.$aworkstreetno.'",';
						$q .= '"'.htmlentities($aworkstreetname).'",';
						$q .= '"'.htmlentities($aworkstreetsuburb).'",';
						$q .= '"'.htmlentities($aworkstreettown).'",';
						$q .= '"'.htmlentities($aworkstreetcountry).'",';
						$q .= '"'.$aworkstreetpostcode.'",';
						$q .= $this->xsid.')';
				
						$r = mysql_query($q) or die(mysql_error().' '.$q);
						
					}
						
					//Billing address
					
					if ($abillstreettown != "") {
						$q = "insert into addresses (member_id,staff_id,location,address_type_id,street_no,ad1,suburb,town,country,postcode,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '"Street",';
						$q .= '4,';
						$q .= '"'.$abillstreetno.'",';
						$q .= '"'.htmlentities($abillstreetname).'",';
						$q .= '"'.htmlentities($abillstreetsuburb).'",';
						$q .= '"'.htmlentities($abillstreettown).'",';
						$q .= '"'.htmlentities($abillstreetcountry).'",';
						$q .= '"'.$abillstreetpostcode.'",';
						$q .= $this->xsid.')';
				
						$r = mysql_query($q) or die(mysql_error().' '.$q);
						
					}
					
					// Update notes
					
					if ($anotes1 != "") {
						$q = "insert into activities (member_id,staff_id,ddate,ttime,activity,status,contact,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '"'.$hdate.'",';
						$q .= '"'.$htime.'",';
						$q .= '"'.$anotes1.'",';
						$q .= '"Sealed",';
						$q .= '"Other",';
						$q .= $this->xsid.')';
				
						$r = mysql_query($q) or die(mysql_error().' '.$q);
						
					}
					
					if ($anotes2 != "") {
						$q = "insert into activities (member_id,staff_id,ddate,ttime,activity,status,contact,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '"'.$hdate.'",';
						$q .= '"'.$htime.'",';
						$q .= '"'.$anotes2.'",';
						$q .= '"Sealed",';
						$q .= '"Other",';
						$q .= $this->xsid.')';
				
						$r = mysql_query($q) or die(mysql_error().' '.$q);
						
					}
		
					//*********************************************************************************************************************
					// Associates
					//*********************************************************************************************************************
		
					if ($aa1lastname != "") {
				
						$qas = "insert into members (sub_id) values (".$this->xsid.")";
						$ras = mysql_query($qas) or die(mysql_error().' '.$qas);
						$amemid = mysql_insert_id();
					
						// update members
						$qmuas = "update members set ";
						$qmuas .= 'firstname = "'.htmlentities($aa1firstname).'",';
						$qmuas .= 'lastname = "'.htmlentities($aa1lastname).'",';
						$qmuas .= 'preferredname = "'.htmlentities($aa1preferredname).'",';
						$qmuas .= 'dob = "'.$aa1dob.'"';
						$qmuas .= ' where member_id = '.$amemid;
						
						$rmuas = mysql_query($qmuas) or die(mysql_error().' '.$qmuas);
						
						// update relationship
								
						$relationshipa = 'Employee';
						$relationshipm = 'Employer';
						
						$qras = 'insert into assoc_xref (sub_id,member_id,association,of_id) values ('.$this->xsid.','.$amemid.',"'.$relationshipa.'",'.$memid.')';
						$rras = mysql_query($qras) or die(mysql_error().' '.$qras);
						$qras = 'insert into assoc_xref (sub_id,member_id,association,of_id) values ('.$this->xsid.','.$memid.',"'.$relationshipm.'",'.$amemid.')';
						$rras = mysql_query($qras) or die(mysql_error().' '.$qras);
				
					}
					
				} // memrows = 0
			} // trim($alastname) != ""
		} // while
	} // function clientimport

} //class

?>