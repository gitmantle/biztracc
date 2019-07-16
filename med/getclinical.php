<?php
session_start();
ini_set('display_errors', true);
require("../db.php");

$id = $_SESSION["s_memberid"];

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());
	
$findb = $_SESSION['s_findb'];
$meddb = $_SESSION['s_prcdb'];

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


$grid->SelectCommand = "select uid, ddate,test,result from clinical where member_id = ".$id; 

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getclinical.php');

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"ddate",
	"sortorder"=>"desc",
    "rowList"=>array(12,30,50),
	"height"=>115,
	"width"=>900
    ));


$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80));
$grid->setColProperty("test", array("label"=>"Test", "width"=>100));
$grid->setColProperty("result", array("label"=>"Result", "width"=>700));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));


$ldevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#mclinicallist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editclinical('+cl+')" >'; 
		jQuery("#mclinicallist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$ldevent);



// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#clinicalpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a clinical test.", "onClickButton"=>"js: function(){addclinical(".$id.");}")
);
$grid->callGridMethod("#mclinicallist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#mclinicallist','#clinicalpager',true, null, null,true,true,true);

?>




