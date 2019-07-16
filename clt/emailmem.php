<?php
session_start();
//error_reporting(0);
$usersession = $_SESSION['usersession'];

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

$uid = $user_id;

$db->query("select uemail as staffemail from users where uid = ".$uid);
$qrow = $db->single();
extract($qrow);

$db->query("select subemail as genericemail from subscribers");
$qrow = $db->single();
extract($qrow);

$cltdb = $_SESSION['s_cltdb'];

$commsid = $_REQUEST['commsid'];

$_SESSION['s_ememberid'] = $memberid;

$db->query("select * from ".$cltdb.".comms where ".$cltdb.".comms_id = ".$commsid);
$row = $db->single();
extract($row);

if ($comms_type_id == 4) {
	$emailaddress = $comm;
} else {
	echo "<script>";
	echo "alert('That was not an email address');";
	echo "this.close();";
	echo "</script>";
}


$emfrom_options = '<option value="'.$staffemail.'">'.$staffemail.'</option>';
$emfrom_options .= '<option value="'.$genericemail.'">'.$genericemail.'</option>';

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

$config['general.engine'] = 'GoogleSpell';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Email Member</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>


<!-- TinyMCE -->
<script type="text/javascript" src="../includes/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		skin : "o2k7",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups,autosave",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,image,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example word content CSS (should be your site CSS) this one removes paragraph margins
		content_css : "css/word.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>
<!-- /TinyMCE -->


<script>


window.name = 'emailmem';

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


function addcc(emailid,recipient) {
	var cc = document.getElementById('ccemail').value + '; ' + recipient;
	document.getElementById('ccemail').value = cc;
	var cclist = document.getElementById('cclist').value + '#' + emailid;
	document.getElementById('cclist').value = cclist;
}
	
function addbcc(emailid,recipient) {
	var cc = document.getElementById('bccemail').value + '; ' + recipient;
	document.getElementById('bccemail').value = cc;
	var cclist = document.getElementById('bcclist').value + '#' + emailid;
	document.getElementById('bcclist').value = cclist;
}

