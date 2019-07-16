<?php
session_start();
$usersession = $_SESSION['usersession'];
$ind = $_REQUEST['ind'];
$tid = $_REQUEST['uid'];

include_once("DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$sname = $row['uname'];
$subscriber = $subid;

$subscriber = $subid;

$cltdb = $_SESSION['s_cltdb'];

$db->query("select * from ".$cltdb.".todo where todo_id = ".$tid);
$row = $db->single();
extract($row);
$utodo_by = $todo_by;

// populate  staff drop down
$db->query("select * from users where sub_id = :subid order by ulname");
$db->bind(':subid', $subid);
$rows = $db->resultset();
$staff_options = '<option value="0">Select</option>';
foreach ($rows as $row) {
	extract($row);
	if ($row['uid'] == $user_id) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$staff_options .= '<option value="'.$row['ufname'].' '.$row['ulname'].'"'.$selected.'>'.$row['ufname'].' '.$row['ulname'].'</option>';
}

date_default_timezone_set($_SESSION['s_timezone']);

if ($complete_by == '0000-00-00') {
	$fddate = date("Y-m-d");
	$ddate = date("Y-m-d");
} else {
	$dt = explode('-',$complete_by);
	$y = $dt[0];
	$m = $dt[1];
	$d = $dt[2];
	$fdt = mktime(0,0,0,$m,$d,$y);
	$fddate = date("Y-m-d",$fdt);
}


// populate category list
    $arr = array('Standard','Low Priority', 'High Priority', 'Urgent');
	$cat_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $category) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$cat_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit ToDo</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
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
 <input type="hidden" name="ddateh" id="ddateh" value="<?php echo $ddate; ?>">
  <table width="750" border="0" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;"><u>Edit Todo</u></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Responsibility of</label></td>
      <td ><select name="lstaff" id="lstaff">
      	<?php echo $staff_options; ?>
      </select></td>
     </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Category</label></td>
      <td><select name="category" id="category">
      	<?php echo $cat_options; ?>
      </select></td>
     </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Task</label></td>
      <td><input name="task" type="text" id="task" size="80" maxlength="100" value="<?php echo $task; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Complete by</label></td>
      <td style="font-size: 100%; font-family:Tahoma, Geneva, sans-serif"><input type="Text" id="ddate" name="ddate" value="<?php echo $fddate; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j,Y"></td>
    </tr>	 
	<tr>
	  <td>&nbsp;</td>
	  <td align="center"><input type="button" value="Save" name="save"  onClick="post()" ></td>
	  </tr>
  </table>
</form>
	<script>document.onkeypress = stopRKey;</script> 
 <script>
 	document.getElementById("ddate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
			ajaxCheckTransDate();	
		}
	});
 </script>
   
<?php

	if($_REQUEST['savebutton'] == "Y") {
		
		$dstaff = $_REQUEST['lstaff'];
	
		$completeby = $_REQUEST['ddate'];
		$task = $_REQUEST['task'];
		if (isset($_REQUEST['category'])) {
			$category = $_REQUEST['category'];
		} else {
			$category = "";
		}
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$db->query('update '.$cltdb.'.todo set complete_by = :complete_by, todo_by = :todo_by, task = :task, category = :category where todo_id = :todo_id');
		$db->bind(':complete_by', $completeby);
		$db->bind(':todo_by', $dstaff);
		$db->bind(':task', $task);
		$db->bind(':category',$category);
		$db->bind(':todo_id', $tid);
		
		$db->execute();
		$db->closeDB();
		
		  echo "<script>";
		  
		  echo 'window.open("","updttodo").jQuery("#todolist").trigger("reloadGrid");';
		  echo 'this.close();';
		  
		  echo '</script>';

		  
	  }

?>


</body>
</html>
