<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$userid = $row['user_id'];
$uname = $row['uname'];

$cluid = $_SESSION["s_memberid"];

// populate  staff drop down
$db->query("select * from users where sub_id = :subid order by ulname");
$db->bind(':subid', $subid);
$rows = $db->resultset();
$staff_options = '<option value="0">Select</option>';
foreach ($rows as $row) {
	extract($row);
	if ($row['uid'] == $userid) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$staff_options .= '<option value="'.$row['ufname'].' '.$row['ulname'].'"'.$selected.'>'.$row['ufname'].' '.$row['ulname'].'</option>';
}

$cltdb = $_SESSION['s_cltdb'];

// populate activity status types drop down
$db->query("select * from ".$cltdb.".activity_status");
$rows = $db->resultset();
$act_options = "";
foreach ($rows as $row) {
	extract($row);
	$act_options .= "<option value=\"".$activity_status."\">".$activity_status."</option>";
}

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");
$ttime = strftime("%H:%M", time());
$chdate = date("Y-m-d");

// populate reason list
    $arr = array('Other','First Appointment Booked', 'Appointment', 'Phone In', 'Phone Out', 'Query', 'Visit', 'Informative');
	$reason_options = "";
    for($i = 0; $i < count($arr); $i++)	{
		$reason_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>
<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Note</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
</head>
<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}


function post() {

	//add validation here if required.

	var activity = document.getElementById('activity').value;
	var ok = "Y";
	if (activity == "") {
		alert("Please enter an activity.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		if (document.getElementById('cbsend').checked == true) {
			var esend = document.getElementById('temailto').value;
			if (esend == "") {
				alert("Please enter an email address to send to.");
				ok = "N";
				return false;
			} else {
				alert('sending mail');
				document.getElementById('sendbutton').value = "Y";
			}
		}

		if (ok == "Y") {
			document.getElementById('savebutton').value = "Y";
			//document.getElementById('cleanactivity').value = CleanWordHTML( activity );
			document.getElementById('form1').submit();
		}
	}

}

</script>
<body>
  <form name="form1" id="form1" method="post" >
    <input type="hidden" name="savebutton" id="savebutton" value="N">
    <input type="hidden" name="sendbutton" id="sendbutton" value="N">
  <table width="950" border="0"  align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u>Add Note</u></label></td>
    </tr>
    <tr>
      <td class="boxlabel">Date</td>
    <td><input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $hdate; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
    </tr>
    <tr>
      <td class="boxlabel">Time</td>
      <td><input name="ttime" type="text" id="ttime"  size="15" maxlength="15" value="<?php echo $ttime; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Note</td>
      <td style="font-size: 100%; font-family:Tahoma, Geneva, sans-serif"><textarea name="activity" id="activity" cols="90" rows="15"></textarea></td>
    </tr>
    <tr>
      <td class="boxlabel">Reason</td>
      <td><select name="contact" id="contact">
        <?php echo $reason_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Email Note to:-</td>
      <td class="boxlabelleft"><input type="text" name="temailto" id="temailto" size="70">
        <input type="checkbox" name="cbsend" id="cbsend">
        Tick box to email to addressee.</td>
    </tr>
    <tr>
      <td class="boxlabel">Save Note as Task</td>
      <td class="boxlabelleft"><select name="ltask" id="ltask" >
        <option value="N">No</option>
        <option value="Y">Yes</option>
      </select>
        &nbsp; Task to:-&nbsp;
        <select name="lstaff" id="lstaff">
          <?php echo $staff_options; ?>
        </select>
        <span class="boxlabel">Category
          <select name="category" id="category">
            <option value="Standard">Standard</option>
            <option value="Low Priority">Low Priority</option>
            <option value="High Priority">High Priority</option>
            <option value="Urgent">Urgent</option>
          </select>
          Complete by<span style="font-size: 100%; font-family:Tahoma, Geneva, sans-serif">
            <input name="cdate" type="text" id="cdate"  size="15" maxlength="15" value="<?php echo $ddate; ?>">
          </span></span></td>
    </tr>
    <tr>
      <td >&nbsp;</td>
      <td align="right"><input type="button" value="Save" name="save"  onClick="post()" ></td>
    </tr>
  </table>
  </form>
