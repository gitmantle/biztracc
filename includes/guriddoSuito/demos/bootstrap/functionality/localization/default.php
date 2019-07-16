<?php 
require_once '../../../../php/demo/tabs3.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <title>jqGrid PHP Demo</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text">
        html, body {
        margin: 0;			/* Remove body margin/padding */
    	padding: 0;
        overflow: hidden;	/* Remove scroll bars on browser window */
        font-size: 75%;
        }
    </style>
    <script src="../../../../js/jquery.min.js" type="text/javascript"></script>
    <script src="../../../../js/jquery.cookie.js" type="text/javascript"></script>
    <script src="../../../../js/trirand/i18n/grid.locale-en.js" type="text/javascript"></script>
    <script src="../../../../js/trirand/i18n/grid.locale-bg.js" type="text/javascript"></script>
    <script src="../../../../js/trirand/i18n/grid.locale-de.js" type="text/javascript"></script>
    <script src="../../../../js/trirand/i18n/grid.locale-kr.js" type="text/javascript"></script>
	<script src="../../../../js/trirand/jquery.jqGrid.min.js" type="text/javascript"></script> 
    <!-- This is the localization file of the grid controlling messages, labels, etc.
    <!-- A link to a jQuery UI ThemeRoller theme, more than 22 built-in and many more custom -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> 
    <!-- The link to the CSS that the grid needs -->
    <link rel="stylesheet" type="text/css" media="screen" href="../../../../css/trirand/ui.jqgrid-bootstrap.css" />
	<script>
		$.jgrid.defaults.width = 780;
		$.jgrid.defaults.responsive = true;
		$.jgrid.defaults.styleUI = 'Bootstrap';

	</script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <meta charset="utf-8" />
     
  </head>
  <body>
	 
	<div style="margin-left:20px">
      <div style="margin-bottom: 10px">
      Select Language :
      <select id="lang" class="form-control" style="width:150px;">
          <option value="bg">Bulgarian</option>
          <option value="en" selected>English</option>
          <option value="de">German</option>
          <option value="kr">Korean</option>
      </select>
      </div>
      <div>
          <?php include ("grid.php");?>
      </div>
	</div>
      <br/>
      <?php tabs(array("grid.php"));?>
   </body>
</html>
