<?php 
require_once '../../../../php/demo/tabs3.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <title>jqGrid PHP Demo</title>
    <!-- The jQuery library is a prerequisite for all jqSuite products -->
    <script type="text/ecmascript" src="../../../../js/jquery.min.js"></script> 
    <!-- We support more than 40 localizations -->
    <script type="text/ecmascript" src="../../../../js/trirand/i18n/grid.locale-en.js"></script>
    <!-- This is the Javascript file of jqGrid -->   
    <script type="text/ecmascript" src="../../../../js/trirand/jquery.jqGrid.min.js"></script>
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
	</script>
     
  </head>
  <body>
	<div style="margin-left:20px">
          <?php include ("grid.php");?>
<br/>
		<button id="savestate" class="btn btn-default">Save Grid State</button>

		<button id="loadstate" class="btn btn-default">Load Grid State</button>
      <br/><br/>
      </div>
      <?php tabs(array("grid.php"));?>
   </body>
</html>
