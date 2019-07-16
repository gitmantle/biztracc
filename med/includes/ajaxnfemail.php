<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$usersession = $_SESSION['usersession'];

require_once('../../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$nftable = 'ztmp'.$user_id.'_nofunds';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select email as coyemail from globals";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$moduledb = $_SESSION['s_prcdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select * from ".$nftable." where email <> ''";
$r = mysql_query($q) or die(mysql_error().' '.$q);
while ($row = mysql_fetch_array($r)) {
	extract($row);
	
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

		$subject = 'Insufficient funds';
		$mess = 'Unfortunately you have insufficient credit with us to enable us to fulfill the next period. Please top up your account or contact us to discuss the matter.';
		$to = trim($email);
		$from = $coyemail; 
	
		require_once '../../includes/swift_email/swift_required.php';
								
		// Create the Transport
		$transport = Swift_SmtpTransport::newInstance('mail.cmeds4u.com', 587)
		  ->setUsername('admin@cmeds4u.com')
		  ->setPassword('admincmeds4u')
		  ;
		$mailer = Swift_Mailer::newInstance($transport);
		
								
		$message = Swift_Message::newInstance();
		$message->setSubject($subject);
		$message->setFrom(array($from));
		$message->setTo(array($email));
		$mstring = "Dear ".$member."\r\n\r\n".$mess."\r\n\r\n";
		$message->setBody($mstring,'text/plain');

		$result = $mailer->send($message);
	
		if ($result > 0) {
			$ret = " Email sent - ".$member.", ";
		} else {
				$ret = $result;
		}
		echo $ret;
		
	} 

}

$q = "select * from ".$nftable." where mobile <> ''";
$r = mysql_query($q) or die(mysql_error().' '.$q);
while ($row = mysql_fetch_array($r)) {
	extract($row);
	
		

}

		echo "This will need to be set up with an SMS gateway for which charges apply";

?>