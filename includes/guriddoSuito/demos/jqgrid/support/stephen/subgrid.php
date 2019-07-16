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
// By default we add to postData subgrid and rowid parameters in the main grid
$subtable = jqGridUtils::Strip($_REQUEST["subgrid"]);
$rowid = jqGridUtils::Strip($_REQUEST["rowid"]);
if(!$subtable && !$rowid) die("Missed parameters");
// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = "SELECT idprotocols, protocol_name, research_idresearch FROM protocols WHERE research_idresearch= ?";
$grid->table ="protocols";
$grid->setPrimaryKeyId("idprotocols");
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel(null,array(&$rowid));
// Set the url from where we obtain the data
$grid->setUrl('subgrid.php');
// Set some grid options
$grid->setGridOptions(array(
    "width"=>540,
    "rowNum"=>10,
    "sortname"=>"idprotocols",
    "height"=>110,
    "postData"=>array("subgrid"=>$subtable,"rowid"=>$rowid)));
// Change some property of the field(s)
$grid->setColProperty('protocol_name', array("label"=>"Protcols", "width"=>50));
 
$selectorder = <<<ORDER
function(rowid, selected)
{
    if(rowid != null) {
        jQuery("#detail").jqGrid('setGridParam',{postData:{idprotocols:rowid}});
        jQuery("#detail").jqGrid('setColProp','idprotocols',{editoptions:{defaultValue:rowid}});
        jQuery("#detail").trigger("reloadGrid");
        jQuery("#detail2").jqGrid('setGridParam',{postData:{idprotocols:rowid}});
        jQuery("#detail2").jqGrid('setColProp','idprotocols',{editoptions:{defaultValue:rowid}});
        jQuery("#detail2").trigger("reloadGrid");
        // Enable CRUD buttons in navigator when a row is selected
        jQuery("#add_detail").removeClass("ui-state-disabled");
        jQuery("#edit_detail").removeClass("ui-state-disabled");
        jQuery("#del_detail").removeClass("ui-state-disabled");
        jQuery("#add_detail2").removeClass("ui-state-disabled");
        jQuery("#edit_detail2").removeClass("ui-state-disabled");
        jQuery("#del_detail2").removeClass("ui-state-disabled");
    }
}
ORDER;
// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
    jQuery("#detail").jqGrid('clearGridData',true);
      jQuery("#detail2").jqGrid('clearGridData',true);
    // Disable CRUD buttons in navigator when a row is not selected
    jQuery("#add_detail").addClass("ui-state-disabled");
    jQuery("#edit_detail").addClass("ui-state-disabled");
    jQuery("#del_detail").addClass("ui-state-disabled");
    jQuery("#add_detail2").addClass("ui-state-disabled");
    jQuery("#edit_detail2").addClass("ui-state-disabled");
    jQuery("#del_detail2").addClass("ui-state-disabled");
    

}
CLEAR;

$grid->setGridEvent('onSelectRow', $selectorder);
$grid->setGridEvent('onSortCol', $cleargrid);
$grid->setGridEvent('onPaging', $cleargrid);
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>true,"edit"=>true,"del"=>false,"view"=>false));
// Enjoy
$subtable = $subtable."_t";
$pager = $subtable."_p";
$grid->renderGrid($subtable,$pager, true, null, array(&$rowid), true,true);
