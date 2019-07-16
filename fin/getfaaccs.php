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
$_SESSION['s_faccgroup'] = $id;

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,branch,asset,cost from ".$findb.".fixassets where hcode = '".$id."'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getfaaccs.php');



// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"asset",
    "rowList"=>array(12,30,50),
	"height"=>280,
	"width"=>620
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>50));
$grid->setColProperty("asset", array("label"=>"Asset", "width"=>300));
$grid->setColProperty("cost", array("label"=>"Cost", "width"=>30, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#faacclist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#faacclist").getRowData(ids[i]);
			var cl = ids[i];
			var ac = rowd.accountno;
			var br = "'"+rowd.branch+"'";
			var cst = rowd.cost;
			be = '<img src="../images/edit.png" title="Edit Account" onclick="javascript:editfa('+cl+')" ></ids>';
			if (cst == 0) {
				pe = '<img src="../images/Asset_add.png" title="Purchase this Asset" onclick="javascript:purchasset('+cl+')" ></ids>';
			} else {
				pe = '<img src="../images/Asset_sell.png" title="Sell this Asset" onclick="javascript:sellasset('+cl+')" ></ids>';
			}
			de = '<img src="../images/delete.png" title="Delete Account" onclick="javascript:delfa('+cl+')" ></ids>';
			jQuery("#faacclist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+pe+'&nbsp;&nbsp;&nbsp;'+de}); 
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
$buttonoptions = array("#faaccpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an account.", "onClickButton"=>"js: function(){addfa();}")
);
$grid->callGridMethod("#faacclist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#faacclist','#faaccpager',true, null, null,true,true,true);



?>




