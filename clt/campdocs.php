<?php
session_start();
//ini_set('display_errors', true);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Campaign Documents</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>

<script type="text/javascript">

window.name = 'updtcampdocs';

	function viewdoc(d) {
		var readfile = "../documents/campaign/"+d;
			var x = 0, y = 0; // default values	
			x = window.screenX +5;
			y = window.screenY +100;
		window.open(readfile,'cdoc','toolbar=0,scrollbars=1,height=500,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}

	function addcampdoc() {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +375;
		window.open('addcampdoc.php','addcdoc','toolbar=0,scrollbars=1,height=170,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}  				

	function deldoc(uid,doc) {
	  if (confirm("Are you sure you want to delete this document")) {
				$.get("includes/ajaxdelcampdoc.php", {tid: uid, doc: doc}, function(data){$("#doclist").trigger("reloadGrid")});
		  }
	}


</script>
</head>
<body>
    <table>
    <tr><td align="center" ><label style="font-size: 14px;">Update Campaign Documents for <?php echo $name; ?></label></td></tr>
    <tr>
    <td><?php include "getcampdocs.php" ?></td>
    </tr>
	</table>		

</body>
</html>