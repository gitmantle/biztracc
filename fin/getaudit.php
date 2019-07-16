<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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

// enable debugging
//$grid->debug = true;

// the actual query for the grid data
$grid->SelectCommand = "SELECT uid,entrydate,entrytime,acc2dr,brdr,subdr,acc2cr,brcr,subcr,ddate,reference,descript1,amount,tax,username from ".$findb.".audit";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getaudit.php');

// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Audit Trail",
    "rowNum"=>20,
    "sortname"=>"entrydate",
    "rowList"=>array(20,100,200),
	"height"=>475,
	"width"=>975
    ));

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("entrydate", array("label"=>"Entered", "width"=>80));
$grid->setColProperty("entrytime", array("label"=>"at", "width"=>70));
$grid->setColProperty("acc2dr", array("label"=>"Debit", "width"=>75));
$grid->setColProperty("brdr", array("label"=>"Branch", "width"=>70));
$grid->setColProperty("subdr", array("label"=>"Sub", "width"=>30));
$grid->setColProperty("acc2cr", array("label"=>"Credit", "width"=>75));
$grid->setColProperty("brcr", array("label"=>"Branch", "width"=>70));
$grid->setColProperty("subcr", array("label"=>"Sub", "width"=>30));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80));
$grid->setColProperty("reference", array("label"=>"Ref", "width"=>40));
$grid->setColProperty("descript1", array("label"=>"Description", "width"=>100));
$grid->setColProperty("amount", array("label"=>"Amount",  "width"=>80, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("tax", array("label"=>"Tax",  "width"=>50, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("user", array("label"=>"User", "width"=>60));

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#auditlist','#auditpager',true, null, null,true,true,true);
$conn = null;

?>




