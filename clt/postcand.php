<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

$cltdb = $_SESSION['s_cltdb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];
$usergroup = $row['usergroup'];

$candid = $_REQUEST['candid'];
$action = $_REQUEST['action'];
$memid = $_REQUEST['memid'];

if ($action != '') {
	$db->query("update ".$cltdb.".candidates set workflow = '".$action."' where candidate_id = ".$candid);
	$db->execute();
	switch ($action) {
		case 'Not Available':
			$db->query("update ".$cltdb.".candidates set candstatus = 'Available' where candidate_id = ".$candid);
			$db->execute();
			break;
		case 'Callback':
			$ddate = $_REQUEST['ddate'];
			$ttime = $_REQUEST['ttime'];
			$memid = $_REQUEST['memid'];
			$em = 'Callback on '.$ddate.' at '.$ttime;
			$db->query("update ".$cltdb.".candidates set candstatus = 'Available', workflow = 'Callback - ".$ddate."' where candidate_id = ".$candid);
			$db->execute();
			$db->query("update ".$cltdb.".members set status = 'In progress' where member_id = ".$memid);
			$db->execute();

			include_once("../includes/cltadmin.php");
			$oAct = new cltadmin;	

			date_default_timezone_set($_SESSION['s_timezone']);
			$edate = date("Y-m-d");
			$ttime = strftime("%H:%M", mktime());

			$oAct->client_id = $memid;

			$oAct->ddate = $edate;
			$oAct->ttime = $ttime;
			$oAct->activity = $em;
			$oAct->status = 'Sealed';
			$oAct->staff_id = $user_id;
			$oAct->sub_id = $subscriber;
			$oAct->contact = 'Other';

			$actid = $oAct->AddActivity();

			break;
		case 'Not Interested':
			$db->query("update ".$cltdb.".candidates set candstatus = 'Complete' where candidate_id = ".$candid);
			$db->execute();
			$db->query("update ".$cltdb.".members set status = 'Passive' where member_id = ".$memid);
			$db->execute();
			break;

		case 'Appointment':
			$em = $_REQUEST['email'];
			$adv_id = $_REQUEST['advisor'];
			$memid = $_REQUEST['memid'];
			$caller = $_REQUEST['caller'];
			$db->query("update ".$cltdb.".candidates set candstatus = 'Complete' where candidate_id = ".$candid);
			$db->execute();
			$db->query("update ".$cltdb.".members set status = 'In progress' where member_id = ".$memid);
			$db->execute();
			
			// get all communications for member

			$db->query("select comms_type.comms_type_id,comms_type.comm_type,comms.country_code,comms.area_code,comms.comm,comms.preferred from ".$cltdb.".comms_type,".$cltdb.".comms where comms_type.comms_type_id = comms.comms_type_id and comms.member_id = ".$memid);
			$rows = $db->resultset();

			$commstring = '';

			if ($db->rowCount() > 0) {
			  foreach ($rows as $row) {
				extract($row);
				$commstring .= $comm_type.' '.trim($country_code.' '.$area_code.' '.$comm);
				if ($preferred == 'Y') {
					$commstring .= ' Preferred';
				}
				$commstring .= '\n';
			  }
			}

			include_once("../includes/cltadmin.php");

			$oAct = new cltadmin;	

			$subject = 'Campaign Appointment';
			$message = $em.'\n'.$commstring;

			$oAct->client_id = $memid;
			$oAct->emdate = $emdate;
			$oAct->emsubject = $subject;
			$oAct->emmessage = $em;
			$oAct->staff_id = $user_id;
			$oAct->sub_id = $sub_id;

			$actid = $oAct->AddEmail();

			// send email to advisor
			$db->query("select uemail as email from users where uid = ".$adv_id);
			$row = $db->single();
			extract($row);
			if ($email != "") {
				$to = $email;
			} else { 
				$from = "admin@biztracc.com";
			}

			$db->query("select uemail as email from users where uid = ".$caller);
			$row = $db->single();
			extract($row);
			if ($email != "") {
				$from = $email;
			} else { 
				$from = "admin@biztracc.com";
			}

			$db->query("insert into ".$cltdb.".emails2send (email_date,email_from,email_to,email_subject,email_message,sub_id) values ('".$emdate."','".$from."','".$to."','".$subject."','".$message."',".$subscriber.")");
			$db->execute();
			
			
			break;
		case 'Advisor Callback':
			$em = $_REQUEST['email'];
			$adv_id = $_REQUEST['advisor'];
			$memid = $_REQUEST['memid'];
			$caller = $_REQUEST['caller'];
			$db->query("update ".$cltdb.".candidates set candstatus = 'Complete' where candidate_id = ".$candid);
			$db->execute();
			$db->query("update ".$cltdb.".members set status = 'In progress', next_meeting = '".$emdate."' where member_id = ".$memid);
			$db->execute();

			// get all communications for member

			$db->query("select comms_type.comms_type_id,comms_type.comm_type,comms.country_code,comms.area_code,comms.comm,comms.preferred from ".$cltdb.".comms_type,".$cltdb.".comms where comms_type.comms_type_id = comms.comms_type_id and comms.member_id = ".$memid);
			$rows = $db->resultset();

			$commstring = '';

			if ($db->rowCount() > 0) {
			  foreach ($rows as $row) {
				extract($row);
				$commstring .= $comm_type.' '.trim($country_code.' '.$area_code.' '.$comm);
				if ($preferred == 'Y') {
					$commstring .= ' Preferred';
				}
				$commstring .= '\n';
			  }
			}

			include_once("../includes/cltadmin.php");

			$oAct = new cltadmin;	

			$subject = 'Campaign Advisor Callback';
			$message = $em.'\n'.$commstring;

			$oAct->client_id = $memid;
			$oAct->emdate = $emdate;
			$oAct->emsubject = $subject;
			$oAct->emmessage = $em;
			$oAct->staff_id = $user_id;
			$oAct->sub_id = $sub_id;

			$actid = $oAct->AddEmail();

			// send email to advisor
			$db->query("select uemail as email from users where uid = ".$adv_id);
			$row = $db->single();
			extract($row);
			if ($email != "") {
				$to = $email;
			} else { 
				$from = "admin@biztracc.com";
			}

			$db->query("select uemail as email from users where uid = ".$caller);
			$row = $db->single();
			extract($row);
			if ($email != "") {
				$from = $email;
			} else { 
				$from = "admin@biztracc.com";
			}

			$db->query("insert into ".$cltdb.".emails2send (email_date,email_from,email_to,email_subject,email_message,sub_id) values ('".$emdate."','".$from."','".$to."','".$subject."','".$message."',".$subscriber.")");
			$db->execute();
			break;

		case 'Advisor Email':
			$em = $_REQUEST['email'];
			$adv_id = $_REQUEST['advisor'];
			$memid = $_REQUEST['memid'];
			$caller = $_REQUEST['caller'];
			$db->query("update ".$cltdb.".candidates set candstatus = 'Complete' where candidate_id = ".$candid);
			$db->execute();
			$db->query("update ".$cltdb.".members set status = 'In progress' where member_id = ".$memid);
			$db->execute();

			include_once("../includes/cltadmin.php");

			$oAct = new cltadmin;	

			date_default_timezone_set($_SESSION['s_timezone']);
			$edate = date("Y-m-d");
			$ttime = strftime("%H:%M", mktime());

			$subject = 'Campaign Advisor email client';
			$message = $em;

			$oAct->client_id = $memid;
			$oAct->emdate = $edate;
			$oAct->emsubject = $subject;
			$oAct->emmessage = $em;
			$oAct->staff_id = $user_id;
			$oAct->sub_id = $sub_id;

			$actid = $oAct->AddEmail();

			// send email to advisor
			$db->query("select uemail as email from users where uid = ".$adv_id);
			$row = $db->single();
			extract($row);
			if ($email != "") {
				$to = $email;
			} else { 
				$from = "admin@biztracc.com";
			}

			$db->query("select uemail as email from users where uid = ".$caller);
			$row = $db->single();
			extract($row);
			if ($email != "") {
				$from = $email;
			} else { 
				$from = "admin@biztracc.com";
			}

			 require("../includes/phpMailer/class.phpmailer.php");  
			 $mail = new PHPMailer();  

			 $mail->IsSMTP();  // telling the class to use SMTP  
			 $mail->Host     = "localhost"; // SMTP server  
			 $mail->From = $from;
			 $mail->FromName = $uname;
			 $mail->SetFrom($from,$uname);
			 $mail->AddReplyTo($from,$uname);
			 $mail->AddAddress($to);  
			 $mail->Subject  = $subject;  
			 $mail->Body     = $message;  
			 $mail->WordWrap = 50;  

			 if(!$mail->Send()) {  
			   $retmsg = 'Message was not sent.'.' Mailer error: ' . $mail->ErrorInfo; 
			 } else {  
			   $retmsg = 'Message has been sent to '.$to;  
			 }  

			echo '<script>';
			echo 'alert("'.$retmsg.'");';
			echo 'this.close();';
			echo '</script>';
			break;

		case 'See Notes':
			$em = $_REQUEST['email'];
			$adv_id = $_REQUEST['advisor'];
			$memid = $_REQUEST['memid'];
			$caller = $_REQUEST['caller'];
			$db->query("update ".$cltdb.".candidates set candstatus = 'Available' where candidate_id = ".$candid);
			$db->execute();
			$db->query("update ".$cltdb.".members set status = 'In progress' where member_id = ".$memid);
			$db->execute();
			
			// get all communications for member

			$db->query("select comms_type.comms_type_id,comms_type.comm_type,comms.country_code,comms.area_code,comms.comm,comms.preferred from ".$cltdb.".comms_type,".$cltdb.".comms where comms_type.comms_type_id = comms.comms_type_id and comms.member_id = ".$memid);
			$rows = $db->resultset();

			$commstring = '';

			if ($db->rowCount() > 0) {
			  foreach ($rows as $row) {
				extract($row);
				$commstring .= $comm_type.' '.trim($country_code.' '.$area_code.' '.$comm);
				if ($preferred == 'Y') {
					$commstring .= ' Preferred';
				}
				$commstring .= '\n';
			  }
			}

			include_once("../includes/cltadmin.php");

			$oAct = new cltadmin;	

			$subject = 'Campaign Notes';
			$message = $em.'\n'.$commstring;

			$oAct->client_id = $memid;
			$oAct->emdate = $emdate;
			$oAct->emsubject = $subject;
			$oAct->emmessage = $em;
			$oAct->staff_id = $user_id;
			$oAct->sub_id = $sub_id;

			$actid = $oAct->AddEmail();

			// send email to advisor
			$db->query("select uemail as email from users where uid = ".$adv_id);
			$row = $db->single();
			extract($row);
			if ($email != "") {
				$to = $email;
			} else { 
				$from = "admin@biztracc.com";
			}

			$db->query("select uemail as email from users where uid = ".$caller);
			$row = $db->single();
			extract($row);
			if ($email != "") {
				$from = $email;
			} else { 
				$from = "admin@biztracc.com";
			}

			$db->query("insert into ".$cltdb.".emails2send (email_date,email_from,email_to,email_subject,email_message,sub_id) values ('".$emdate."','".$from."','".$to."','".$subject."','".$message."',".$subscriber.")");
			$db->execute();
			
			break;
	}
	
	$db->closeDB();

	echo '<script>';
	echo 'window.open("","campcandidates").jQuery("#candlist").trigger("reloadGrid");';
	echo 'window.open("","campcandidates").jQuery("#cnotelist").trigger("reloadGrid");';
	echo 'this.close();';
	echo '</script>';
}



?>



