<?php

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>jQuery UI Datepicker - Default functionality</title>
<link type="text/css" href="includes/jquery/themes/dark-hive/jquery.ui.all.css" rel="stylesheet" />
<script src="includes/jquery/js/jquery.js"></script>
<script src="includes/jquery/jquery-ui/ui/jquery-ui.js"></script>
<script>

	$(document).ready(function(){
		$( "#datepicker" ).datepicker({ dateFormat: "dd/mm/yy", yearRange: "-5:+5", showOn: "button", buttonImage: "images/calendar.gif", buttonImageOnly: true, altField: "#ddateh", altFormat: "yy-mm-dd"});
	});
/*
	 $(document).ready(function(){
		$('#ddate').datepicker({ dateFormat: "dd/mm/yy", yearRange: "-5:+5", showOn: "button", buttonImage: "../images/calendar.gif", buttonImageOnly: true, altField: "#ddateh", altFormat: "yy-mm-dd"});
	 });

*/


</script>
</head>
<body>
<p>Date: <input type="text" id="datepicker"></p>
</body>
</html>