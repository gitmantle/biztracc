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

$acuid = $_REQUEST['uid'];

$cltdb = $_SESSION['s_cltdb'];

$db->query("select * from ".$cltdb.".activities where activities_id = ".$acuid);
$row = $db->single();
extract($row);
$acttype = $status;
$reason = $contact;
$note = str_replace("\'","'",$activity);
$memid = $member_id;

if ($ddate == '0000-00-00') {
	$fddate = date("d/m/Y");
	$ddate = date("Y-m-d");
	$fdateh = date("Y-m-d");
} else {
	$dt = explode('-',$ddate);
	$y = $dt[0];
	$m = $dt[1];
	$d = $dt[2];
	$fdt = mktime(0,0,0,$m,$d,$y);
	$fddate = date("d/m/Y",$fdt);
	$fdateh = date("Y-m-d", $fdt);
}


// populate activity status type drop down
$db->query("select * from ".$cltdb.".activity_status");
$rows = $db->resultset();
$acttype_options = "<option value=\"\">Select Status</option>";
foreach ($rows as $row) {
	extract($row);
	if ($activity_status == $acttype) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$acttype_options .= "<option value=\"".$activity_status."\" ".$selected.">".$activity_status."</option>";
}

// populate reason list
    $arr = array('Other','First Appointment Booked', 'Appointment', 'Phone In', 'Phone Out', 'Query', 'Visit', 'Informative');
	$reason_options = "";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $reason) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
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
<title>Edit Note</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
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
	 
	 
</script>

<body>
<div id="bwin">
<form name="form1" method="post" >
  <table width="950" border="0" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;"><u>View Note </u></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><div align="right">Date</div></td>
      
      <?php 
	  $dd = "'ddate'";
      if ($acttype == 'Sealed') {
      	echo '<td><input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $fdateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>';
     	echo '</tr>';
    	echo '<tr>';
     	echo '<td class="boxlabel"><div align="right">Time</div></td>';
      	echo '<td><input name="ttime" type="text" id="ttime"  size="15" maxlength="15" readonly value="'.$ttime.'"></td>';
     	echo '</tr>';
    	echo '<tr>';
      	echo '<td class="boxlabel">Note</td>';
      	echo '<td style="font-size: 100%; font-family:Tahoma, Geneva, sans-serif;"><textarea name="activity" id="activity" cols="100" rows="17" readonly>'.trim($note).'</textarea></td>';
    	echo '</tr>';
    	echo '<tr>';
      	echo '<td class="boxlabel">Reason</td>';
      	echo '<td><input name="contact" value="'.$contact.'" readonly ></td>';
		
      } else {
      	echo '<td><input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $fdateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>';
     	echo '</tr>';
    	echo '<tr>';
     	echo '<td class="boxlabel"><div align="right">Time</div></td>';
      	echo '<td><input name="ttime" type="text" id="ttime"  size="15" maxlength="15" value="'.$ttime.'"></td>';
     	echo '</tr>';
    	echo '<tr>';
      	echo '<td><div align="right">Note</div></td>';
      	echo '<td style="font-size: 100%; font-family:Tahoma, Geneva, sans-serif;"><textarea name="activity" id="activity" cols="100" rows="17">'.trim($note).'</textarea></td>';
    	echo '</tr>';
    	echo '<tr>';
      	echo '<td class="boxlabel"><div align="right">Status</div></td>';
      	echo '<td><select name="activity_status" id="activity_status">'.$acttype_options.'</select></td>';
		echo '</tr>';
    	echo '<tr>';
      	echo '<td class="boxlabel">Reason</td>';
      	echo '<td><select name="contact" id="contact">'.$reason_options.'</select></td>';
      }
      ?>
      
    </tr>	 
  </table>
</form>
</div>

	<script>document.onkeypress = stopRKey;</script> 
    
  <script>
 	document.getElementById("ddate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>    
   

<?php

	if(isset($_POST['save'])) {
			$ok = 'Y';
			if ($_REQUEST['activity'] == '') {
				echo '<script>';
				echo 'alert("Please enter an activity.")';
				echo '</script>';	
				$ok = 'N';
			}
			if ($_REQUEST['activity_status'] == '') {
				echo '<script>';
				echo 'alert("Please enter an activity status.")';
				echo '</script>';	
				$ok = 'N';
			}			
				
			if ($ok == 'Y') {		
	
				  include_once("../includes/cltadmin.php");
				  $oAct = new cltadmin;	
					
				  $oAct->activity_status = $_REQUEST['activity_status'];
				  $oAct->uid = $acuid;
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
					$oAct->status = $_REQUEST['activity_status'];
					$oAct->staff_id = $user_id;
					$oAct->contact = $_REQUEST['contact'];
		
					$oAct->EditActivity();
		
				  $hdate = date('Y-m-d');
				  $ttime = strftime("%H:%M", time());
				  
				  include_once("../includes/DBClass.php");
				  $dba = new DBClass();

					$dba->query("insert into ".$cltdb.".audit (ddate,ttime,user_id,uname,sub_id,member_id,activities_id,action) values (:ddate,:ttime,:user_id,:uname,:sub_id,:member_id,:activities_id,:action)");
					$dba->bind(':ddate', $hdate);
					$dba->bind(':ttime', $ttime);
					$dba->bind(':user_id', $user_id);
					$dba->bind(':uname', $uname);
					$dba->bind(':sub_id', $subid);
					$dba->bind(':member_id', $cluid);
					$dba->bind(':activities_id', $acuid);
					$dba->bind(':action', 'Edit Note');
				  
				  $dba->execute();
				  $dba->closeDB();
				  
					?>
				<script>
				var view = '<?php echo $view; ?>';
				var from = '<?php echo $from; ?>';
				window.open("","editmembers").jQuery("#mactivitylist").trigger("reloadGrid");
				this.close();
				</script>
					<?php
			
				
			}
		}
?>


</body>
</html>