<script>document.onkeypress = stopRKey;</script>
<script>
		document.getElementById('activity').focus()

	</script>
    
 <script>
 	document.getElementById("ddate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>    
    
<?php

	if($_REQUEST['savebutton'] == "Y") {

		  include_once("../includes/cltadmin.php");

		  $oAct = new cltadmin;	

		  $oAct->client_id = $cluid;
		  
		  $odt = $_REQUEST['ddate'];
		  $t = explode('/',$odt);
		  $d = $t[0];
		  if (strlen($d) == 1) {
			$d = '0'.$d;
		  }
		  $m = $t[1];
		  if (strlen($m) == 1) {
			$m = '0'.$m;
		  }
		  $y = $t[2];
		  $ddate = $y.'-'.$m.'-'.$d;		  
		  $oAct->ddate = $ddate;

		  $oAct->ttime = $_REQUEST['ttime'];
		  $oAct->activity = $_REQUEST['activity'];
		  $oAct->status = 'Sealed';
		  $oAct->staff_id = $userid;
		  $oAct->sub_id = $sub_id;
		  $oAct->contact = $_REQUEST['contact'];

		  $actid = $oAct->AddActivity();

		  $dt = date('Y-m-d H:i:s',time());

			include_once("../includes/DBClass.php");
			$dba = new DBClass();

			  $dba->query("insert into ".$cltdb.".audit (ddate,ttime,user_id,uname,sub_id,member_id,activities_id,action) values (:ddate,:ttime,:user_id,:uname,:sub_id,:member_id,:activities_id,:action)");
			  $dba->bind(':ddate', $hdate);
			  $dba->bind(':ttime', $ttime);
			  $dba->bind(':user_id', $userid);
			  $dba->bind(':uname', $uname);
			  $dba->bind(':sub_id', $subid);
			  $dba->bind(':member_id', $cluid);
			  $dba->bind(':activities_id', $actid);
			  $dba->bind(':action', 'Add Note');
			
			$dba->execute();
		  
		  // if to save as task
		  if ($_REQUEST['ltask'] == 'Y') {
			  $edate = date('Y-m-d');
			  $estaff = $uname;
			  $dstaff = $_REQUEST['lstaff'];
			  $completeby = $_REQUEST['cdateh'];
			  $task = $_REQUEST['activity'];
			  $category = $_REQUEST['category'];
			  $subid = $subscriber;
			  
			  $dba->query('insert into '.$cltdb.'.todo (enter_date,enter_staff,todo_by,complete_by,task,category) values (:enter_date,:enter_staff,:todo_by,:complete_by,:task,:category)');
			  $dba->bind(':enter_date', $edate);
			  $dba->bind(':enter_staff', $estaff);
			  $dba->bind(':todo_by', $dstaff);
			  $dba->bind(':completge_by', completeby);
			  $dba->bind(':task', $task);
			  $dba->bind(':category', $category);
			  
			  $dba->execute();
			  
		  }
		  
		if($_REQUEST['sendbutton'] == "Y") {
			$dba->query("select uemail as emailfrom from users where uid = ".$userid);
			$row = $dba->single();
			extract($row);

			$subject = 'Note';
			$message = $_REQUEST['activity'];
			$to = $_REQUEST['temailto'];
			$from = $emailfrom; 
			$host = $_SESSION['s_server'];

			 require("../includes/phpMailer/class.phpmailer.php");  
			 $mail = new PHPMailer();  

			 $mail->IsSMTP();  // telling the class to use SMTP  
			 $mail->Host     = $host; // SMTP server  

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
		}

		$dba->closeDB();

		  ?>
<script>

		  window.open("","editmembers").jQuery("#mactivitylist").trigger("reloadGrid");
		  this.close();

		  </script>
<?php

	  }

?>
</body>
</html>
