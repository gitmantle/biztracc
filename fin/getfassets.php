<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$usersession = $_SESSION['usersession'];

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
$grid->SelectCommand = "select uid,accountno,branch,sub,asset,bought,depndate,totdep,anndep from ".$findb.".fixassets";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getfassets.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Fixed Assets",
    "rowNum"=>15,
    "sortname"=>"uid",
    "rowList"=>array(15,100,200),
	"height"=>300,
	"width"=>900
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("accountno", array("label"=>"Account", "width"=>20, "hidden"=>true));
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>20, "hidden"=>true));
$grid->setColProperty("sub", array("label"=>"Sub", "width"=>20, "hidden"=>true));
$grid->setColProperty("asset", array("label"=>"Asset", "width"=>100));
$grid->setColProperty("bought", array("label"=>"Bought", "width"=>40, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("depndate", array("label"=>"Last Depreciated", "width"=>40, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("totdep", array("label"=>"Total Depreciation", "width"=>60, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("anndep", array("label"=>"Last Annual Depn.", "width"=>60, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>20));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#faslist").getDataIDs();
	for(var i=0;i<ids.length;i++){
	  var rowd = $("#faslist").getRowData(ids[i]);
	  var cl = ids[i];
	  be = '<img src="../images/into.png" title="Reverse Depreciation" onclick="javascript:rev1dep('+cl+')" >'; 
	  jQuery("#faslist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#faslist','#faspager',true,null, null, true,true,true);
$conn = null;

?>




