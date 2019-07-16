<?php
session_start();
$name = $_REQUEST['name'];
$email_from = $_REQUEST['email'];
$msg = $_REQUEST['message'];
//$email_to = "sales@logtracc.co.nz";
$email_to = "mrsagana@gmail.com";

$tpt = 'smtp.webhost.co.nz';
$pot = 465;
$enc = 'ssl';
$usr = 'admin@logtracc.co.nz';
$pwd = 'logtrux99';

$_SESSION['s_transport'] = $tpt.'~'.$pot.'~'.$enc."~".$usr."~".$pwd;

$ok = 'Y';
if (trim($email_from) == "") {
	$ok = 'N';
}
				
if ($ok == 'Y') {
		
	require_once '../includes/swift_email/swift_required.php';
	
	$t = $_SESSION['s_transport'];
	$te = explode('~',$t);
	
				
	$transport = Swift_SmtpTransport::newInstance($te[0], $te[1], $te[2])
	  ->setUsername($te[3])
	  ->setPassword($te[4])
	  ;
	
	// Create the Mailer using your created Transport
	$mailer = Swift_Mailer::newInstance($transport);
			
	
	$message = Swift_Message::newInstance();
	$message->setSubject('Contact Enquiry');
	$message->setFrom(array($email_from => $name));
	$message->setTo(array($email_to => "Sales"));
	$mstring = $msg;
	$message->setBody($mstring,'text/plain');
			
	$result = $mailer->send($message);
	
	echo $result;
}
		

?>
