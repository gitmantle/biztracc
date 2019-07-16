<?php
require_once '../../../php/demo/tabs2.php';
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
  <head>
    <title>jqForm PHP Demo</title>
    <link rel="stylesheet" type="text/css" media="screen" href="../../../css/jquery-ui.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="../../../css/trirand/ui.jqform.css" />
    <style type="text/css">
        html, body {
        margin: 0;			/* Remove body margin/padding */
    	padding: 0;
        /*overflow: hidden;*/	/* Remove scroll bars on browser window */
        font-size: 90%;
        }
		body {
			font-family: "Trebuchet MS", "Helvetica", "Arial",  "Verdana", "sans-serif";
		}		

		#notsupported  { border: 0px none;}
    </style>
    <script src="../../../js/jquery.min.js" type="text/javascript"></script>
	<script src="../../../js/jquery-ui.min.js" type="text/javascript"></script>	
    <script src="../../../js/jquery.form.js" type="text/javascript"></script>
    <script src="../../../js/modernizr.js" type="text/javascript"></script>
	<script type="text/javascript">
		jQuery(document).ready(function(){
		  if(Modernizr.inputtypes.number) {
			  jQuery("#supported").append("<div>Your browser support input type: number!</div>");
		  } else {
			  jQuery("#notsupported").append("<div>Your browser does not support input type: number!</div>").addClass('ui-state-error');
		  }
		  $("input:submit").css('font-size','1.0em');
	  });
	</script>
	
  </head>
  <body>
      <div style="margin-left:20px;margin-right: 20px;">
          <?php include ("servervalidation.php");?>
		  <div style="text-align: center;margin-top:10px;">This Form is created with HTML5 Guriddo Form PHP builder</div>
      </div>
      <br/>
	  <div id="supported"></div>
	  <div id="notsupported"></div>
      <?php tabs(array("servervalidation.php"));?>
   </body>
</html>
