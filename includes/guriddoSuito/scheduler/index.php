<?php 
session_start();
include 'tabs.php';
ini_set("display_errors",1);
?>
<!DOCTYPE html>
<html>
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	  
    <style type="text">
        html, body {
			margin: 0;			/* Remove body margin/padding */
			padding: 0;
		    overflow: hidden;	/* Remove scroll bars on browser window */
	        font-size: 62.5%;
        }
		body {
			font-family: "Trebuchet MS", "Helvetica", "Arial",  "Verdana", "sans-serif";
		}
    </style>
    <title>jqScheduler PHP Demo</title>
    <link rel="stylesheet" type="text/css" media="screen" href="../css/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="../css/jquery.ui.tooltip.css" />

    <link rel="stylesheet" type="text/css" media="screen" href="../css/trirand/ui.jqscheduler.css" />
	<style type="text/css">
		#search_table a {
			color : inherit;
		}
	</style>
	<script src="../js/jquery.min.js" type="text/javascript"></script>
    <script src="../js/jquery-ui.min.js" type="text/javascript"></script>

    <script src="../js/trirand/jquery.jqScheduler.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			//setTimeout(function(){
				//$(".fc-sun").hide();
			//},1000);
		});
	</script>
  </head>
  <body>
<!-- <div id="calendar" style="height: 900px;margin: 0 auto;width:1000px;font-size: 0.8em;"></div> -->
<?php require_once("eventcal.php");?>  

<br/>
<?php tabs(array("eventcal.php"));?>
   </body>
</html>
