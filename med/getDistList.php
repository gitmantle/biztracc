<?php
session_start();
$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());


include 'jq-config.php';
//require_once "jq-config.php"
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// enable debugging
//$grid->debug = true;


// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "select uid,distperiod,startdate,processed from distlist";


// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getDistList.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Distribution Lists",
    "rowNum"=>18,
    "sortname"=>"uid",
	"sortorder"=>"desc",
    "rowList"=>array(18,30,50),
	"height"=>420,
	"width"=>240
    ));


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("distperiod", array("label"=>"Period", "width"=>70));
$grid->setColProperty("startdate", array("label"=>"Start Date", "width"=>100, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("processed", array("label"=>"Processed", "width"=>70));

// on select row we should post the member id to second table and trigger it to reload the data
$selectdets = <<<DETS
function(rowid, selected)
{
    if(rowid != null) {
        jQuery("#distdetlist").jqGrid('setGridParam',{postData:{did:rowid}});
        jQuery("#distdetlist").trigger("reloadGrid");
    }
}
DETS;
$grid->setGridEvent('onSelectRow', $selectdets);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#distdetlist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#distpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a distribution list", "onClickButton"=>"js: function(){adddistlist();}")
);
$grid->callGridMethod("#distlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#distlist','#distpager',true, null, null, true,true);

?>



