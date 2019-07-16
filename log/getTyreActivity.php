<?php
session_start();
//ini_set('display_errors', true);
require("../db.php");

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

include '../fin/jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Get the needed parameters passed from the main grid

if(isset ($_REQUEST["its"])) {
    $itsno = jqGridUtils::Strip($_REQUEST["its"]);
	$i = explode('~',$itsno);
	$itid = $i[0];
	$sno = $i[1];
} else {
	$itid = "";	
	$sno = "";
}

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select stkserials.uid,stkserials.item,stkserials.serialno,stkserials.ref_no,stkserials.date,stkserials.activity from stkserials where stkserials.itemcode = '".$itid."' and stkserials.serialno = '".$sno."'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getTyreActivity.php');

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>7,
    "sortname"=>"uid",
    "rowList"=>array(7,50,100),
	"height"=>161,
	"width"=>860
    ));


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("item", array("label"=>"Item", "width"=>190));
$grid->setColProperty("serialno", array("label"=>"Serial Number", "width"=>100));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>100));
$grid->setColProperty("date", array("label"=>"Date", "width"=>100, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("activity", array("label"=>"Activity", "width"=>100));

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#tyreactivitylist','#tyreactivitypager',true, null, null,true,true);

?>




