<?php
require_once 'jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Get the needed parameters passed from the main grid
$rowid = jqGridUtils::GetParam('idprotocols', 0);
// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = "SELECT a.idprotocol_Ingredients, a.ingredients_idingredients, b.idingredients, b.ing_name, a.unit, a.amount, a.protocols_idprotocols FROM protocol_ingredients a, ingredients b WHERE b.idingredients=a.ingredients_idingredients AND a.protocols_idprotocols= ?";
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->table ="protocol_ingredients";
$grid->setColModel(null, array((int)$rowid));
// Set the url from where we obtain the data
$grid->setUrl('detail.php');
// Set some grid options 
$grid->setGridOptions(array(
    "rowNum"=>10,
    "footerrow"=>true,
    "userDataOnFooter"=>true,
    "sortname"=>"idprotocol_Ingredients",
    "height"=>110
    ));
// Change some property of the field(s)
$grid->setColProperty('amount', array("label"=>"Amount", "width"=>50));
$grid->setColProperty('ing_name', array("label"=>"Ingredients", "width"=>150));
$grid->setColProperty('unit', array("label"=>"Unit", "width"=>50));
$grid->setColProperty("idingredients", array("hidden"=>true));
$grid->setColProperty("ingredients_idingredients", array("editoptions"=>array("readonly"=>"readonly")));
$grid->setAutocomplete("ing_name","#ingredients_idingredients","SELECT  b.idingredients, b.ing_name, a.ingredients_idingredients FROM protocol_ingredients a, ingredients b WHERE b.ing_name LIKE ? ORDER BY b.ing_name",null,true,true);

// on beforeshow form when add we get the customer id and set it for posting
$beforeshow = <<<BEFORE
function(formid)
{
var srow = jQuery("#grid").jqGrid('getGridParam','selrow');
if(srow) {
    var gridrow = jQuery("#grid").jqGrid('getRowData',srow);
    $("#protocols_idprotocols",formid).val(gridrow.protocols_idprotocols);
}
}
BEFORE;

// disable the CRUD buttons when we initialy load the grid
$initgrid = <<< INIT
jQuery("#add_detail").addClass("ui-state-disabled");
jQuery("#edit_detail").addClass("ui-state-disabled");
jQuery("#del_detail").addClass("ui-state-disabled");
INIT;

//$grid->debug = true;
$grid->showError = true;
$grid->setJSCode($initgrid);

$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>true,"edit"=>true,"del"=>true,"view"=>false));
$grid->setNavEvent('add', 'beforeShowForm', $beforeshow);
$grid->setNavOptions('add', array("recreateForm"=>true));
$grid->setNavOptions('edit', array("recreateForm"=>true, "closeAfterEdit"=>true));
// Enjoy
$grid->renderGrid("#detail","#pgdetail", true, $summaryrow=array(), array((int)$rowid), true,true);
