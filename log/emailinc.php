<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];
$incid = $_REQUEST['id'];
$usersession = $_SESSION['usersession'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$attach = 'emailed/Incident_'.$incid.'_'.$coyid.'.pdf';

$q = "select coyemail from companies where coyid = ".$coyid;
$r = mysql_query($q) or die(mysql_error().$q);
$qrow = mysql_fetch_array($r);
extract($qrow);
$emfrom = $coyemail;

date_default_timezone_set($_SESSION['s_timezone']);

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select * from incidents where uid = ".$incid;
$r = mysql_query($q) or die(mysql_error().$q);
$qrow = mysql_fetch_array($r);
extract($qrow);
$dr = $client;
$ac = $accountno;
$sb = $sub;
$subject = $_SESSION['s_coyname'].' Incident '.$uid.' Ref: '.$ref;
$mess = "Attached please find completed incident report for ".$subject;

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate debtors drop down
$query = "SELECT concat(t1.firstname,' ',t1.lastname) as fname, t1.member_id, t2.drno, t2.drsub, t3.comm
FROM members as t1, client_company_xref as t2, comms as t3 where
t1.member_id = t2.client_id and t2.drno > 0 and
t1.member_id = t3.member_id and t2.company_id = ".$coyid." and t2.drno != 0
 and t3.comms_type_id = 4"; 
$result = mysql_query($query) or die(mysql_error().$query);
$debtors_options = "<option value=\"0\">Select Customer</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($drno == $ac && $drsub == $sb) {
		$selected = 'selected="selected"';
		$cemailad = $comm;
	} else {
		$selected = '';
	}
	$debtors_options .= '<option value="'.$member_id.'~'.$comm.'"'.$selected.'>'.trim($fname).'</option>';
}

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

$config['general.engine'] = 'GoogleSpell';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Email Incident</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>

<script>


window.name = 'emailinc';

function showcc() {
	var x=document.getElementById("ccemail");
	var listlength = document.getElementById("ccemail").length;
	for (var i = 0; i < listlength; i ++) {
		x.remove(x[i]);
	}
	var x=document.getElementById("bccemail");
	var listlength = document.getElementById("bccemail").length;
	for (var i = 0; i < listlength; i ++) {
		x.remove(x[i]);
	}
	
	var mto = document.getElementById('emaddress').value;
	var sp = mto.split('~');
	var mailto = sp[0];
	var mailad = sp[1];
	$.get("includes/ajaxGetccList.php", {mailto: mailto}, function(data){
		$("#ccemail").append(data);
		$("#bccemail").append(data);
		document.getElementById('cltemailaddress').value = mailad;

	});

}

function attachdoc(d) {
	var attlist = document.getElementById('attfile').value;
	var docfile = "../documents/clients/"+d;
	if (attlist  == "") {
		document.getElementById('attfile').value = docfile;
	} else {
		document.getElementById('attfile').value = attlist+"#"+docfile;
	}
}

function attachdocb(d) {
	var attlist = document.getElementById('attfile').value;
	var docfile = "../documents/attachments/"+d;
	if (attlist  == "") {
		document.getElementById('attfile').value = docfile;
	} else {
		document.getElementById('attfile').value = attlist+"#"+docfile;
	}
}

function addto(emailid,email) {
	document.getElementById('emaddress').value = email;
}


function addcc(recipient) {
	var clst = document.getElementById('cclist').value;
	document.getElementById('cclist').value = clst + recipient + ';';
}
	
function addbcc(recipient) {
	var bclst = document.getElementById('bcclist').value;
	document.getElementById('bcclist').value = bclst + recipient + ';';
}


</script>

</head>

<body>
<div id="bwin">

<form name="form1" id="form1" method="post" >
 <input type="hidden" name="attachment" id="attachment" >

  <table width="700" border="0">
    <tr>
      <td class="boxlabel">Email to:-</td>
      <td align="left"><select name="emaddress" id="emaddress" onclick="showcc();"><?php echo $debtors_options; ?></select></td>
      <td align="left"><label>
        <input type="text" name="cltemailaddress" id="cltemailaddress" size="60" value="<?php echo $cemailad; ?>"/>
      </label></td>
    </tr>
    <tr>
      <td class="boxlabel">CC:-</td>
      <td align="left"><select name="ccemail" id="ccemail" onChange="addcc(this.value);"></select></td>
      <td align="left"><label>
        <input type="text" name="cclist" id="cclist" size="80"/>
      </label></td>
    </tr>
    <tr>
      <td class="boxlabel">BCC:-</td>
      <td align="left"><select name="bccemail" id="bccemail" onChange="addbcc(this.value);"></select></td>
      <td align="left"><label>
        <input type="text" name="bcclist" id="bcclist" size="80"/>
      </label></td>
    </tr>
    <tr>
      <td class="boxlabel">Subject:-</td>
      <td colspan="2" align="left"><input name="emsubject" type="text" id="emsubject" size="90" readonly value="<?php echo $subject; ?>" /></td>
    </tr>
    <tr>
      <td class="boxlabel">From:-</td>
      <td colspan="2" align="left"><input type="text" name="emfrom" id="emfrom" size="80" readonly value="<?php echo $emfrom; ?>"/></td>
    </tr>
    <tr>
      <td class="boxlabel">Message:-</td>
      <td colspan="2" align="left"><textarea name="emtext" id="emtext" cols="90" rows="7"><?php echo $mess; ?></textarea></td>
    </tr>
    <tr>
      <td class="boxlabel">Attachment</td>
      <td colspan="2" align="left"><input type="text" size="80" name="attfile" id="attfile"  readonly value="<?php echo $attach; ?>"/></td>
    </tr>
    <tr>
      <td colspan="3">
		<?php //include "getattach.php" ?>
	</td>
    <tr>
      <td colspan="3"><input type="submit" name="emsend" id="emsend" value="Send" /></td>
    </tr>
  </table>

</form>

<script>
	document.getElementById('emaddress').click();

</script>

</div>


<?php

	if(isset($_POST['emsend'])) {


		$subject = $_REQUEST['emsubject'];
		$mess = $_REQUEST['emtext'];
		$to = trim($_REQUEST['cltemailaddress']);
		$from = $_REQUEST['emfrom']; 
		$attfile = $_REQUEST["attfile"];
		$ccemail = $_REQUEST["cclist"];
		$bccemail = $_REQUEST["bcclist"];

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
		$message->setSubject($subject);
		$message->setFrom(array($from));
		$message->setTo(array($to));
		$mstring = "Dear Sir/Madam\r\n\r\n".$mess."\r\n\r\n";
		$message->setBody($mstring,'text/plain');

		$cc = explode(';',$ccemail);
			 
		foreach ($cc as $val) {
			if ($val != "") {
				 $message->addCc(trim($val));
			}
		}
			 
		$bcc = explode(';',$bccemail);
			 
		foreach ($bcc as $val) {
			if ($val != "") {
				$message->addBcc(trim($val));
			}
		}

		$message->attach(Swift_Attachment::fromPath($attfile));

		$result = $mailer->send($message);

		if ($result > 0) {
			$ret = "Email sent";
		} else {
			$ret = $result;
		}
		echo '<script>';
		echo 'alert("'.$ret.'");';
		echo 'this.close();';
		echo '</script>';

	}

?>


</body>
</html>