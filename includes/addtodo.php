<?php
session_start();
$usersession = $_SESSION['usersession'];
$ind = $_REQUEST['ind'];

include_once("DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$sname = $row['uname'];
$subscriber = $subid;

// populate  user drop down
$db->query("select * from users where sub_id = ".$subscriber." order by ulname");
$rows = $db->resultsetNum();
$staff_options = '<option value="0">Select User</option>';
foreach ($rows as $row) {
	extract($row);
	$staff_options .= '<option value="'.$row[2].' '.$row[3].'">'.$row[2].' '.$row[3].'</option>';
}

$cltdb = $_SESSION['s_cltdb'];

date_default_timezone_set($_SESSION['s_timezone']);

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");
$ttime = strftime("%H:%M", time());

// populate reason list
    $arr = array('Other', 'Appointment', 'Query', 'Visit', 'Informative');
	$reason_options = "";
    for($i = 0; $i < count($arr); $i++)	{
		$selected = '';
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
<title>Add ToDo</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="jquery/js/jquery.js" type="text/javascript"></script>
<script src="jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="jquery/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="js/main.js"></script>
</head>


<script>
	 
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function post() {

	//add validation here if required.
	var activity = document.getElementById('task').value;
	var staff = document.getElementById('lstaff').value;
	var ok = "Y";
	if (activity == "") {
		alert("Please enter a task.");
		ok = "N";
		return false;
	}
	if (staff == 0) {
		alert("Please enter the responsible member of staff.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
	
	
}
 
	 
</script>

<body>
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
 <input type="hidden" name="ddateh" id="ddateh" value="<?php echo $hdate; ?>">
  <table width="750" border="0" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;"><u>Add Todo</u></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Responsibility of</label></td>
      <td ><select name="lstaff" id="lstaff">
      	<?php echo $staff_options; ?>
      </select></td>
     </tr>
    <tr>
      <td class="boxlabel">Category</td>
      <td><select name="category" id="category">
        <option value="Standard">Standard</option>
        <option value="Low Priority">Low Priority</option>
        <option value="High Priority">High Priority</option>
        <option value="Urgent">Urgent</option>
      </select></td>
     </tr>
    <tr>
      <td class="boxlabel">Task</td>
      <td><input name="task" type="text" id="task" size="80" maxlength="100"></td>
    </tr>
    <tr>
      <td class="boxlabel">Complete by</td>
      <td style="font-size: 100%; font-family:Tahoma, Geneva, sans-serif"><input type="Text" id="ddate" name="ddate" value="<?php echo $hdate; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j,Y"></td>
    </tr>	 
	<tr>
	  <td>&nbsp;</td>
	  <td align="center"><input type="button" value="Save" name="save"  onClick="post()" ></td>
	  </tr>
  </table>
  
 <script>
 	document.getElementById("ddate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
			ajaxCheckTransDate();	
		}
	});
 </script>
   
</form>
<script>
	document.onkeypress = stopRKey;
</script> 

<?php

	if($_REQUEST['savebutton'] == "Y") {
		
		$edate = date('Y-m-d');
		$estaff = $sname;
		$dstaff = $_REQUEST['lstaff'];
	
		$completeby = $_REQUEST['ddate'];
		
		$task = $_REQUEST['task'];
		$category = $_REQUEST['category'];
		$subid = $subscriber;
		
		include_once("DBClass.php");
		$db = new DBClass();
		
		$db->query('insert into '.$cltdb.'.todo (enter_date,enter_staff,todo_by,complete_by,task,category,sub_id) values (:enter_date,:enter_staff,:todo_by,:complete_by,:task,:category,:sub_id)');
		$db->bind(':enter_date', $edate);
		$db->bind(':enter_staff', $estaff);
		$db->bind(':todo_by', $dstaff);
		$db->bind(':complete_by', $completeby);
		$db->bind(':task', $task);
		$db->bind(':category', $category);
		$db->bind(':sub_id', $subid);
		
		$db->execute();
		$db->closeDB();
		
		  echo "<script>";
		  
		  if ($ind == 'I') {
		  	echo 'window.open("","cltindex").jQuery("#todolist").trigger("reloadGrid");';
		  }
		  if ($ind == 'M') {
		  	echo 'window.open("","updttodo").jQuery("#todolist").trigger("reloadGrid");';
		  }
		  echo 'this.close();';
		  
		  echo '</script>';
		  
	  }

?>


</body>
</html>
