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
$grid->setColProperty('CompanyName', array("editoptions"=>array("required"=>"required")));
// Enable navigator
$grid->navigator = true;
// Enable only editing
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>true,"edit"=>true,"del"=>false,"view"=>false, "search"=>false));
// Close the dialog after editing
$grid->setNavOptions('edit',array("recreateForm"=>true, "width"=>400,"height"=>'auto',"dataheight"=>'auto', "closeAfterEdit"=>true,"editCaption"=>"Update Customer","bSubmit"=>"Update"));
$grid->setNavOptions('add',array("recreateForm"=>true, "width"=>400,"height"=>'auto',"dataheight"=>'auto',"closeAfterAdd"=>true));

$buttonoptions = array("#pager",
    array("title"=>"View Row", "caption"=>"", "onClickButton"=>"js: function(){
		var sr = this.p.selrow;
		if(sr) {
			$('#grid').jqGrid('editGridRow', sr, {
				recreateForm: true, 
				width:'400', 
				height:'auto', 
				dataheight:'auto',
				bCancel : 'Close',
				beforeShowForm: function(form) {
					// The modal has a id - editmod + id of the grid
					// i.e in this case editmodgrid
					$('#sData','#editmodgrid').remove();
				}
			});
		} else {
			// your alert here
		}
}")
);
$grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions);


// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);
?>
