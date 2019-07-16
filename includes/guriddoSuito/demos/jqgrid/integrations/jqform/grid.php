<?php
require_once '../../jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = 'SELECT CustomerID, CompanyName, Phone, PostalCode, City FROM customers';
// Set the table to where you update the data
$grid->table = 'customers';
// Set output format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "rowList"=>array(10,20,30),
    "sortname"=>"CustomerID"
));
$grid->setColProperty('CustomerID', array("editoptions"=>array("readonly"=>true)));
// Enable navigator
$grid->navigator = true;
// Enable only editing
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false, "search"=>false));
// Close the dialog after editing
$grid->setNavOptions('edit',array("closeAfterEdit"=>true,"editCaption"=>"Update Customer","bSubmit"=>"Update"));
// Enjoy

$form = <<< FORM
function(){
   var id = $("#grid").jqGrid('getGridParam','selrow'), data={};
   if(id) {
	   data = {CustomerID:id};
   } else {
	  alert('Please select a row to edit');
	  return;
   }
   var ajaxDialog = $('<div id="ajax-dialog" style="display:hidden" title="Customer Edit"></div>').appendTo('body');
   ajaxDialog.load(
      'customer.php',
       data,
       function(response, status){
           ajaxDialog.dialog({
			   width: 'auto',
			   modal:true,
			   open: function(ev, ui){
				  $(".ui-dialog").css('font-size','0.9em');
			   },
               close: function(e,ui) {
                   ajaxDialog.remove();
               }
           });
		    ajaxDialog.dialog('widget').css('font-size','12px');
        }
	);
}
FORM;

$buttonoptions = array("#pager",
    array(
      "caption"=>"Custom Edit Form",
      "onClickButton"=>"js:".$form
    )
);
$grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions);
$grid->renderGrid('#grid','#pager',true, null, null, true,true);

