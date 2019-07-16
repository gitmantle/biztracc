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
	 	<script src="../../../../js/trirand/jquery.jqGrid.min.js" type="text/javascript"></script> <script type="text/javascript">   	  
		jQuery(document).ready(function(){
			$(".datepicker").datepicker({dateFormat:'mm/dd/yy'});
			$("#search").click(function(){
				var from = $("#from").val(),
				to = $("#to").val();
				if( from && to) {
					$("#grid").jqGrid('setGridParam', {postData:{"from":from,"to":to}, search: true} );
					$("#grid").trigger("reloadGrid");
				}
			});
		});
	</script>
  </head>
  <body>
	  <div>
		  From: <input id="from" class="datepicker" size="10"></input>
		  To: <input id="to" class="datepicker" size="10"></input>
		  <input id="search" type="button" value="Search"></input>
		  <br/>
		  <br/>
	  </div>
      <div>
          <?php include ("grid.php");?>
      </div>
      <br/>
      <?php tabs(array("grid.php"));?>
   </body>
</html>
