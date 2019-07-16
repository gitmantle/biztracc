<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Test Swift Email</title>
</head>

<body>

<?php
require_once '../includes/swift_email/swift_required.php';

$transport = Swift_SmtpTransport::newInstance('smtp.webhost.co.nz', 25);
$mailer = Swift_Mailer::newInstance($transport);

$message = Swift_Message::newInstance();
$message->setSubject('Test Invoice');
$message->setFrom(array('test@gmail.com' => 'Test'));
$message->setTo(array('mrsagana@gmail.com' => 'Murray'));
$message->setBody('This is the test message from me');

$result = $mailer->send($message);

?>




</body>
</html>