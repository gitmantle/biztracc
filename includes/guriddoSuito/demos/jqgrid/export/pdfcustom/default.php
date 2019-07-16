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
		jQuery(document).ready(function($) {
			jQuery('#grid').jqGrid({
				"width":"700",
				"hoverrows":false,
				"viewrecords":true,
				"jsonReader":{"repeatitems":false},
				"gridview":true,
				"url":"grid.php",
				"editurl":"grid.php",
				"cellurl":"grid.php",
				"rowNum":10,
				"rowList":[10,20,30],
				"sortname":"OrderID",
				"caption":"PDF export",
				"datatype":"json",
				"colModel":[
					{"name":"OrderID","index":"OrderID","sorttype":"int","key":true,"editable":true},
					{"name":"OrderDate","index":"OrderDate","sorttype":"datetime","formatter":"date","formatoptions":{"srcformat":"Y-m-d H:i:s","newformat":"m\/d\/Y"},"search":false,"editable":true},
					{"name":"CustomerID","index":"CustomerID","sorttype":"string","editable":true},
					{"name":"ShipName","index":"ShipName","sorttype":"string","width":"250","editable":true},
					{"name":"Freight","index":"Freight","sorttype":"numeric","label":"Test","align":"right","editable":true}
				],
				"postData":{"oper":"grid"},
				"loadError":function(xhr,status, err){ 
					try {jQuery.jgrid.info_dialog(jQuery.jgrid.errors.errcap,'<div class="ui-state-error">'+ xhr.responseText +'</div>', jQuery.jgrid.edit.bClose,{buttonalign:'right'});} 
					catch(e) { alert(xhr.responseText);} },
				"pager":"#pager"
			});
			jQuery('#grid').jqGrid('navGrid','#pager',{"edit":false,"add":false,"del":false,"search":true,"refresh":true,"view":false,"excel":false,"pdf":true,"csv":false,"columns":false});
			jQuery('#grid').jqGrid('navButtonAdd','#pager',{
				id:'pager_pdf',
				caption:'',
				title:'Export To Pdf',
				onClickButton : function(e)	{
					try {
						jQuery("#grid").jqGrid('excelExport',{tag:'pdf', url:'export.php'});
					} catch (e) {
						window.location= 'export.php?oper=pdf';
					}
				}, 
				buttonicon:'ui-icon-print'
			}); 
		});	
	</script>
  </head>
  <body>
      <div>
		  <table id="grid"></table>
		  <div id="pager"></div>
      </div>
      <br/>
      <?php tabs(array("export.php","grid.php"));?>
   </body>
</html>
