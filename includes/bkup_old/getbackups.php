<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

include_once("../DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$table = 'ztmp'.$user_id.'_bkfiles';

$module = $_SESSION['s_module'];

if ($module == 'clt') {
	$moddb = $_SESSION['s_cltdb'];
}
if ($module == 'fin') {
	$moddb = $_SESSION['s_findb'];
}
if ($module == 'log') {
	$moddb = $_SESSION['s_logdb'];
}
if ($module == 'med') {
	$moddb = $_SESSION['s_meddb'];
}


$db->closeDB();

include '../jquery/jq-config.php';

// include the jqGrid Class
require_once "../jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Get the needed parameters passed from the main grid

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,bkfile from ".$moddb.".".$table;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('../includes/bkup/getbackups.php');



// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "sortname"=>"uid",
    "rowList"=>array(10),
	"height"=>280,
	"width"=>400
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("bkfile", array("label"=>"Back-up File", "width"=>250));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>50));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#bkuplist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#bkuplist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/into.png" title="Restore to Database" onclick="javascript:restorebkup('+cl+')" ></ids>';
			jQuery("#bkuplist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#bkuplist','#bkuppager',true, null, null,true,true,true);



?>




