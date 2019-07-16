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

if(isset ($_REQUEST["cid"])) {
    $id = jqGridUtils::Strip($_REQUEST["cid"]);
} else {
    $id = "";
}

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,itemcode,item,unit,quantity,supplied,ref_no from ".$findb.".p_olines where ref_no = '".$id."'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getposodetails.php');

// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Line Items",
    "rowNum"=>7,
    "sortname"=>"itemcode",
    "rowList"=>array(7,50,100),
	"height"=>150,
	"width"=>750
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("itemcode", array("label"=>"Item Code", "width"=>90));
$grid->setColProperty("item", array("label"=>"Item", "width"=>150));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>70));
$grid->setColProperty("quantity", array("label"=>"Ordered", "width"=>70));
$grid->setColProperty("supplied", array("label"=>"Supplied", "width"=>70));
$grid->setColProperty("ref_no", array("label"=>"Ref", "width"=>50, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#posodetlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#posodetlist").getRowData(ids[i]);
			var cl = ids[i];
			var rf = "'"+rowd.ref_no+"'";
			se = '<img src="../images/printer.gif" title="Print Document" onclick="javascript:printp_o('+rf+')" ></ids>';
			jQuery("#posodetlist").setRowData(ids[i],{act:se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#posodetlist','#posodetpager',true, null, null,true,true,true);
?>




