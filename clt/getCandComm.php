<?php
session_start();

$cltdb = $_SESSION['s_cltdb'];

$id = $_SESSION['s_mid'];
if (isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
}

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
$grid->SelectCommand = "select comms.comms_id,comms_type.comm_type,concat(comms.country_code,' ',comms.area_code,' ',comms.comm) as fullcomm from ".$cltdb.".comms_type,".$cltdb.".comms where comms_type.comms_type_id = comms.comms_type_id and comms.member_id = ".$id;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getCandComm.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
	"caption"=>"Communications",						
    "rowNum"=>12,
    "sortname"=>"comm_type",
    "rowList"=>array(12,30,50),
	"height"=>60,
	"width"=>385
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("comms_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("comm_type", array("label"=>"Type", "width"=>80));
$grid->setColProperty("fullcomm", array("label"=>"Communication", "width"=>130));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>40));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#ccommslist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var from = "'c'";
		be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editcomm('+cl+','+from+')" >'; 
		jQuery("#ccommslist").setRowData(ids[i],{act:be}); 
	} 
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#ccommpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Phone Number etc.", "onClickButton"=>"js: function(){addcomm();}")
);
$grid->callGridMethod("#ccommslist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#ccommslist','#ccommpager',true, null, null, true,true);

?>




