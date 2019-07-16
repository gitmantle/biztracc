<?php
ini_set("display_errors",1);
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
$grid->SelectCommand = "SELECT idCultural_Notes, protocol_idprotocols, note_type, note_unit, note_min, note_max FROM cultural_notes WHERE protocol_idprotocols= ?";
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

$grid->setColModel(null, array((int)$rowid));
// Set the url from where we obtain the data
$grid->setUrl('detail2.php');
// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "footerrow"=>true,
    "userDataOnFooter"=>true,
    "sortname"=>"idCultural_Notes",
    "height"=>110
    ));
// Change some property of the field(s)
$grid->setColProperty('note_min', array("label"=>"Minimum Amount", "width"=>50));
$grid->setColProperty('note_type', array("label"=>"Recomended:", "width"=>150));
$grid->setColProperty('note_unit', array("label"=>"Unit", "width"=>50));
$grid->setColProperty("idCultural_Notes", array("hidden"=>true));
$grid->setColProperty("protocol_idprotocols", array("hidden"=>true));

// on beforeshow form when add we get the customer id and set it for posting
$beforeshow = <<<BEFORE
function(formid)
{
var srow = jQuery("#grid").jqGrid('getGridParam','selrow');
if(srow) {
    var gridrow = jQuery("#grid").jqGrid('getRowData',srow);
    $("#protocol_idprotocols",formid).val(gridrow.protocol_idprotocols);
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
$grid->renderGrid("#detail2","#pgdetail2", true, $summaryrow=array(), array((int)$rowid), true,true);
