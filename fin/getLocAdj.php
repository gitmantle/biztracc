<?php
session_start();
//ini_set('display_errors', true);

$findb = $_SESSION['s_findb'];

include 'jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Get the needed parameters passed from the main grid

if(isset ($_REQUEST["itid"])) {
    $id = jqGridUtils::Strip($_REQUEST["itid"]);
} else {
    $id = '';
}

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "SELECT stktrans.itemcode,stktrans.locid,stklocs.location, sum( stktrans.increase - stktrans.decrease ) as onhand FROM ".$findb.".stktrans,".$findb.".stklocs WHERE stktrans.itemcode = '".$id."' and stktrans.locid = stklocs.uid GROUP BY stktrans.locid";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getLocAdj.php');

// Set some grid options
$grid->setGridOptions(array(
	"caption"=>"Quantities per Location",						
    "rowNum"=>7,
    "sortname"=>"location",
    "rowList"=>array(7,50,100),
	"height"=>182,
	"width"=>880
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("itemcode", array("label"=>"Itemcode", "width"=>40,"hidden"=>true));
$grid->setColProperty("locid", array("label"=>"Loc Id", "width"=>40,"hidden"=>true));
$grid->setColProperty("location", array("label"=>"Location", "width"=>400));
$grid->setColProperty("onhand", array("label"=>"Quantity on hand", "width"=>400));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

// At end call footerData to put total  label
// Set which parameter to be sumarized
$summaryrows = array("onhand"=>array("onhand"=>"SUM"));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#stklqlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#stklqlist").getRowData(ids[i]);
			var cl = ids[i];
			var ic = rowd.itemcode;
			var lc = rowd.locid;
			var il = "'"+ic+'~'+lc+"'";
			be = '<img src="../images/edit.png" title="Adjust stock quantity" onclick="javascript:adjstock('+il+')" ></ids>';
			jQuery("#stklqlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#stklqlist','#stklqpager',true, $summaryrows, null,true,true,true);
?>




