<?php
session_start();
ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

include_once("DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

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

$db->closeDB();

require_once("backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Task List</title>
<link rel="stylesheet" href="mantle.css" media="screen" type="text/css">
<link type="text/css" href="jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="jquery/themes/ui.jqgrid.css" />
<script src="jquery/js/jquery.js" type="text/javascript"></script>
<script src="jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="jquery/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="js/main.js"></script>

</head>

<script type="text/javascript">

window.name = 'updttodo';

function tedittodo(uid,i) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('edittodo.php?uid='+uid+'&ind='+i,'edtd','toolbar=0,scrollbars=1,height=300,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function taddtodo(i) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addtodo.php?ind='+i,'addtd','toolbar=0,scrollbars=1,height=300,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function tdeltodo(uid) {
	 if (confirm("Are you sure you want to delete this Task")) {
			$.get("ajaxdeltodo.php", {tid: uid}, function(data){$("#todolist").trigger("reloadGrid")});
	  }
}

function tdonetodo(uid) {
	 if (confirm("Are you sure you have completed this task?")) {
		$.get("ajaxdonetodo.php", {tid: uid}, function(data){$("#todolist").trigger("reloadGrid")});																	
	 }
}

function temailtodo() {
	
	location.href='mailto:Enter recipient here'
	
}

function xl_todo() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('xl_todo.php','tdxl','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

</script>

<body>
<table align="center">
  <tr>
    <td class="boxlabel"><label style="color:#FFF">Tasks for</label></td>
    <td align="left"><select name="lstaff" id="lstaff" onChange="listtasks()">
        <?php echo $staff_options; ?>
      </select></td>
  </tr>
  <tr>
    <td colspan="2">
    <?php include "gettodo.php"; ?>
    </td>
  </tr>
</table>
</body>
</html>
