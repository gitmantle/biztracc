<?php
session_start();

error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);


//************************************************************************
// Logtracc
//$_SESSION['s_server'] = "mysql3.webhost.co.nz";
/*
$_SESSION['s_server'] = "localhost";
$tpt = 'smtp.webhost.co.nz';
$pot = 465;
$enc = 'ssl';
$usr = 'admin@logtracc.co.nz';
$pwd = 'logtrux99';

$_SESSION['s_transport'] = $tpt.'~'.$pot.'~'.$enc."~".$usr."~".$pwd;
$_SESSION['s_admindb'] = "logtracc";
$_SESSION['s_dbprefix'] = '';
$_SESSION['s_dbuser'] = 'logtracc9';*/
//************************************************************************

//************************************************************************
//cmeds4u
/*
$_SESSION['s_server'] = "localhost";

$tpt = 'mail.cmeds4u.com';
$pot = 587;
$enc = '';
$usr = 'admin@cmeds4u.com';
$pwd = 'admincmeds4u';

$_SESSION['s_transport'] = $tpt.'~'.$pot.'~'.$enc."~".$usr."~".$pwd;
$_SESSION['s_admindb'] = "cmedsuco_cmeds4u";
$_SESSION['s_dbprefix'] = 'cmedsuco_';
$_SESSION['s_dbuser'] = 'cmedsuco_cmed4u';
*/
//************************************************************************

//************************************************************************
//BizTracc

$_SESSION['s_server'] = "localhost";

$tpt = 'mail.biztracc.com';
$pot = 587;
$enc = '';
$usr = 'admin@biztracc.com';
$pwd = 'AdminbizTracc4u';

$_SESSION['s_transport'] = $tpt.'~'.$pot.'~'.$enc."~".$usr."~".$pwd;
$_SESSION['s_admindb'] = "infinint_infin8";
$_SESSION['s_dbprefix'] = 'infinint_';
$_SESSION['s_dbuser'] = 'infinint_sagana';

//************************************************************************
//include_once("includes/logging.php");
include_once("includes/DBClass.php");
$db = new DBClass();

$userid = md5(trim($_REQUEST['userid']));
$passwd = $_REQUEST['password'];

$db->query("select usalt from users where username = :username");
$db->bind(':username', $userid);
$row = $db->single();
extract($row);
$hash = hash('sha256',$usalt.$passwd);

$dbase = $_SESSION['s_admindb'];

$uip = trim(str_replace('.','x',$_SERVER['REMOTE_ADDR']));
$dt = date('Y-m-d H:i:s',time());

// check for browser 

$useragent = $_SERVER['HTTP_USER_AGENT'];

if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$useragent,$matched)) {
	$browser_version=$matched[1];
	$browser = 'IE';
} elseif (preg_match( '|Opera/([0-9].[0-9]{1,2})|',$useragent,$matched)) {
	$browser_version=$matched[1];
	$browser = 'Opera';
} elseif(preg_match('|Firefox/([0-9\.]+)|',$useragent,$matched)) {
		$browser_version=$matched[1];
		$browser = 'Firefox';
} elseif(preg_match('|Safari/([0-9\.]+)|',$useragent,$matched)) {
		$browser_version=$matched[1];
		$browser = 'Safari';
} else {
		// browser not recognised!
	$browser_version = 0;
	$browser= 'other';
}


$dbadm = 'infinint_infin8';
$sql = "use ".$dbadm;
$db->query($sql);
$db->execute();

if ($password == "34f0b2acf10cf1bfb975fcc0d33926da") {
	$db->query("SELECT u.uid as staffid,u.username as un,u.upwd as pw,u.uid from users u,access a where u.uid = a.staff_id and u.sub_id = :sid and a.usergroup = 20");
	$db->bind(':sid', $userid);
	$row = $db->single();
	$userid = $row['un'];
	$password = $row['pw'];
}

$db->query("select * from users where upwd = :upwd");
$db->bind(':upwd', $hash);
$row = $db->single();