function addrecipient() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('addrecipient.php?from=e','addrecip','toolbar=0,scrollbars=1,height=150,width=700,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				


</script>

</head>

<body>
<div id="bwin">

<form name="form1" id="form1" method="post" >
 <input type="hidden" name="attachment" id="attachment" >
 <input type="hidden" name="cclist" id="cclist" value="" >
 <input type="hidden" name="bcclist" id="bcclist" value="" >

  <table width="700" border="0">
    <tr>
      <td class="boxlabel">Email to:-</td>
      <td align="left"><input name="emaddress" type="text" id="emaddress" value="<?php echo $emailaddress; ?>" size="60" /></td>
      <td align="left"><?php include "gettoemail.php" ?></td>
    </tr>
    <tr>
      <td class="boxlabel">CC:-</td>
      <td align="left"><input name="ccemail" type="text" id="ccemail" size="60" /></td>
      <td align="left"><?php include "getccemail.php" ?></td>
    </tr>
    <tr>
      <td class="boxlabel">BCC:-</td>
      <td align="left"><input name="bccemail" type="text" id="bccemail" size="60" /></td>
      <td align="left"><?php include "getbccemail.php" ?></td>
    </tr>
    <tr>
      <td class="boxlabel">Subject:-</td>
      <td colspan="2" align="left"><input name="emsubject" type="text" id="emsubject" size="90" /></td>
    </tr>
    <tr>
      <td class="boxlabel">From:-</td>
      <td colspan="2" align="left"><select name="emfrom" id="emfrom">
      	<?php echo $emfrom_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Message:-</td>
      <td colspan="2" align="left"><textarea name="emtext" id="emtext" cols="90" rows="7"></textarea></td>
    </tr>
    <tr>
      <td class="boxlabel">Attachment</td>
      <td colspan="2" align="left"><input type="text" size="122" name="attfile" id="attfile"  readonly/></td>
    </tr>
    <tr>
      <td colspan="3">
		<?php include "getdocs.php" ?>
	</td>
    </tr>
    <tr>
      <td colspan="3">
		<?php include "getattach.php" ?>
	</td>
    </tr>
    <tr>
      <td colspan="3"><input type="submit" name="emsend" id="emsend" value="Send" /></td>
    </tr>
  </table>

</form>
</div>


<?php

include_once("../includes/DBClass.php");
$db = new DBClass();

	if(isset($_POST['emsend'])) {


		$subject = $_REQUEST['emsubject'];
		$message = $_REQUEST['emtext'];
		$to = $_REQUEST['emaddress'];
		$from = $_REQUEST['emfrom']; 
		$attfile = $_REQUEST["attfile"];
		$ccemail = substr($_REQUEST["cclist"],1);
		$bccemail = substr($_REQUEST["bcclist"],1);



    require_once('../includes/rmail/Rmail.php');
    
    $mail = new Rmail();

    /**
    * Set the from address of the email
    */
    $mail->setFrom($from);
    
    /**
    * Set the subject of the email
    */
    $mail->setSubject($subject);
    
    /**
    * Set high priority for the email. This can also be:
    * high/normal/low/1/3/5
    */
    $mail->setPriority('normal');

    /**
    * Set the text of the Email
    */
    //$mail->setText('My Sample text to see what is sent through.');
    
    /**
    * Set the HTML of the email. Any embedded images will be automatically found as long as you have added them
    * using addEmbeddedImage() as below.
    */
	$message = nl2br($message);
	
	// see if there is a signature file
	$fl = "../documents/signatures/sig_".$user_id.".jpg";
	$file = "sig_".$user_id.".jpg";
	if (file_exists($fl)) {
		$mail->setHTML($message.'<br><br><img src="'.$file.'">');
		$mail->addEmbeddedImage(new fileEmbeddedImage($fl));
	} else {
		$mail->setHTML($message);
	}
    
    /**
    * Add an attachment to the email.
    */
    //$mail->addAttachment(new fileAttachment('example.zip'));
	
		 $attlist = explode('#',$attfile);
		 foreach ($attlist as $val) {
			 $mail->addAttachment(new fileAttachment($val));
		 }
		 
		 $cclist = explode('#',$ccemail);
		 
		 foreach ($cclist as $val) {
			 if ($val != "") {
				 $db->query('select email,recipient from '.cltdb.'.subemails where subemail_id = '.$val);
				 $row = $db=->single();
				 extract($row);
				 $mail->setCc($email);
			 }
		 }
		 
		 $bcclist = explode('#',$bccemail);
		 
		 foreach ($bcclist as $val) {
			 if ($val != "") {
				 $db->query('select email,recipient from '.cltdb.'.subemails where subemail_id = '.$val);
				 $row = $db->single();
				 extract($row);
				 $mail->setBcc($email);
			 }
		 }
		 
$db->closeDB();

    /**
    * Send the email. Pass the method an array of recipients.
    */
    $address = $to;
    $result  = $mail->send(array($address));
	
	if ($result == 1) {
		$ret = "Email sent to ".$address;
		
		date_default_timezone_set($_SESSION['s_timezone']);

		$hdate = date("Y-m-d");
		$ttime = strftime("%H:%M", time());
		
		include_once("includes/cltadmin.php");
		$oAct = new cltadmin;	

		$oAct->client_id = $memberid;
		$oAct->emdate = $hdate;
		$oAct->emtime = $ttime;
		$oAct->emfrom = $from;
		$oAct->emsubject = $subject;
		$oAct->emmessage = preg_replace('#<br\s*?/?>#i', "\n", $message);
		$oAct->staff_id = $user_id;
		$oAct->sub_id = $sub_id;

		$oAct->AddEmail();
		
	} else {
		$ret = "Email not sent";	
	}

		echo '<script>';
		echo 'alert("'.$ret.'");';
		echo 'this.close();';
		echo '</script>';
	}


?>


</body>
</html>