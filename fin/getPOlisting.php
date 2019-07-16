<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db_po = new DBClass();
$db_it = new DBClass();

$db_po->query("select * from sessions where session = :vusersession");
$db_po->bind(':vusersession', $usersession);
$row = $db_po->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];
$table = 'ztmp'.$user_id.'_grn2po';

$db_po->closeDB();

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

$grid->SelectCommand = "select uid,ddate,item,ref_no,unit,quantity,supplied,thisgrn from ".$findb.".".$table;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getPOlisting.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Allocations against Purchase Orders",
    "rowNum"=>10,
    "sortname"=>"ddate",
	"rowList"=>array(10,30,50),
	"height"=>450,
	"width"=>950
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>70, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("item", array("label"=>"Item", "width"=>200));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>80));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>80));
$grid->setColProperty("quantity", array("label"=>"Ordered", "width"=>80, "align"=>"right"));
$grid->setColProperty("supplied", array("label"=>"Supplied to date", "width"=>87, "align"=>"right"));
$grid->setColProperty("thisgrn", array("label"=>"Allocate to this GRN", "width"=>130, "align"=>"right"));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#polist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#polist").getRowData(ids[i]);
			var cl = ids[i];
			var need = rowd.quantity - rowd.supplied;
			var refno = "'"+rowd.ref_no+"'";
			be = '<img src="../images/outof.png" title="Edit quantity to allocate against PO" onclick="javascript:tempgrn2po('+cl+','+refno+','+need+')" ></ids>';
			jQuery("#polist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#polist','#popager2',true, null, null, true,true);


?>