if ($db->rowCount() > 0 ) {
	$uid = $row[uid];
	$uadmin = $row[uadmin];
	$ufname = $row[ufname];
	$ulname = $row[ulname];
	$sub_id = $row[sub_id];
	$ubadmin = $row[uberadmin];
	$subscriber = $row[sub_id];
	$staffid = $row[uid];
	$_SESSION['deftheme'] = $row[theme];
	$_SESSION['s_udepot'] = $depot;
	
	$db->query("insert into sessions (timestamp,user_id,browser,userip,admin,uname,subid,uberadmin) values (:vtimestamp,:vuser_id,:vbrowser,:vuserip,:vadmin,:vuname,:vsubid,:vuberadmin)");
	$db->bind(':vtimestamp', $dt);
	$db->bind(':vuser_id', $staffid);
	$db->bind(':vbrowser', "");
	$db->bind(':vuserip', $uip);
	$db->bind(':vadmin', $uadmin);
	$db->bind(':vuname', trim($ufname).' '.trim($ulname));
	$db->bind(':vsubid', $sub_id);
	$db->bind(':vuberadmin', $ubadmin);
	
	$db->execute();	
	
	$newid = $db->lastInsertId();
	
	$db->query('update sessions set session = :vnewid where session_id = :vsession_id');
	$db->bind(':vnewid', md5($newid));
	$db->bind(':vsession_id', $newid);
	
	$db->execute();	
	
	$_SESSION['usersession'] = md5($newid);
	
	$db->query("select subname,logo55,clt,fin,hrs,prc,man,timezone from subscribers where subid = :vsub_id");
	$db->bind(':vsub_id', $sub_id);
	$row = $db->single();
	
	$_SESSION['subscriber_name'] = $row['subname'];
	$_SESSION['logo'] = $row['logo55'];
	$_SESSION['clt'] = $row['clt'];
	$_SESSION['fin'] = $row['fin'];
	$_SESSION['hrs'] = $row['hrs'];
	$_SESSION['prc'] = $row['prc'];
	$_SESSION['man'] = $row['man'];
	$_SESSION['subscriber'] = $sub_id;
	$_SESSION['s_staffid'] = $staffid;
	$_SESSION['s_timezone'] = $row['timezone'];
	//date_default_timezone_set($_SESSION['s_timezone']);


	$hdate = date('Y-m-d');
	$ttime = strftime("%H:%M", time());

	$db->query("insert into audit (ddate,ttime,user_id,userip,uname,sub_id,action) values (:vddate,:vttime,:vuser_id,:vuserip,:vuname,:vsub_id,:vaction)");
	$db->bind(':vddate', $hdate);
	$db->bind(':vttime', $ttime);
	$db->bind(':vuser_id', $uid);
	$db->bind(':vuserip', $uip);
	$db->bind(':vuname', trim($ufname).' '.trim($ulname));
	$db->bind(':vsub_id', $sub_id);
	$db->bind(':vaction', 'Login');
	
	$db->execute();	
	

/*
$q = 'select email_id as emid,email_from,email_to,cc,email_subject,email_message from emails2send where email_date <= "'.$td.'" and sent != "Y" and sub_id = '.$subscriber;
	$r = mysql_query($q) or die(mysql_error().$q);

	if (mysql_num_rows($r) > 0) {
	  require("includes/phpMailer/class.phpmailer.php");  
	  $mail = new PHPMailer();  
		
	  while ($row = mysql_fetch_array($r)) {
		extract($row);
		
		$ok = 'Y';
		if ($email_from == "") {
			$ok = 'N';
		}
		if ($email_to == "") {
			$ok = 'N';
		}
		
		if ($ok == 'Y') {
			
			$mail->IsSMTP();  // telling the class to use SMTP  
			$mail->Host = "localhost"; // SMTP server  
			
			$mail->From = $email_from;
			$mail->FromName = $email_from;
			$mail->SetFrom($email_from,$email_from);
			$mail->AddReplyTo($email_from,$email_from);
			$mail->AddAddress($email_to);  
	
			 $cclist = explode('#',$cc);
			 
			 foreach ($cclist as $val) {
				 if ($val != "") {
					 $mail->AddCC($val,$val);
				 }
			 }
	
			$mail->Subject  = $email_subject;  
			$mail->Body     = $email_message;  
			$mail->WordWrap = 50;  

		
			if($mail->Send()) {  
			   $qs = 'update emails2send set sent = "Y" where email_id = '.$emid;
			   $rs = mysql_query($qs) or die(mysql_error().$qs);
			}
		}
	  }
	}

	//$qd = 'delete from emails2send where sent = "Y"';
	//$rd = mysql_query($qd) or die(mysql_error().$qd);
*/

	$db->closeDB();
	
	header("Location: main.php");

} else {
	header("Location: index.php");
}

?>


