<?php
session_start();
$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");
$memberid = $_SESSION["s_memberid"];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$cltdb = $_SESSION['s_cltdb'];


// get sender/subscriber's email address and name
$db->query("select email as subemail, sub_name as subname from subscribers where sub_id = ".$subscriber);
$row = $db->single();
extract($row);


$template = $_REQUEST['template'];
$output = $_REQUEST['output'];
$mergefile = $_REQUEST['mergefile'];
$tsubject = $_REQUEST['tsubject'];
$tmessage = $_REQUEST['tmessage'];
$dt = date("Y-m-d");

$f = explode("__",$output);
$filename = $f[1];

require_once('../../includes/word/classes/CreateDocx.inc');
$objDocx = new CreateDocx();

chmod('../../documents/',0777);
chmod('../../documents/tempdocs/',0777);
chmod('../../documents/clients/',0777);

$db->query("select member_id from ".$mergefile);
$db->rows = resultset();
foreach ($rows as $row) {
	extract($row);
	$memid = $member_id;
	$clientfile = $memid."__".$filename;
		
	$db->query("select sub_name,pad1,pad2,pad3,padtown,padpostcode from subscribers where sub_id =".$subscriber);
	$row = $db->single();
	extract($row);
	
	$db->query("select * from members WHERE `member_id` = ".$memid);
	$row = $db->single();
	extract($row);		
	$mstatus = $category;
	$mtype = $client_type_id;
	$mprocess = $process_id;
	$mdbs = $dbs_id;
	$mrefby = $referred_id;
	$mfirstname = $firstname;
	$mmiddlename = $middlename;
	$mpreferredname = $preferredname;
	$mlastname = $lastname;
	$mdob = split('-',$dob);
	$md = $mdob[2];
	$mm = $mdob[1];
	$my = $mdob[0];
	$mbirth = date("jS F", mktime(0,0,0,$mm,$md,$my));
	
	if ($dob == "0000-00-00") {
		$mage = 0;
	} else {
		$mage = floor((time() - strtotime($dob))/31556926);
	}
	$mgender = $gender;
	$mtitle = $title;
	$mmarried = $married;
	$msmoker = $smoker;
	$mpadvisor = $padvisor;
	$mpartner_id = $partner_id;
	$mrmonth = $review_month;
	$mrating = $rating;
	$memployer = $employer;
	$mcnt = $contract;
	if ($salary > 0) {
		$msal = $salary;
	} else {
		$msal = 0;
	}
	$mposition = $position;
	$mcom = split('-',$commenced);
	$med = $mcom[2];
	$mem = $mcom[1];
	$mey = $mcom[0];
	if ($commenced == "0000-00-00") {
		$mservice = 0;
	} else {
		$mservice = floor((time() - strtotime($commenced))/31556926);
	}
	
	
	// get business addresses
	$db->query("select distinct * from addresses where member_id = ".$memid." and address_type_id = 2 and location = 'Street'");
	$row = $db->single();
	if ($db->rowCount() > 0) {
		extract($row);	
		$mstreetbus = '';
		if ($street_no != '') {
			$mstreetbus .= $street_no.' '.$ad1."\n";
		}
		if ($ad2 != '') {
			$mstreetbus .= $ad2."\n";
		}
		if ($suburb != '') {
			$mstreetbus .= $suburb."\n";
		}
		if ($town != '') {
			$mstreetbus .= $town.' '.$postcode;
		}
	} else {
		$mstreetbus= '';
	}
	
	$mbusaddress = '';
	if ($memployer != '') {
		$mbusaddress .= $memployer."\n";
	}
	$mbusaddress .= $mstreetbus;
	
	//get phone numbers
	$db->query("select distinct * from comms WHERE member_id = ".$memid." and comms_type_id = 1");
	$row = $db->single();
	if ($db->rowCount() > 0) {
		extract($row);	
		$mhome = trim($country_code.' '.$area_code.' '.$comm);
	} else {
		$mhome = '';
	}
	$db->query("select distinct * from comms WHERE member_id = ".$memid." and comms_type_id = 2");
	$row = $db->single();
	if ($db->rowCount() > 0) {
		extract($row);	
		$mwork = trim($country_code.' '.$area_code.' '.$comm);
	} else {
		$mwork = '';
	}
	$db->query("select distinct * from comms WHERE `member_id` = ".$memid." and comms_type_id = 3");
	$row = $db->single();
	if ($db->rowCount() > 0) {
		extract($row);	
		$mmobile = trim($country_code.' '.$area_code.' '.$comm);
	} else {
		$mmobile= '';
	}
	$db->query("select distinct * from comms WHERE `member_id` = ".$memid." and comms_type_id = 4");
	$row = $db->single();
	if ($db->rowCount() > 0) {
		extract($row);	
		$memail = $comm;
	} else {
		$memail = '';
	}
	$db->query("select distinct * from comms WHERE `member_id` = ".$memid." and comms_type_id = 6");
	$row = $db->single();
	if ($db->rowCount() > 0) {
		extract($row);	
		$mwfax = $comm;
	} else {
		$mwfax = '';
	}
	$db->query("select distinct * from comms WHERE `member_id` = ".$memid." and comms_type_id = 7");
	$row = $db->single();
	if ($db->rowCount() > 0) {
		extract($row);	
		$mhfax = $comm;
	} else {
		$mhfax = '';
	}
	$db->query("select distinct * from comms WHERE `member_id` = ".$memid." and comms_type_id = 11");
	$row = $db->single();
	if ($db->rowCount() > 0) {
		extract($row);	
		$mweb = $comm;
	} else {
		$mweb = '';
	}
	
	// get addresses
	$db->query("select distinct * from addresses where member_id = ".$memid." and address_type_id = 1 and location = 'Street'");
	$row = $db->single();
	$numstreetrows = $db->rowCount();
	if ($numstreetrows > 0) {
		extract($row);	
		$mstreethome = '';
		if ($street_no != '') {
			$ms1 = $street_no.' '.$ad1;
		} else {
			$ms1 = '';
		}
		if ($ad2 != '') {
			$ms2 = $ad2;
		} else {
			$ms2 = '';
		}
		if ($suburb != '') {
			$mssuburb = $suburb;
		} else {
			$mssuburb = '';
		}
		if ($town != '') {
			$mstown = $town;
		} else {
			$mstown = '';
		}
		if ($postcode != '') {
			$mspostcode = $postcode;
		} else {
			$mspostcode = '';
		}
	}
	$db->query("select distinct * from addresses where member_id = ".$memid." and address_type_id = 1 and location = 'Postal'");
	$row = $db->single();
	$numpostalrows = $db->rowCount();
	if ($numpostalrows > 0) {
		extract($row);	
		if ($street_no != '') {
			$tadd1 = $street_no.' '.$ad1;
		} else {
			$tadd1 = '';
		}
		if ($ad2 != '') {
			$tadd2 = $ad2;
		} else {
			$tadd2 = '';
		}
		if ($suburb != '') {
			$tsuburb = $suburb;
		} else {
			$tsuburb = '';
		}
		if ($town != '') {
			$ttown = $town;
		} else {
			$ttown = '';
		}
		if ($postcode != '') {
			$tpostcode = $postcode;
		} else {
			$tpostcode = '';
		}
	} else {
		if ($numstreetrows > 0) {
			$tadd1 = $ms1;
			$tadd2 = $ms2;
			$tsuburb = $mssuburb;
			$ttown = $mstown;
			$tpostcode = $mspostcode;
		} else {
			$tadd1 = '';
			$tadd2 = '';
			$tsuburb = '';
			$ttown = '';
			$tpostcode = '';
		}
	}
	
	
	$objDocx->addTemplate('../../documents/templates/'.$template);
	
	$objDocx->addTemplateVariable('date',date("d, F Y"));
	$objDocx->addTemplateVariable('title',$mtitle);
	$objDocx->addTemplateVariable('firstname',$mfirstname);
	$objDocx->addTemplateVariable('lastname',$mlastname);
	$objDocx->addTemplateVariable('preferred',$mpreferredname);
	$objDocx->addTemplateVariable('partner_firstname',$pfirstname);
	$objDocx->addTemplateVariable('birthdate',$mbirth);
	$objDocx->addTemplateVariable('theiraddress1',$tadd1);
	$objDocx->addTemplateVariable('theiraddress2',$tadd2);
	$objDocx->addTemplateVariable('theirsuburb',$tsuburb);
	$objDocx->addTemplateVariable('theirtown',$ttown);
	$objDocx->addTemplateVariable('theirpostcode',$tpostcode);
	$objDocx->addTemplateVariable('ouraddress1',$pad1);
	$objDocx->addTemplateVariable('ouraddress2',$pad2);
	$objDocx->addTemplateVariable('oursuburb',$pad3);
	$objDocx->addTemplateVariable('ourtown',$padtown);
	$objDocx->addTemplateVariable('ourpostcode',$padpostcode);
	$objDocx->addBreak('page');	
	
	$objDocx->createDocx('../../documents/clients/'.$clientfile);

	// email document as attachment to recipient

	require_once("../../includes/phpMailer/class.phpmailer.php");  
	
	$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
	$mail->IsSendmail(); // telling the class to use SendMail transport
	
	$email_from = $subemail;
	$email_to = $memail;
	$email_subject = $tsubject;
	$email_message = $tsubject;
	$ok = 'Y';
	if ($email_from == "") {
		$ok = 'N';
	}
	if ($email_to == "") {
		$ok = 'N';
	}
	
	if ($ok == 'Y') {
	
	  try {
		$mail->AddReplyTo($email_from,$email_from);
		$mail->AddAddress($email_to);
		$mail->SetFrom($email_from,$email_from);
		$mail->AddReplyTo($email_from,$email_from);
		$mail->Subject  = $email_subject;  
		$mail->AddAttachment("../../documents/clients/".$clientfile); // attachment
		$mail->Body     = $email_message;  
		$mail->Send();
		echo "Message Sent OK</p>\n";
	  } catch (phpmailerException $e) {
		echo $e->errorMessage(); //Pretty error messages from PHPMailer
	  } catch (Exception $e) {
		echo $e->getMessage(); //Boring error messages from anything else!
	  }
	
	}	
	

	//insert entry into documents table
	$db->query("insert into ".$cltdb.".documents (member_id,ddate,doc,staff,subject,sub_id) values (:member_id,:ddate,:doc,:staff,:subject,:sub_id)";
	$db->bind(':member_id'), $memid);																					 
	$db->bind(':ddate'), $dt);																					 
	$db->bind(':doc'), $filename);																					 
	$db->bind(':staff'), $uname);																					 
	$db->bind(':subject'), $_REQUEST['tsubject']);																					 
	$db->bind(':sub_id'), $subscriber);	
	
	$db->execute();

}

chmod('../../documents/tempdocs/',0755);
chmod('../../documents/clients/',0755);
chmod('../../documents/',0755);


?>
