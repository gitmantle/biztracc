<?php 
require_once '../../../../php/demo/tabs.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <title>jqGrid PHP Demo</title>
    <link rel="stylesheet" type="text/css" media="screen" href="../../../../css/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="../../../../css/trirand/ui.jqgrid.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="../../../../css/ui.multiselect.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="../../../../css/trirand/ui.jqform.css" />
    <style type="text">
        html, body {
        margin: 0;			/* Remove body margin/padding */
    	padding: 0;
        overflow: hidden;	/* Remove scroll bars on browser window */
        font-size: 75%;
        }
		#tags {z-index: 900}
	.input-ui  {padding :4px;}
	.select-ui  {padding : 3px;}
	.textarea-ui {padding: 3px;}
	.globalparam {display:none;}
	.ui-input  {padding :4px;}
	.ui-select  {padding : 3px;}
	.ui-textarea {padding: 3px;}
	.ui-event-cell {padding: 5px;height:25px;white-space: nowrap; overflow:  hidden;}
	.ui-grid-footer { background-image: none; font-weight: normal; text-align: left;}
	</style>
    <script src="../../../../js/jquery.min.js" type="text/javascript"></script>
    <script src="../../../../js/trirand/i18n/grid.locale-en.js" type="text/javascript"></script>
	 	<script src="../../../../js/trirand/jquery.jqGrid.min.js" type="text/javascript"></script> <script type="text/javascript">   	  
	$.jgrid.no_legacy_api = true;
	$.jgrid.useJSON = true;
	$.jgrid.defaults.width = "700";
	</script>
    <script src="../../../../js/jquery.form.js" type="text/javascript"></script>
     
    <script src="../../../../js/jquery-ui.min.js" type="text/javascript"></script>
  </head>
  <body>
      <div>
          <?php include ("grid.php");?>
      </div>
      <br/>
      <?php tabs(array("grid.php","customer.php"));?>
   </body>
</html>
