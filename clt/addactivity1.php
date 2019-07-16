<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$userid = $user_id;

$cluid = $_SESSION["s_memberid"];

// populate  staff drop down
$query = "select * from users where sub_id = ".$subid." order by ulname";
$result = mysql_query($query) or die(mysql_error().' '.$query);
$staff_options = '<option value="0">Select User</option>';
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$staff_options .= '<option value="'.$row[2].' '.$row[3].'">'.$row[2].' '.$row[3].'</option>';
}


$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());


// populate activity status types drop down
$query = "select * from activity_status";
$result = mysql_query($query) or die(mysql_error());
$act_options = "";
while ($row = mysql_fetch_array($result)) {
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

	 $(document).ready(function(){
		$('#ddate').datepicker({ dateFormat: "dd/mm/yy", yearRange: "-5:+5", showOn: "button", buttonImage: "../images/calendar.gif", buttonImageOnly: true, altField: "#ddateh", altFormat: "yy-mm-dd"});
	 });
	 $(document).ready(function(){
		$('#cdate').datepicker({ dateFormat: "dd/mm/yy", yearRange: "-5:+5", showOn: "button", buttonImage: "../images/calendar.gif", buttonImageOnly: true, altField: "#cdateh", altFormat: "yy-mm-dd"});
	 });

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
    <input type="hidden" name="cleanactivity" id="cleanactivity" value="N">
    <input type="hidden" name="sendbutton" id="sendbutton" value="N">
    <input type="hidden" name="ddateh" id="ddateh" value="<?php echo $hdate; ?>">
    <input type="hidden" name="cdateh" id="cdateh" value="<?php echo $chdate; ?>">
  <table width="950" border="0" align="left">
    <tr>
      <td colspan="2" align="center" ><u>Add Note</u></td>
    </tr>
    <tr>
      <td class="boxlabel">Date</td>
      <td><input name="ddate" type="text" id="ddate"  size="15" maxlength="15" value="<?php echo $ddate; ?>"></td>
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
<?php

	if($_REQUEST['savebutton'] == "Y") {

		  include_once("../includes/cltadmin.php");

		  $oAct = new cltadmin;	

		  $oAct->client_id = $cluid;
		  $oAct->ddate = $_REQUEST['ddateh'];
		  $oAct->ttime = $_REQUEST['ttime'];
		  $oAct->activity = $_REQUEST['cleanactivity'];
		  $oAct->status = 'Sealed';
		  $oAct->staff_id = $userid;
		  $oAct->sub_id = $sub_id;
		  $oAct->contact = $_REQUEST['contact'];

		  $actid = $oAct->AddActivity();

		  $dt = date('Y-m-d H:i:s',time());

		  $query = "insert into audit (ddate,ttime,user_id,uname,sub_id,member_id,activities_id,action) values ";
		  $query .= "('".$hdate."',";
		  $query .= "'".$ttime."',";
		  $query .= $user_id.",";
		  $query .= '"'.$uname.'",';
		  $query .= $sub_id.",";
		  $query .= $cluid.",";
		  $query .= $actid.",";
		  $query .= "'Add Note')";

		  $result = mysql_query($query) or die(mysql_error().$query);
		  
		  // if to save as task
		  if ($_REQUEST['ltask'] == 'Y') {
			  $edate = date('Y-m-d');
			  $estaff = $uname;
			  $dstaff = $_REQUEST['lstaff'];
			  $completeby = $_REQUEST['cdateh'];
			  $task = $_REQUEST['cleanactivity'];
			  $category = $_REQUEST['category'];
			  $subid = $subscriber;
			  
			  $q = 'insert into todo (enter_date,enter_staff,todo_by,complete_by,task,category,sub_id) values (';
			  $q .= '"'.$edate.'","'.$estaff.'","'.$dstaff.'","'.$completeby.'","'.$task.'","'.$category.'",'.$subid.')';
			  $r = mysql_query($q) or die(mysql_error().$q);
		  }
		  
		  

		if($_REQUEST['sendbutton'] == "Y") {
			$q = "select email as emailfrom from staff where staff_id = ".$user_id;
			$result = mysql_query($query) or die(mysql_error().$query);
			$row = mysql_fetch_array($result);
			extract($row);

			$subject = 'Note';
			$message = $_REQUEST['activity'];
			$to = $_REQUEST['temailto'];
			$from = $emailfrom; 

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
		}


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
