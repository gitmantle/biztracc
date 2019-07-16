<?php

class bankimport
{


	// properties for adding client details
	public $xdate;
	public $xtype;
	public $xchq;
	public $xdesc1;
	public $xdesc2;
	public $xamt;
	
	public $xsid;
	public $xfile;
	public $xuserid;

	
	// private for internal use only
	private $sSQLString;
	
//**************************************************************
	function updateBanksheet()
//**************************************************************
	{
		$uploadfile = $this->xfile;

		// get data from generic Members table - our Excel spreadsheet
		$query = 'Select * from '.$uploadfile;
		$result = mysql_query($query) or die (mysql_error().' '.$query);
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			
			$acell = 'x'.$this->xfirstname;
			$afirstname = ${$acell};
				
			$acell = 'x'.$this->xmiddlename;
			$amiddle = ${$acell};
				
			$acell = 'x'.$this->xlastname;
			$alastname = ${$acell};
			
			$acell = 'x'.$this->xpreferredname;
			$amiddle = ${$acell};

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

			$acell = 'x'.$this->xgender;
			$cellval = ${$acell};
			$cellval = strtoupper($cellval);
			switch ($cellval) {
				case 'F':
					$agender = 'Female';
					break;
				case 'FEMALE':
					$agender = 'Female';
					break;
				default:
					$agender = 'Male';
					break;
			}
			
			$acell = 'x'.$this->xtitle;
			$atitle = ${$acell};
			
			$acell = 'x'.$this->xsmoker;
			$cellval = ${$acell};
			$cellval = strtoupper($cellval);
			switch ($cellval) {
				case 'Y':
					$asmoker = 'Yes';
					break;
				case 'YES':
					$asmoker = 'Yes';
					break;
				default:
					$asmoker = 'No';
					break;
			}
			
			$acell = 'x'.$this->xmaritalstatus;
			$amaritalstatus = ${$acell};
			
			$acell = 'x'.$this->xcountryoforigin;
			$acountryoforigin = ${$acell};

			$acell = 'x'.$this->xreviewmonth;
			$areviewmonth = ${$acell};

			$acell = 'x'.$this->xcategory;
			$acategory = ${$acell};

			$acell = 'x'.$this->xownedby;
			$aownedby = ${$acell};

			$acell = 'x'.$this->xpadvisor;
			$apadvisor = ${$acell};

			$acell = 'x'.$this->xnextmeeting;
			$anextmeeting = ${$acell};

			$acell = 'x'.$this->xemployer;
			$aemployer = ${$acell};

			$acell = 'x'.$this->xcontract;
			$acontract = ${$acell};

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

			$acell = 'x'.$this->xnotes1;
			$anotes1 = ${$acell};

			$acell = 'x'.$this->xnotes2;
			$anotes2 = ${$acell};

			$acell = 'x'.$this->xsource;
			$asource = ${$acell};
			
			$acell = 'x'.$this->xsourceno;
			$asourceno = ${$acell};
			$asourceno = intval($asourceno);

			// Partner details

			$acell = 'x'.$this->xfirstnamep;
			$afirstnamep = ${$acell};
				
			$acell = 'x'.$this->xmiddlenamep;
			$amiddlenamep = ${$acell};
				
			$acell = 'x'.$this->xlastnamep;
			$alastnamep = ${$acell};
			
			$acell = 'x'.$this->xpreferrednamep;
			$apreferednamep = ${$acell};

			$acell = 'x'.$this->xdobp;
			$excel_date = ${$acell};
			$adobp = date("Y-m-d", strtotime("01/01/1900 + $excel_date days - 2 days"));
				
			$acell = 'x'.$this->xgenderp;
			$cellval = ${$acell};
			$cellval = strtoupper($cellval);
			switch ($cellval) {
				case 'F':
					$agenderp = 'Female';
					break;
				case 'FEMALE':
					$agenderp = 'Female';
					break;
				default:
					$agenderp = 'Male';
					break;
			}
			
			$acell = 'x'.$this->xtitlep;
			$atitlep = ${$acell};
			
			$acell = 'x'.$this->xsmokerp;
			$cellval = ${$acell};
			$cellval = strtoupper($cellval);
			switch ($cellval) {
				case 'Y':
					$asmokerp = 'Yes';
					break;
				case 'YES':
					$asmokerp = 'Yes';
					break;
				default:
					$asmokerp = 'No';
					break;
			}
			
			$acell = 'x'.$this->xmaritalstatusp;
			$amaritalstatusp = ${$acell};
			
			$acell = 'x'.$this->xcountryoforiginp;
			$acountryoforiginp = ${$acell};

			$acell = 'x'.$this->xreviewmonthp;
			$areviewmonthp = ${$acell};

			$acell = 'x'.$this->xcategoryp;
			$acategoryp = ${$acell};

			$acell = 'x'.$this->xownedbyp;
			$aownedbyp = ${$acell};

			$acell = 'x'.$this->xpadvisorp;
			$apadvisorp = ${$acell};

			$acell = 'x'.$this->xnextmeetingp;
			$anextmeetingp = ${$acell};

			$acell = 'x'.$this->xemployerp;
			$aemployerp = ${$acell};

			$acell = 'x'.$this->xcontractp;
			$acontractp = ${$acell};

			$acell = 'x'.$this->xpositionp;
			$apositionp = ${$acell};

			$acell = 'x'.$this->xoccupationp;
			$aoccupationp = ${$acell};

			$acell = 'x'.$this->xphonehomecountry1p;
			$aphonehomecountry1p = ${$acell};

			$acell = 'x'.$this->xphonehomearea1p;
			$aphonehomearea1p = ${$acell};

			$acell = 'x'.$this->xphonehomenumber1p;
			$aphonehomenumber1p = ${$acell};

			$acell = 'x'.$this->xphonehomecountry2p;
			$aphonehomecountry2p = ${$acell};

			$acell = 'x'.$this->xphonehomearea2p;
			$aphonehomearea2p = ${$acell};

			$acell = 'x'.$this->xphonehomenumber2p;
			$aphonehomenumber2p = ${$acell};

			$acell = 'x'.$this->xmobilecountry1p;
			$amobilecountry1p = ${$acell};

			$acell = 'x'.$this->xmobilecode1p;
			$amobilecode1p = ${$acell};

			$acell = 'x'.$this->xmobilenumber1p;
			$amobilenumber1p = ${$acell};

			$acell = 'x'.$this->xmobilecountry2p;
			$amobilecountry2p = ${$acell};

			$acell = 'x'.$this->xmobilecode2p;
			$amobilecode2p = ${$acell};

			$acell = 'x'.$this->xmobilenumber2p;
			$amobilenumber2p = ${$acell};

			$acell = 'x'.$this->xphoneworkcountry1p;
			$aphoneworkcountry1p = ${$acell};

			$acell = 'x'.$this->xphoneworkarea1p;
			$aphoneworkarea1p = ${$acell};

			$acell = 'x'.$this->xphoneworknumber1p;
			$aphoneworknumber1p = ${$acell};

			$acell = 'x'.$this->xphoneworkcountry2p;
			$aphoneworkcountry2p = ${$acell};

			$acell = 'x'.$this->xphoneworkarea2p;
			$aphoneworkarea2p = ${$acell};

			$acell = 'x'.$this->xphoneworknumber2p;
			$aphoneworknumber2p = ${$acell};

			$acell = 'x'.$this->xphoneworkcountry3p;
			$aphoneworkcountry3p = ${$acell};

			$acell = 'x'.$this->xphoneworkarea3p;
			$aphoneworkarea3p = ${$acell};

			$acell = 'x'.$this->xphoneworknumber3p;
			$aphoneworknumber3p = ${$acell};

			$acell = 'x'.$this->xphoneworkcountry4p;
			$aphoneworkcountry4p = ${$acell};

			$acell = 'x'.$this->xphoneworkarea4p;
			$aphoneworkarea4p = ${$acell};

			$acell = 'x'.$this->xphoneworknumber4p;
			$aphoneworknumber4p = ${$acell};

			$acell = 'x'.$this->xfaxworkcountryp;
			$afaxworkcountryp = ${$acell};

			$acell = 'x'.$this->xfaxworkareap;
			$afaxworkareap = ${$acell};

			$acell = 'x'.$this->xfaxworknumberp;
			$afaxworknumberp = ${$acell};

			$acell = 'x'.$this->xemail1p;
			$aemail1p = ${$acell};

			$acell = 'x'.$this->xemail2p;
			$aemail2p = ${$acell};

			$acell = 'x'.$this->xhomestreetnop;
			$ahomestreetnop = ${$acell};

			$acell = 'x'.$this->xhomestreetnamep;
			$ahomestreetnamep = ${$acell};

			$acell = 'x'.$this->xhomestreetsuburbp;
			$ahomestreetsuburbp = ${$acell};

			$acell = 'x'.$this->xhomestreettownp;
			$ahomestreettownp = ${$acell};

			$acell = 'x'.$this->xhomestreetpostcodep;
			$ahomestreetpostcodep = ${$acell};

			$acell = 'x'.$this->xhomestreetcountryp;
			$ahomestreetcountryp = ${$acell};

			$acell = 'x'.$this->xworkstreetnop;
			$aworkstreetnopp = ${$acell};

			$acell = 'x'.$this->xworkstreetname;
			$aworkstreetnamep = ${$acell};

			$acell = 'x'.$this->xworkstreetsuburbp;
			$aworkstreetsuburbp = ${$acell};

			$acell = 'x'.$this->xworkstreettownp;
			$aworkstreettownp = ${$acell};

			$acell = 'x'.$this->xworkstreetpostcodep;
			$aworkstreetpostcodep = ${$acell};

			$acell = 'x'.$this->xworkstreetcountryp;
			$aworkstreetcountryp = ${$acell};

			$acell = 'x'.$this->xnotes1p;
			$anotes1p = ${$acell};

			$acell = 'x'.$this->xnotes2p;
			$anotes2p = ${$acell};
			
			$acell = 'x'.$this->xsourcenop;
			$asourcenop = ${$acell};
			$asourcenop = intval($asourcenop);

			// Associates
			
			$acell = 'x'.$this->xa1firstname;
			$aa1firstname = ${$acell};
			
			$acell = 'x'.$this->xa1lastname;
			$aa1lastname = ${$acell};
			
			$acell = 'x'.$this->xa1dob;
			$aa1dob = ${$acell};
			
			$acell = 'x'.$this->xa1gender;
			$aa1gender = ${$acell};
			
			$acell = 'x'.$this->xa1relationship;
			$aa1relationship = ${$acell};
			
			$acell = 'x'.$this->xa1preferredname;
			$aa1preferredname = ${$acell};
			
			$acell = 'x'.$this->xa2firstname;
			$aa2firstname = ${$acell};
			
			$acell = 'x'.$this->xa2lastname;
			$aa2lastname = ${$acell};
			
			$acell = 'x'.$this->xa2dob;
			$aa2dob = ${$acell};
			
			$acell = 'x'.$this->xa2gender;
			$aa2gender = ${$acell};
			
			$acell = 'x'.$this->xa2relationship;
			$aa2relationship = ${$acell};
			
			$acell = 'x'.$this->xa2preferredname;
			$aa2preferredname = ${$acell};
			
			$acell = 'x'.$this->xa3firstname;
			$aa3firstname = ${$acell};
			
			$acell = 'x'.$this->xa3lastname;
			$aa3lastname = ${$acell};
			
			$acell = 'x'.$this->xa3dob;
			$aa3dob = ${$acell};
			
			$acell = 'x'.$this->xa3gender;
			$aa3gender = ${$acell};
			
			$acell = 'x'.$this->xa3relationship;
			$aa3relationship = ${$acell};
			
			$acell = 'x'.$this->xa3preferredname;
			$aa3preferredname = ${$acell};
			
			$acell = 'x'.$this->xa4firstname;
			$aa4firstname = ${$acell};
			
			$acell = 'x'.$this->xa4lastname;
			$aa4lastname = ${$acell};
			
			$acell = 'x'.$this->xa4dob;
			$aa4dob = ${$acell};
			
			$acell = 'x'.$this->xa4gender;
			$aa4gender = ${$acell};
			
			$acell = 'x'.$this->xa4relationship;
			$aa4relationship = ${$acell};
			
			$acell = 'x'.$this->xa4preferredname;
			$aa4preferredname = ${$acell};
			
			date_default_timezone_set($_SESSION['s_timezone']);
			$hdate = date("Y-m-d");
			$ttime = strftime("%H:%M", time());
	
			if (trim($alastname) != "") {
				if ($adob != "") {
					$q = 'select member_id as mid,firstname,lastname from members where firstname = "'.$afirstname.'" and lastname = "'.$alastname.'"  and dob = "'.$adob.'" and sub_id = '.$this->xsid;
				} else {
					$q = 'select member_id as mid,firstname,lastname from members where firstname = "'.$afirstname.'" and lastname = "'.$alastname.'"  and sub_id = '.$this->xsid;
					$adob = '0000-00-00';
				}
				$r = mysql_query($q) or die (mysql_error().' '.$q);
				$memrows = mysql_num_rows($r);
				if ($memrows == 0) {

					// check source and if necessary update referred
					$qs = 'select * from referred where referred = "'.$asource.'" and sub_id = '.$this->xsid;
					$rs = mysql_query($qs) or die(mysql_error().' '.$qs);
					$snumrows = mysql_num_rows($rs);
					if ($snumrows == 0) {
						$qsi = 'insert into referred (referred,sub_id) values ("'.$asource.'",'.$this->xsid.')';
						$rsi = mysql_query($qsi) or die(mysql_error().' '.$qsi);
						$referredid = mysql_insert_id();
					} else {
						$row = mysql_fetch_array($rs);
						extract($row);
						$referredid = $referred_id;
					}
				
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
					$qmu .= 'gender = "'.$agender.'",';
					$qmu .= 'title = "'.$atitle.'",';
					$qmu .= 'smoker = "'.$asmoker.'",';
					$qmu .= 'married = "'.$amaritalstatus.'",';
					$qmu .= 'referred_id = '.$referredid.',';
					$qmu .= 'review_month = "'.strtoupper($areviewmonth).'",';
					$qmu .= 'status = "Passive",';
					$qmu .= 'process_id = 27,';
					$qmu .= 'padvisor = "'.$apadvisor.'",';
					$qmu .= 'owned_by = "'.$aownedby.'",';
					$qmu .= 'next_meeting = "'.$anextmeeting.'",';
					$qmu .= 'employer = "'.$aemployer.'",';
					$qmu .= 'contract = "'.$acontract.'",';
					$qmu .= 'position = "'.$aposition.'",';
					$qmu .= 'occupation = "'.$aoccupation.'",';
					$qmu .= 'origin = "'.$acountryoforigin.'",';
					$qmu .= 'checked = "No"';
					$qmu .= ' where member_id = '.$memid;
					
					$rmu = mysql_query($qmu) or die(mysql_error().' '.$qmu);
				
				
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
					
					if ($ahomeponumber != "") {
						$q = "insert into addresses (member_id,staff_id,location,address_type_id,street_no,suburb,town,country,postcode,sub_id) values (";
						$q .= $memid.',';
						$q .= $this->xuserid.',';
						$q .= '"Post Box",';
						$q .= '1,';
						$q .= '"'.$ahomeponumber.'",';
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
			
					//******************************************************************************************	
					// Partner details	
					//******************************************************************************************
					if ($alastnamep != "") {
						
						$q = "insert into members (sub_id,alt_id) values (".$this->xsid.",".$asourcenop.")";
						$r = mysql_query($q) or die(mysql_error().' '.$q);
						$memidp = mysql_insert_id();
			
						// update members
						$qmu = "update members set ";
						$qmu .= 'firstname = "'.htmlentities($afirstnamep).'",';
						$qmu .= 'lastname = "'.htmlentities($alastnamep).'",';
						$qmu .= 'middlename = "'.htmlentities($amiddlenamep).'",';
						$qmu .= 'preferredname = "'.htmlentities($apreferrednamep).'",';
						$qmu .= 'dob = "'.$adobp.'",';
						$qmu .= 'gender = "'.$agenderp.'",';
						$qmu .= 'title = "'.$atitlep.'",';
						$qmu .= 'smoker = "'.$asmokerp.'",';
						$qmu .= 'married = "'.$amaritalstatusp.'",';
						$qmu .= 'referred_id = '.$referredid.',';
						$qmu .= 'review_month = "'.strtoupper($areviewmonthp).'",';
						$qmu .= 'status = "Passive",';
						$qmu .= 'process_id = 27,';
						$qmu .= 'padvisor = "'.$apadvisorp.'",';
						$qmu .= 'owned_by = "'.$aownedbyp.'",';
						$qmu .= 'next_meeting = "'.$anextmeetingp.'",';
						$qmu .= 'employer = "'.$aemployerp.'",';
						$qmu .= 'contract = "'.$acontractp.'",';
						$qmu .= 'position = "'.$apositionp.'",';
						$qmu .= 'occupation = "'.$aoccupationp.'",';
						$qmu .= 'origin = "'.$acountryoforiginp.'",';
						$qmu .= 'checked = "No"';
						$qmu .= ' where member_id = '.$memidp;
						
						$rmu = mysql_query($qmu) or die(mysql_error().' '.$qmu);
						
						// establish partner cross reference
						$qpart = "update members set partner_id = ".$memid." where member_id = ".$memidp;
						$rpart = mysql_query($qpart) or die(mysql_error().' '.$qpart);
						$qpart = "update members set partner_id = ".$memidp." where member_id = ".$memid;
						$rpart = mysql_query($qpart) or die(mysql_error().' '.$qpart);
					
					
						// update comms
						
						if ($aphonehomenumber1p != "") {
							$comm1 = str_replace(" ","",$aphonehomenumber1p);
							$comm2 = str_replace("-","",$comm1);
							$comm3 = str_replace(")","",$comm2);
							$comm4 = str_replace("(","",$comm3);
							$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '1,';
							$q .= '"'.$aphonehomecountry1p.'",';
							$q .= '"'.$aphonehomearea1p.'",';
							$q .= '"'.$aphonehomenumber1p.'",';
							$q .= '"'.$comm4.'",';
							$q .= $this->xsid.')';
							
							$r = mysql_query($q) or die(mysql_error().' '.$q);
						}
					
						if ($aphonehomenumber2p != "") {
							$comm1 = str_replace(" ","",$aphonehomenumber2p);
							$comm2 = str_replace("-","",$comm1);
							$comm3 = str_replace(")","",$comm2);
							$comm4 = str_replace("(","",$comm3);
							$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '1,';
							$q .= '"'.$aphonehomecountry2p.'",';
							$q .= '"'.$aphonehomearea2p.'",';
							$q .= '"'.$aphonehomenumber2p.'",';
							$q .= '"'.$comm4.'",';
							$q .= $this->xsid.')';
							
							$r = mysql_query($q) or die(mysql_error().' '.$q);
						}
					
						if ($amobilenumber1p != "") {
							$comm1 = str_replace(" ","",$amobilenumber1p);
							$comm2 = str_replace("-","",$comm1);
							$comm3 = str_replace(")","",$comm2);
							$comm4 = str_replace("(","",$comm3);
							$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '3,';
							$q .= '"'.$amobilecountry1p.'",';
							$q .= '"'.$amobilecode1p.'",';
							$q .= '"'.$amobilenumber1p.'",';
							$q .= '"'.$comm4.'",';
							$q .= $this->xsid.')';
							
							$r = mysql_query($q) or die(mysql_error().' '.$q);
						}
					
						if ($amobilenumber2p != "") {
							$comm1 = str_replace(" ","",$amobilenumber2p);
							$comm2 = str_replace("-","",$comm1);
							$comm3 = str_replace(")","",$comm2);
							$comm4 = str_replace("(","",$comm3);
							$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '3,';
							$q .= '"'.$amobilecountry2p.'",';
							$q .= '"'.$amobilecode2p.'",';
							$q .= '"'.$amobilenumber2p.'",';
							$q .= '"'.$comm4.'",';
							$q .= $this->xsid.')';
							
							$r = mysql_query($q) or die(mysql_error().' '.$q);
						}
			
						if ($aphoneworknumber1p != "") {
							$comm1 = str_replace(" ","",$aphoneworknumber1p);
							$comm2 = str_replace("-","",$comm1);
							$comm3 = str_replace(")","",$comm2);
							$comm4 = str_replace("(","",$comm3);
							$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '2,';
							$q .= '"'.$aphoneworkcountry1p.'",';
							$q .= '"'.$aphoneworkarea1p.'",';
							$q .= '"'.$aphoneworknumber1p.'",';
							$q .= '"'.$comm4.'",';
							$q .= $this->xsid.')';
							
							$r = mysql_query($q) or die(mysql_error().' '.$q);
						}
					
						if ($aphoneworknumber2p != "") {
							$comm1 = str_replace(" ","",$aphoneworknumber2p);
							$comm2 = str_replace("-","",$comm1);
							$comm3 = str_replace(")","",$comm2);
							$comm4 = str_replace("(","",$comm3);
							$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '2,';
							$q .= '"'.$aphoneworkcountry2p.'",';
							$q .= '"'.$aphoneworkarea2p.'",';
							$q .= '"'.$aphoneworknumber2p.'",';
							$q .= '"'.$comm4.'",';
							$q .= $this->xsid.')';
							
							$r = mysql_query($q) or die(mysql_error().' '.$q);
						}
			
						if ($aphoneworknumber3p != "") {
							$comm1 = str_replace(" ","",$aphoneworknumber3p);
							$comm2 = str_replace("-","",$comm1);
							$comm3 = str_replace(")","",$comm2);
							$comm4 = str_replace("(","",$comm3);
							$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '2,';
							$q .= '"'.$aphoneworkcountry3p.'",';
							$q .= '"'.$aphoneworkarea3p.'",';
							$q .= '"'.$aphoneworknumber3p.'",';
							$q .= '"'.$comm4.'",';
							$q .= $this->xsid.')';
							
							$r = mysql_query($q) or die(mysql_error().' '.$q);
						}
			
						if ($aphoneworknumber4p != "") {
							$comm1 = str_replace(" ","",$aphoneworknumber4p);
							$comm2 = str_replace("-","",$comm1);
							$comm3 = str_replace(")","",$comm2);
							$comm4 = str_replace("(","",$comm3);
							$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '2,';
							$q .= '"'.$aphoneworkcountry4p.'",';
							$q .= '"'.$aphoneworkarea4p.'",';
							$q .= '"'.$aphoneworknumber4p.'",';
							$q .= '"'.$comm4.'",';
							$q .= $this->xsid.')';
							
							$r = mysql_query($q) or die(mysql_error().' '.$q);
						}
			
						if ($afaxworknumberp != "") {
							$comm1 = str_replace(" ","",$afaxworknumberp);
							$comm2 = str_replace("-","",$comm1);
							$comm3 = str_replace(")","",$comm2);
							$comm4 = str_replace("(","",$comm3);
							$q = "insert into comms (member_id,staff_id,comms_type_id,country_code,area_code,comm,comm2,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '6,';
							$q .= '"'.$afaxworkcountryp.'",';
							$q .= '"'.$afaxworkareap.'",';
							$q .= '"'.$afaxworknumberp.'",';
							$q .= '"'.$comm4.'",';
							$q .= $this->xsid.')';
							
							$r = mysql_query($q) or die(mysql_error().' '.$q);
						}
			
						if ($aemail1p != "") {
							$q = "insert into comms (member_id,staff_id,comms_type_id,comm,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '4,';
							$q .= '"'.$aemail1p.'",';
							$q .= $this->xsid.')';
							
							$r = mysql_query($q) or die(mysql_error().' '.$q);
						}
					
						if ($aemail2p != "") {
							$q = "insert into comms (member_id,staff_id,comms_type_id,comm,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '4,';
							$q .= '"'.$aemail2p.'",';
							$q .= $this->xsid.')';
							
							$r = mysql_query($q) or die(mysql_error().' '.$q);
						}
			
						// update addresses
						//Home Street address
						
						if ($ahomestreettownp != "") {
							$q = "insert into addresses (member_id,staff_id,location,address_type_id,street_no,ad1,suburb,town,country,postcode,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '"Street",';
							$q .= '1,';
							$q .= '"'.$ahomestreetnop.'",';
							$q .= '"'.htmlentities($ahomestreetnamep).'",';
							$q .= '"'.htmlentities($ahomestreetsuburbp).'",';
							$q .= '"'.htmlentities($ahomestreettownp).'",';
							$q .= '"'.htmlentities($ahomestreetcountryp).'",';
							$q .= '"'.$ahomestreetpostcodep.'",';
							$q .= $this->xsid.')';
					
							$r = mysql_query($q) or die(mysql_error().' '.$q);
							
						}
							
						//Home Post Box address
						
						if ($ahomeponumberp != "") {
							$q = "insert into addresses (member_id,staff_id,location,address_type_id,street_no,suburb,town,country,postcode,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '"Post Box",';
							$q .= '1,';
							$q .= '"'.$ahomeponumberp.'",';
							$q .= '"'.htmlentities($ahomeposuburbp).'",';
							$q .= '"'.htmlentities($ahomepotownp).'",';
							$q .= '"'.htmlentities($ahomepocountryp).'",';
							$q .= '"'.$ahomepopostcodep.'",';
							$q .= $this->xsid.')';
					
							$r = mysql_query($q) or die(mysql_error().' '.$q);
							
						}
							
						//Work Street address
						
						if ($aworkstreettownp != "") {
							$q = "insert into addresses (member_id,staff_id,location,address_type_id,street_no,ad1,suburb,town,country,postcode,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '"Street",';
							$q .= '2,';
							$q .= '"'.$aworkstreetnop.'",';
							$q .= '"'.htmlentities($aworkstreetnamep).'",';
							$q .= '"'.htmlentities($aworkstreetsuburbp).'",';
							$q .= '"'.htmlentities($aworkstreettownp).'",';
							$q .= '"'.htmlentities($aworkstreetcountryp).'",';
							$q .= '"'.$aworkstreetpostcodep.'",';
							$q .= $this->xsid.')';
					
							$r = mysql_query($q) or die(mysql_error().' '.$q);
							
						}
							
						// Update notes
						
						if ($anotes1p != "") {
							$q = "insert into activities (member_id,staff_id,ddate,ttime,activity,status,contact,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '"'.$hdate.'",';
							$q .= '"'.$htime.'",';
							$q .= '"'.$anotes1p.'",';
							$q .= '"Sealed",';
							$q .= '"Other",';
							$q .= $this->xsid.')';
					
							$r = mysql_query($q) or die(mysql_error().' '.$q);
							
						}
						
						if ($anotes2p != "") {
							$q = "insert into activities (member_id,staff_id,ddate,ttime,activity,status,contact,sub_id) values (";
							$q .= $memidp.',';
							$q .= $this->xuserid.',';
							$q .= '"'.$hdate.'",';
							$q .= '"'.$htime.'",';
							$q .= '"'.$anotes2p.'",';
							$q .= '"Sealed",';
							$q .= '"Other",';
							$q .= $this->xsid.')';
					
							$r = mysql_query($q) or die(mysql_error().' '.$q);
							
						}
					
					}	// if ($alastnamep != "") {
						
					//*********************************************************************************************************************
					// Associates
					//*********************************************************************************************************************
		
					if ($a1lastname != "" && $a1relationship != "") {
				
						$q = "insert into members (sub_id) values (".$msubid.")";
						$r = mysql_query($q) or die(mysql_error().' '.$q);
						$amemid = mysql_insert_id();
					
						// update members
						$qmu = "update members set ";
						$qmu .= 'dbs = "'.$mdbs.'",';
						$qmu .= 'firstname = "'.htmlentities($a1firstname).'",';
						$qmu .= 'lastname = "'.htmlentities($a1lastname).'",';
						$qmu .= 'preferredname = "'.htmlentities($a1preferredname).'",';
						$qmu .= 'dob = "'.$a1dob.'",';
						$qmu .= 'gender = "'.$a1gender.'"';
						$qmu .= ' where member_id = '.$amemid;
						
						$rmu = mysql_query($qmu) or die(mysql_error().' '.$qmu);
						
						// update relationship
						switch (strtoupper($a1relationship)) {
							case 'SON':
								$relationshipa = 'Child';
								$relationshipm = 'Parent';
								break;
							case 'DAUGHTER':
								$relationshipa = 'Child';
								$relationshipm = 'Parent';
								break;
							case 'CHILD':
								$relationshipa = 'Child';
								$relationshipm = 'Parent';
								break;
							case 'MOTHER':
								$relationshipa = 'Parent';
								$relationshipm = 'Child';
								break;
							case 'FATHER':
								$relationshipa = 'Parent';
								$relationshipm = 'Child';
								break;
							case 'PARENT':
								$relationshipa = 'Parent';
								$relationshipm = 'Child';
								break;
							default:
								$relationshipa = '';
								$relationshipm = '';
								break;
							
						}
						
						$qr = 'insert into assoc_xref (sub_id,member_id,association,of_id) values ('.$msubid.','.$amemid.',"'.$relationshipa.'",'.$omemid.')';
						$rr = mysql_query($qr) or die(mysql_error().' '.$qr);
						$qr = 'insert into assoc_xref (sub_id,member_id,association,of_id) values ('.$msubid.','.$omemid.',"'.$relationshipm.'",'.$amemid.')';
						$rr = mysql_query($qr) or die(mysql_error().' '.$qr);
				
					}
				
					if ($a2lastname != "" && $a2relationship != "") {
				
						$q = "insert into members (sub_id) values (".$msubid.")";
						$r = mysql_query($q) or die(mysql_error().' '.$q);
						$amemid = mysql_insert_id();
					
						// update members
						$qmu = "update members set ";
						$qmu .= 'dbs = "'.$mdbs.'",';
						$qmu .= 'firstname = "'.htmlentities($a2firstname).'",';
						$qmu .= 'lastname = "'.htmlentities($a2lastname).'",';
						$qmu .= 'preferredname = "'.htmlentities($a2preferredname).'",';
						$qmu .= 'dob = "'.$a2dob.'",';
						$qmu .= 'gender = "'.$a2gender.'"';
						$qmu .= ' where member_id = '.$amemid;
						
						$rmu = mysql_query($qmu) or die(mysql_error().' '.$qmu);
						
						// update relationship
						switch (strtoupper($a2relationship)) {
							case 'SON':
								$relationshipa = 'Child';
								$relationshipm = 'Parent';
								break;
							case 'DAUGHTER':
								$relationshipa = 'Child';
								$relationshipm = 'Parent';
								break;
							case 'CHILD':
								$relationshipa = 'Child';
								$relationshipm = 'Parent';
								break;
							case 'MOTHER':
								$relationshipa = 'Parent';
								$relationshipm = 'Child';
								break;
							case 'FATHER':
								$relationshipa = 'Parent';
								$relationshipm = 'Child';
								break;
							case 'PARENT':
								$relationshipa = 'Parent';
								$relationshipm = 'Child';
								break;
							default:
								$relationshipa = '';
								$relationshipm = '';
								break;
							
						}
						
						$qr = 'insert into assoc_xref (sub_id,member_id,association,of_id) values ('.$msubid.','.$amemid.',"'.$relationshipa.'",'.$omemid.')';
						$rr = mysql_query($qr) or die(mysql_error().' '.$qr);
						$qr = 'insert into assoc_xref (sub_id,member_id,association,of_id) values ('.$msubid.','.$omemid.',"'.$relationshipm.'",'.$amemid.')';
						$rr = mysql_query($qr) or die(mysql_error().' '.$qr);
				
					}
				
					if ($a3lastname != "" && $a3relationship != "") {
				
						$q = "insert into members (sub_id) values (".$msubid.")";
						$r = mysql_query($q) or die(mysql_error().' '.$q);
						$amemid = mysql_insert_id();
					
						// update members
						$qmu = "update members set ";
						$qmu .= 'dbs = "'.$mdbs.'",';
						$qmu .= 'firstname = "'.htmlentities($a3firstname).'",';
						$qmu .= 'lastname = "'.htmlentities($a3lastname).'",';
						$qmu .= 'preferredname = "'.htmlentities($a3preferredname).'",';
						$qmu .= 'dob = "'.$a3dob.'",';
						$qmu .= 'gender = "'.$a3gender.'"';
						$qmu .= ' where member_id = '.$amemid;
						
						$rmu = mysql_query($qmu) or die(mysql_error().' '.$qmu);
						
						// update relationship
						switch (strtoupper($a3relationship)) {
							case 'SON':
								$relationshipa = 'Child';
								$relationshipm = 'Parent';
								break;
							case 'DAUGHTER':
								$relationshipa = 'Child';
								$relationshipm = 'Parent';
								break;
							case 'CHILD':
								$relationshipa = 'Child';
								$relationshipm = 'Parent';
								break;
							case 'MOTHER':
								$relationshipa = 'Parent';
								$relationshipm = 'Child';
								break;
							case 'FATHER':
								$relationshipa = 'Parent';
								$relationshipm = 'Child';
								break;
							case 'PARENT':
								$relationshipa = 'Parent';
								$relationshipm = 'Child';
								break;
							default:
								$relationshipa = '';
								$relationshipm = '';
								break;
							
						}
						
						$qr = 'insert into assoc_xref (sub_id,member_id,association,of_id) values ('.$msubid.','.$amemid.',"'.$relationshipa.'",'.$omemid.')';
						$rr = mysql_query($qr) or die(mysql_error().' '.$qr);
						$qr = 'insert into assoc_xref (sub_id,member_id,association,of_id) values ('.$msubid.','.$omemid.',"'.$relationshipm.'",'.$amemid.')';
						$rr = mysql_query($qr) or die(mysql_error().' '.$qr);
				
					}
				
					if ($a4lastname != "" && $a4relationship != "") {
				
						$q = "insert into members (sub_id) values (".$msubid.")";
						$r = mysql_query($q) or die(mysql_error().' '.$q);
						$amemid = mysql_insert_id();
					
						// update members
						$qmu = "update members set ";
						$qmu .= 'dbs = "'.$mdbs.'",';
						$qmu .= 'firstname = "'.htmlentities($a4firstname).'",';
						$qmu .= 'lastname = "'.htmlentities($a4lastname).'",';
						$qmu .= 'preferredname = "'.htmlentities($a4preferredname).'",';
						$qmu .= 'dob = "'.$a4dob.'",';
						$qmu .= 'gender = "'.$a4gender.'"';
						$qmu .= ' where member_id = '.$amemid;
						
						$rmu = mysql_query($qmu) or die(mysql_error().' '.$qmu);
						
						// update relationship
						switch (strtoupper($a4relationship)) {
							case 'SON':
								$relationshipa = 'Child';
								$relationshipm = 'Parent';
								break;
							case 'DAUGHTER':
								$relationshipa = 'Child';
								$relationshipm = 'Parent';
								break;
							case 'CHILD':
								$relationshipa = 'Child';
								$relationshipm = 'Parent';
								break;
							case 'MOTHER':
								$relationshipa = 'Parent';
								$relationshipm = 'Child';
								break;
							case 'FATHER':
								$relationshipa = 'Parent';
								$relationshipm = 'Child';
								break;
							case 'PARENT':
								$relationshipa = 'Parent';
								$relationshipm = 'Child';
								break;
							default:
								$relationshipa = '';
								$relationshipm = '';
								break;
							
						}
						
						$qr = 'insert into assoc_xref (sub_id,member_id,association,of_id) values ('.$msubid.','.$amemid.',"'.$relationshipa.'",'.$omemid.')';
						$rr = mysql_query($qr) or die(mysql_error().' '.$qr);
						$qr = 'insert into assoc_xref (sub_id,member_id,association,of_id) values ('.$msubid.','.$omemid.',"'.$relationshipm.'",'.$amemid.')';
						$rr = mysql_query($qr) or die(mysql_error().' '.$qr);
				
					}
				}
			}
			
		} //while
	} // function updateclients



} //class

?>