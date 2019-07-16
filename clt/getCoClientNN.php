<?php
session_start();
$usersession = $_SESSION['usersession'];

$cltdb = $_SESSION['s_cltdb'];

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
if(isset ($_REQUEST["memid"])) {
    $id = jqGridUtils::Strip($_REQUEST["memid"]);
} else {
    $id = 0;
}
// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query


$grid->SelectCommand = "select comms.comms_id as commid,comms_type.comm_type as commtype,concat_ws(' ',comms.country_code,comms.area_code,comms.comm) as full,comms.preferred as pref,comms.member_id as mid from ".$cltdb.".comms_type,".$cltdb.".comms where comms_type.comms_type_id = comms.comms_type_id and comms.member_id = ".$id;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel(null, array(&$id));
// Set the url from where we obtain the data
$grid->setUrl('getCoClientNN.php');
// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Communications",
    "rowNum"=>15,
    "sortname"=>"commtype",
    "rowList"=>array(15,30,50),
	"height"=>350,
	"width"=>395
    ));


// Change some property of the field(s)
$grid->setColProperty("commid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("commtype", array("label"=>"Type", "width"=>85));
$grid->setColProperty("full", array("label"=>"Detail", "width"=>250));
$grid->setColProperty("pref", array("label"=>"Pref", "width"=>30));
$grid->setColProperty("mid", array("label"=>"Member", "width"=>30, "hidden"=>true));


$dclickevent = <<<DBLCLICK
function(rowid) {
	var rowdata = $("#commslist").getRowData(rowid);
	var memberid = rowdata.mid;
	emailmem(rowid);
}
DBLCLICK;
$grid->setGridEvent('ondblClickRow',$dclickevent);

// Run the script
$grid->renderGrid('#commslist','#commspager',true, null, null, true,true);

?>


