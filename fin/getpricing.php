<?php
session_start();
//ini_set('display_errors', true);

$findb = $_SESSION['s_findb'];
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


if(isset ($_REQUEST["grp"])) {
    $id = jqGridUtils::Strip($_REQUEST["grp"]);
} else {
    $id = '';
}
$_SESSION['s_stkgroup'] = $id;

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// enable debugging
//$grid->debug = true;

// the actual query for the grid data
$grid->SelectCommand = "select ".$findb.".pricing.uid,".$findb.".pricing.dracno,".$cltdb.".client_company_xref.member,".$findb.".pricing.stkid,".$findb.".stkmast.item,".$findb.".pricing.price,".$findb.".pricing.units from ".$findb.".pricing join ".$cltdb.".client_company_xref on ".$findb.".pricing.dracno = ".$cltdb.".client_company_xref.drno join ".$findb.".stkmast on ".$findb.".pricing.stkid = ".$findb.".stkmast.itemid";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getpricing.php');



// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "rowList"=>array(12,50,100),
	"height"=>280,
	"width"=>500
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("dracno", array("label"=>"dracno", "width"=>50, "hidden"=>true));
$grid->setColProperty("member", array("label"=>"Debtor", "width"=>100));
$grid->setColProperty("stkid", array("label"=>"stkid", "width"=>50, "hidden"=>true));
$grid->setColProperty("item", array("label"=>"Item", "width"=>100));
$grid->setColProperty("price", array("label"=>"Price", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("units", array("label"=>"Units", "width"=>50));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#pricinglist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#pricinglist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit price for Debtor/Item" onclick="javascript:editprice('+cl+')" ></ids>';
			jQuery("#pricinglist").setRowData(ids[i],{act:be}); 
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
$buttonoptions = array("#pricingpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a new price for Debtor/Item.", "onClickButton"=>"js: function(){addprice();}")
);
$grid->callGridMethod("#pricinglist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#pricinglist','#pricingpager',true, null, null,true,true,true);



?>




