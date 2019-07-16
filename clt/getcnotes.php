<?php
session_start();

$cltdb = $_SESSION['s_cltdb'];

$id = $_SESSION['s_mid'];
if (isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
}

include 'jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select activities.activities_id,activities.ddate,activities.activity from ".$cltdb.".activities where activities.member_id  = ".$id; 

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getcnotes.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
	"caption"=>"Notes",						
    "rowNum"=>12,
    "sortname"=>"ddate",
	"sortorder"=>"desc",
    "rowList"=>array(12,30,50),
	"height"=>60,
	"width"=>490
    ));


// Change some property of the field(s)
$grid->setColProperty("activities_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>100, "formatter"=>"date"));
$grid->setColProperty("activity", array("label"=>"Note", "width"=>385));


// on select row we should post the member id to second table and trigger it to reload the data
$selectnote = <<<NOTE
function(rowid, selected)
{
    if(rowid != null) {
		var rowdata = $("#cnotelist").getRowData(rowid);
		var content = rowdata.activity;
		displayitem(content);
    }
}
NOTE;
$grid->setGridEvent('onSelectRow', $selectnote);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#cnotepager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a note", "onClickButton"=>"js: function(){addnote();}")
);
$grid->callGridMethod("#cnotelist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#cnotelist','#cnotepager',true, null, null, true,true);

?>




