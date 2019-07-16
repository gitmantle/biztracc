<?php
session_start();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Insufficient Funds Listing</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>

<script type="text/javascript">

window.name = 'nofunds';

function nfemail() {
	  if (confirm("Are you sure you want to send reminder emails to those on this list who have email addresses")) {
		$.get("includes/ajaxnfemail.php", {}, function(data){alert(data);});
	  }
}

function nfsms() {
	  if (confirm("Are you sure you want to send sms messages to those on this list who have mobile phones")) {
		$.get("includes/ajaxnfsms.php", {}, function(data){alert(data);});
	  }
}

</script>
</head>
<body>
    <table align="center">
        <tr>
	        <td><?php include "getnofunds.php"; ?></td>
        </tr>
	</table>		

</body>
</html>