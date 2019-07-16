<?php
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

$mergefile = $_REQUEST['filterfile'];

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

$db->closeDB();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Template Document</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>


<script>
function usetemplate(fl,tsub) {
	document.getElementById('ttemplate').value = fl;
	document.getElementById('tempsub').value = tsub;
}

function viewdoc(d) {
	var readfile = ""+d;
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
	window.open(readfile,'rldoc','toolbar=0,scrollbars=1,height=10,width=10,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function temprun() {
	var sid = "<?php echo $subscriber; ?>";
	var userid = "<?php echo $user_id; ?>";
	var template = sid+'__'+document.getElementById('ttemplate').value;
	var output = userid+'__'+document.getElementById('toutput').value+'.docx';
	var tsubject = document.getElementById('tempsub').value;
	var mergefile = "<?php echo $mergefile; ?>";
	$.get("includes/ajaxEmailmerge.php", {template: template, output: output, tsubject: tsubject, mergefile: mergefile}, function(data){
	});
	
	
}


</script>



</head>

<body>
<form name="merge" id="merge" method="post">
 <input type="hidden" name="tempsub" id="tempsub" value=" ">

  <table width="500" align="center">
    <tr>
      <td colspan="2"><?php include "gettemplates.php" ?></td>
    </tr>
    <tr>
    	<td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Populate this template</label></td>
        <td><input name="ttemplate" id="ttemplate" type="text" /></td>
    </tr>    
    <tr>
    	<td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Output to file name</label></td>
        <td class="boxlabelleft"><input name="toutput" id="toutput" type="text" />.docx</td>
    </tr>    
    <tr>
    	<td><input type="button" name="brun" id="brun" value="Run" onclick="temprun()" /></td>
        <td>&nbsp;</td>
    </tr>
  </table>
</form>




</body>
</html>