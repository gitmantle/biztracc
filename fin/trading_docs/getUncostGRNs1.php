<?php
session_start();
error_reporting (E_ALL ^ E_NOTICE);
$findb = $_SESSION['s_findb'];

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
$grid->SelectCommand = "select invtrans.uid,invtrans.ref_no,invhead.ddate,invhead.client,invtrans.itemcode,stkmast.item,invtrans.quantity from ".$findb.".invtrans,".$findb.".invhead,".$findb.".stkmast where (invhead.ref_no = invtrans.ref_no) and (stkmast.itemcode = invtrans.itemcode) and (invtrans.value = 0) and invhead.transtype = 'GRN'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getUncostGRNs.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Uncosted GRNs",
    "rowNum"=>15,
    "sortname"=>"ref_no",
    "rowList"=>array(15,50,100),
	"height"=>350,
	"width"=>800
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>80));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("client", array("label"=>"Supplier", "width"=>150));
$grid->setColProperty("itemcode", array("label"=>"Stock code", "width"=>80));
$grid->setColProperty("item", array("label"=>"Item", "width"=>150));
$grid->setColProperty("quantity", array("label"=>"Quantity", "width"=>60));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#uncostlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#uncostlist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/dollar.gif" title="Cost Line Item" onclick="javascript:costgrn('+cl+')" ></ids>';
			jQuery("#uncostlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;
$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#uncostlist','#uncostpager',true, null, null,true,true,true);
$conn = null;

?>




