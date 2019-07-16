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
$grid->SelectCommand = "select invtrans.uid,invtrans.ref_no,invtrans.itemcode,stkmast.item,invtrans.quantity from ".$findb.".invtrans,".$findb.".invhead,".$findb.".stkmast where (invhead.ref_no = invtrans.ref_no) and (stkmast.itemcode = invtrans.itemcode) and (invtrans.value = 0) and invtrans.ref_no = '".$id."'";
// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getUncostGRNitems.php');

// Set some grid options
$grid->setGridOptions(array(
	"caption"=>"GRN Items",						
    "rowNum"=>3,
    "rowList"=>array(3,20,100),
	"height"=>72,
	"width"=>800
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ref_no", array("label"=>"Ref", "width"=>80));
$grid->setColProperty("itemcode", array("label"=>"Stock code", "width"=>150));
$grid->setColProperty("item", array("label"=>"Item", "width"=>150));
$grid->setColProperty("quantity", array("label"=>"Quantity", "width"=>80));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#UncostGRNitems").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#UncostGRNitems").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/dollar.gif" title="Cost Line Item" onclick="javascript:costgrn('+cl+')" ></ids>';
			jQuery("#UncostGRNitems").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;
$grid->setGridEvent("loadComplete",$loadevent);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#UncostGRNitems','#UncostGRNitemspager',true, null, null,true,true,true);
?>




