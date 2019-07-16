<?php
session_start();
ini_set('display_errors', true);

$findb = $_SESSION['s_findb'];

if(isset($_REQUEST["cr_mask"])) {
	$cr_mask = $_REQUEST['cr_mask'];
} else {
  	$cr_mask = ""; 
}


//construct where clause 
$where = " where trackserial = 'No' and stock = 'Stock'";
if($cr_mask!='') {
	$where = " and upper(item) LIKE '".$cr_mask."%'"; 
}

$heading = 'Select Stock Item';


include 'jq-config.php';

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

$grid->SelectCommand = "select itemcode,item,unit,groupid,catid,avgcost from ".$findb.".stkmast".$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selectstktransfer1.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>5,
    "sortname"=>"item",
    "rowList"=>array(5,50,100,200),
	"height"=>120,
	"width"=>448
	));

// Change some property of the field(s)
$grid->setColProperty("itemcode", array("label"=>"Item Code", "width"=>100));
$grid->setColProperty("item", array("label"=>"Item", "width"=>200));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>50, "hidden"=>true));
$grid->setColProperty("groupid", array("label"=>"group", "width"=>50, "hidden"=>true));
$grid->setColProperty("catid", array("label"=>"cat", "width"=>50, "hidden"=>true));
$grid->setColProperty("avgcost", array("label"=>"avgcost", "width"=>50, "hidden"=>true));


// on select row populate the relevant text box and hide the div
$selectac = <<<ACC
function(rowid, selected)
{
    if(rowid != null) {
	var rowdata = $("#selectstklist1").getRowData(rowid);
	var stkname = rowdata.item;
	var stkcode = rowdata.itemcode;
	var stkunit = rowdata.unit;
	var group = rowdata.groupid;
	var cat = rowdata.catid;
	var cost = rowdata.avgcost;
	var stk = stkcode+'~'+stkname+'~'+stkunit+'~'+group+'~'+cat+'~'+cost;
	setstkselect1(stk);	
    }
}
ACC;
$grid->setGridEvent('onSelectRow', $selectac);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#selectstklist1','#selectstkpager1',true, null, null, true,true);

?>

