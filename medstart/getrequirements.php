<?php
session_start();

include_once("../includes/DBClass.php");
$db = new DBClass();
$sessionid = session_id();

$rectable = $_SESSION['s_rectable'];

include '../med/jqns-config.php';


// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// enable debugging
//$grid->debug = true;

// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "select uid,itemid,item,qty,dosage,totcost from ".$rectable." where guid = '".$sessionid."'"; 


// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getrequirements.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "rowNum"=>5,
    "rowList"=>array(5,10,50),
	"height"=>100,
	"width"=>870,
	));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("itemid", array("label"=>"Itemid", "width"=>20, "hidden"=>true));
$grid->setColProperty("item", array("label"=>"Item", "width"=>150));
$grid->setColProperty("qty", array("label"=>"Qty", "width"=>80, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("dosage", array("label"=>"Per", "width"=>80));
$grid->setColProperty("totcost", array("label"=>"Monthly Cost", "width"=>80, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>40));

$ld3event = <<<LD3COMPLETE
function(rowid){
	var ids = jQuery("#reqlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		be = '<img src="../images/delete.png" title="Delete this line" onclick="javascript:delmed('+cl+')" >'; 
		jQuery("#reqlist").setRowData(ids[i],{act:be});
	} 
}
LD3COMPLETE;

$grid->setGridEvent("loadComplete",$ld3event);


// At end call footerData to put total  label
$grid->callGridMethod('#reqlist', 'footerData', array("set",array("dosage"=>"Total period cost incl. GST:")));
// Set which parameter to be sumarized
$summaryrows = array("totcost"=>array("totcost"=>"SUM"));


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"search"=>false,"refresh"=>false, "view"=>false));

// Run the script
$grid->renderGrid('#reqlist','#reqpager',true, $summaryrows, null,true,true,true);

?>



