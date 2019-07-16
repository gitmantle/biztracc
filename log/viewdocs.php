<?php
session_start();
$stf = $_REQUEST['stf'];
$id = $_REQUEST['id'];

$_SESSION['s_staffdoc'] = $stf;
$_SESSION['s_staffid'] = $id;

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>View Signed Document</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>


<script type="text/javascript" src="js/fin.js"></script>


<script type="text/javascript">

window.name = 'docgrid';

function adddocument(staffid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('uploaddoc.php?staffid='+staffid,'addc','toolbar=0,scrollbars=1,height=270,width=570,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function viewdocument(id,sid,d) {
	var readfile = "documents/"+sid+"/"+d;
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
	window.open(readfile,'vdoc','toolbar=0,scrollbars=1,height=500,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function deldocument(uid) {
	  if (confirm("Are you sure you want to delete this document?")) {
		$.get("includes/ajaxdeldocument.php", {tid: uid}, function(data){$("#doclist").trigger("reloadGrid")});
	  }
}
</script>
</head>
<body>
    <table align="center">
        <tr>
	        <td><?php include "get1doc.php"; ?></td>
        </tr>
	</table>		

</body>
</html>


