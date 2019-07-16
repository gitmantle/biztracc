<?php
session_start();
//ini_set('display_errors', true);

$findb = $_SESSION['s_findb'];

include '../fin/jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Get the needed parameters passed from the main grid

$tfile = $_SESSION['s_tfile'];

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,item,include from ".$findb.".".$tfile." where item in ('page','watermark','image','box1','box2','box3','box4','box5','box6','box7','box8','box9','rbox1','rbox2','rbox3','rbox4','rbox5','rbox6','rbox7','rbox8','rbox9','fromname','fromaddress','toaddress','delivery','header1','header2','ref1','ref2','notes','label1','label2','label3','label4','label5','label6','label7','label8','label9','label10','label11','label12','label13','label14','label15','label16''label17','label18','label19','label20','docdate','gst')";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('../fin/getfindocheader.php');



// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>18,
    "sortname"=>"item",
    "rowList"=>array(18,50,100),
	"height"=>410,
	"width"=>600
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("item", array("label"=>"Item", "width"=>200));
$grid->setColProperty("include", array("label"=>"Include", "width"=>30));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#fdocheadlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#fdocheadlist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Properties" onclick="javascript:editproperties('+cl+')" ></ids>';
			jQuery("#fdocheadlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#fdocheadlist','#fdocheadpager',true, null, null,true,true,true);



?>




