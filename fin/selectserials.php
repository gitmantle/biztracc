<?php
session_start();
ini_set('display_errors', true);

if (isset($_SESSION['s_itemcode'])) {
	$itc = $_SESSION['s_itemcode'];
} else {
	$itc = "";
}
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subscriber = $row['subid'];
$user_id = $row['user_id'];

$serialtable = 'ztmp'.$user_id.'_serialnos';

$findb = $_SESSION['s_findb'];

$db->closeDB();

$heading = 'Select Serial Numbers';

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

$grid->SelectCommand = "select uid,serialno,location,selected from ".$findb.".".$serialtable." where itemcode = '".$itc."'";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selectserials.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>5,
    "sortname"=>"serialno",
    "rowList"=>array(5,50,100,200),
	"height"=>120,
	"width"=>300
	));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"id", "width"=>20, "hidden"=>true));
$grid->setColProperty("serialno", array("label"=>"Serial Number", "width"=>100));
$grid->setColProperty("location", array("label"=>"Location", "width"=>100));
$grid->setColProperty("selected", array("label"=>"Selected", "width"=>30, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Selected", "width"=>50));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#selectseriallist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#selectseriallist").getRowData(ids[i]);
			var cl = ids[i];
			var sel = rowd.selected;
			if (sel == 'N') {
				be = '<img src="../images/close.png" title="Add to selected" onclick="javascript:serialselect('+cl+')" ></ids>';
			} else {
				be = '<img src="../images/accept.gif" title="De-selected" onclick="javascript:serialdeselect('+cl+')" ></ids>';
			}
			jQuery("#selectseriallist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#selectseriallist','#selectserialpager',true, null, null, true,true);

?>

