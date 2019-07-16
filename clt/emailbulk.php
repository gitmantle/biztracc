<?php
ini_set('display_errors', true);
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

$cltdb = $_SESSION['s_cltdb'];

$q = "select email as genericemail from subscribers where sub_id = ".$subid;
$r = mysql_query($q) or die(mysql_error().$q);
$qrow = mysql_fetch_array($r);
extract($qrow);

$q = "select email as staffemail from users where uid = ".$user_id;
$r = mysql_query($q) or die(mysql_error().$q);
$qrow = mysql_fetch_array($r);
extract($qrow);


$emfrom_options = '<option value="'.$staffemail.'">'.$staffemail.'</option>';
$emfrom_options .= '<option value="'.$genericemail.'">'.$genericemail.'</option>';

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

$db->closeDB();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Bulk Email Members</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>


<script>
function attachdocb(d) {
	var attlist = document.getElementById('attfile').value;
	var docfile = "../documents/attachments/"+d;
	if (attlist  == "") {
		document.getElementById('attfile').value = docfile;
	} else {
		document.getElementById('attfile').value = attlist+"#"+docfile;
	}
}

function mailmerge() {
  var filterfile = "<?php echo $filterfile; ?>";
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
	window.open('mailmerge.php?filterfile='+filterfile,'mmerge','toolbar=0,scrollbars=1,height=500,width=1020,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}




</script>



</head>

<body>

<form name="form1" id="form1" method="post" >
  <table width="700" border="1">
    <tr>
      <td width="71" class="boxlabel">Subject:-</td>
      <td width="620" align="left"><input name="emsubject" type="text" id="emsubject" size="100" /></td>
    </tr>
    <tr>
      <td class="boxlabel">From:-</td>
      <td align="left"><select name="emfrom" id="emfrom">
      	<?php echo $emfrom_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Message:-</td>
      <td align="left"><textarea name="emtext" id="emtext" cols="100" rows="17"></textarea></td>
    </tr>
    <tr>
      <td class="boxlabel">Attachment</td>
      <td align="left"><input name="attfile" type="text" id="attfile" size="122" readonly /></td>
    </tr>
    <tr>
      <td colspan="2"> 
           	<?php include "getattach.php" ?> 
	</td>
    </tr>
    <tr>
      <td colspan="2" align="right"><input type="submit" name="emsend" id="emsend" value="Send" /></td>
    </tr>
  </table>

	 <?
        require_once "../includes/phpspellcheck/include.php";	//   "phpspellcheck/include.php" // Full file path to the include.php file in the phpspellcheck Folder
        $mySpell = new SpellCheckButton();
        $mySpell->InstallationPath = "../includes/phpspellcheck/";	  // "/phpspellcheck/" //  Relative URL of phpspellcheck within your site
        $mySpell->Fields = "ALL";
        echo $mySpell->SpellImageButton();
        
        $mySpell = new SpellAsYouType();
        $mySpell->InstallationPath = "../includes/phpspellcheck/"; // "/phpspellcheck/" // Relative URL of phpspellcheck within your site
        $mySpell->Fields = "ALL";
        echo $mySpell->Activate();
    ?>


</form>



<?php

	if(isset($_POST['emsend'])) {

		$subject = $_REQUEST['emsubject'];
		$message = $_REQUEST['emtext'];
		$from = $_REQUEST['emfrom']; 
		$attfile = $_REQUEST["attfile"];

		 require("../includes/phpMailer/class.phpmailer.php");  
		   
		 $mail = new PHPMailer();  
		 
		 $mail->IsSMTP();  // telling the class to use SMTP  
		 $mail->Host     = "localhost"; // SMTP server  
		  
		 $mail->From = $from;
		 $mail->FromName = $uname;
		 $mail->SetFrom($from,$uname);
		 $mail->AddReplyTo($from,$uname);

		foreach ($arrchosen as $mem) {
			$query = "update ".$filterfile." set del = 'N' where member_id = ".$mem;
			$result = mysql_query($query) or die(mysql_error().$query);
		}


		 $query = "select email,lastname,firstname,member_id,del from ".$filterfile." where del = 'N'";
		 $result = mysql_query($query) or die(mysql_error());
		 while ($row = mysql_fetch_array($result)) {
			extract($row);
			if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
				$name = trim($firstname.' '.$lastname);
				$mail->AddBCC($email,$name);
			}
		 }

		 $mail->Subject  = $subject;  
		 $mail->Body     = $message; 
		 
		 $attlist = split('#',$attfile);
		 foreach ($attlist as $val) {
			 $mail->AddAttachment($val);
		 }
		 
		 $mail->WordWrap = 50;  

		 if(!$mail->Send()) {  
		   $retmsg = 'Message was not sent.'.' Mailer error: ' . $mail->ErrorInfo; 

		 } else {  
		   $retmsg = 'Message has been sent';  
			date_default_timezone_set("Pacific/Auckland");
		   $hdate = date("Y-m-d");
		   $ttime = strftime("%H:%M", mktime());
		  
		   include_once("includes/cltadmin.php");
		   $oAct = new cltadmin;	

		   $query = "select email,lastname,firstname,member_id from ".$filterfile;
		   $result = mysql_query($query) or die(mysql_error());
		   while ($row = mysql_fetch_array($result)) {
				extract($row);
				  
				  $oAct->client_id = $member_id;
				  $oAct->emdate = $hdate;
				  $oAct->emtime = $ttime;
				  $oAct->emfrom = '';
				  $oAct->emsubject = $subject;
				  $oAct->emmessage = $message;
				  $oAct->staff_id = $user_id;
				  $oAct->sub_id = $sub_id;
		
				  $oAct->AddEmail();
				  

		   }
	   
		 }  

		echo '<script>';
		echo 'alert("'.$retmsg.'");';
		echo 'this.close();';
		echo '</script>';

	}


?>


</body>
</html>