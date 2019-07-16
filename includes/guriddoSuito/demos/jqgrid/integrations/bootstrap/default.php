<?php 
require_once '../../../../php/demo/tabs.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <title>jqGrid PHP Demo</title>
   
    <link rel="stylesheet" type="text/css" media="screen" href="../../../../css/trirand/ui.jqgrid.css" />
    <!-- A link to a jQuery UI ThemeRoller theme, more than 22 built-in and many more custom -->
    <link rel="stylesheet" type="text/css" media="screen" href="../../../../css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="../../../../css/uibootstrap/jquery-ui-1.10.0.bootstrap.css" />
    <!-- The link to the CSS that the grid needs -->
    <link rel="stylesheet" type="text/css" media="screen" href="../../../../css/trirand/ui.jqgrid-bootstarp.css" />

    <style type="text">
        html, body {
        margin: 0;			/* Remove body margin/padding */
    	padding: 0;
        overflow: hidden;	/* Remove scroll bars on browser window */
        font-size: 75%;
        }
		
    </style>
    <script src="../../../../js/jquery.min.js" type="text/javascript"></script>
    <script src="../../../../js/trirand/i18n/grid.locale-en.js" type="text/javascript"></script>
	 	<script src="../../../../js/trirand/jquery.jqGrid.min.js" type="text/javascript"></script> <script type="text/javascript">   	  
	$.jgrid.no_legacy_api = true;
	$.jgrid.useJSON = true;
	$.jgrid.defaults.width = "700";
	</script>
     
    <script src="../../../../js/jquery-ui.min.js" type="text/javascript"></script>
  </head>
  <body>
      <div>
          <?php include ("grid.php");?>
      </div>
      <br/>
      <?php tabs(array("grid.php"));?>
   </body>
</html>
