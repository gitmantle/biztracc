<?php
session_start();
//ini_set('display_errors', true);

$findb = $_SESSION['s_findb'];

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


if(isset ($_REQUEST["grp"])) {
    $id = jqGridUtils::Strip($_REQUEST["grp"]);
} else {
    $id = '';
}
$_SESSION['s_stkgroup'] = $id;

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,priceband,pcent,setprice from ".$findb.".stkpricepcent";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getpcents.php');



// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"uid",
    "rowList"=>array(12,50,100),
	"height"=>280,
	"width"=>400
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("priceband", array("label"=>"Price band", "width"=>150));
$grid->setColProperty("pcent", array("label"=>"Percentage", "width"=>70, "align"=>"right", "formatter"=>"number"));
$grid->setColProperty("setprice", array("label"=>"Set Price", "width"=>70, "align"=>"right", "formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#stkpcentlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#stkpcentlist").getRowData(ids[i]);
			var cl = ids[i];
			if (cl > 1) {
				sb = '<img src="../images/delete.png" title="Delete pcentage markup" onclick="javascript:delpcent('+cl+')" ></ids>';
			} else {
				sb = '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			be = '<img src="../images/edit.png" title="Edit percentage markup" onclick="javascript:editpcent('+cl+')" ></ids>';
			jQuery("#stkpcentlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+sb}); 
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
$buttonoptions = array("#stkpcentpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an percentage markup.", "onClickButton"=>"js: function(){addpcent();}")
);
$grid->callGridMethod("#stkpcentlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#stkpcentlist','#stkpcentpager',true, null, null,true,true,true);



?>




