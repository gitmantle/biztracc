<?php
session_start();
$usersession = $_SESSION['usersession'];

$dlist = $_SESSION['s_distlist'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

// get stock required for period and stock on hand
$potable = 'ztmp'.$user_id.'_po';

include '../fin/jq-config.php';
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
$grid->SelectCommand = "select uid,medicine,itemcode,unit,noinunit,unitsrequired,unitsonhand,supplier_id,supplier,phone,mobile,email,toorder from ".$potable;


// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getpo.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Insufficient stock of the following:-     EDIT THE ORDER COLUMN TO THE QUANTITIES YOU WANT TO ORDER",
    "rowNum"=>5,
    "sortname"=>"supplier",
    "rowList"=>array(5,20,50),
	"height"=>120,
	"width"=>970,
	"cellEdit"=> true,
	"mtype" => "POST",
	"cellsubmit" => "remote",
	"cellurl" => "includes/ajaxpicked.php"
	));


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "editable"=>false, "hidden"=>true));
$grid->setColProperty("medicine", array("label"=>"Medicine", "width"=>100, "editable"=>false));
$grid->setColProperty("itemcode",array("label"=>"itcode", "width"=>20, "editable"=>false, "hidden"=>true));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>40, "editable"=>false));
$grid->setColProperty("noinunit", array("label"=>"of", "width"=>40, "align"=>"right", "editable"=>false));
$grid->setColProperty("unitsrequired", array("label"=>"Required", "width"=>60, "editable"=>false, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("unitsonhand", array("label"=>"Available", "width"=>60, "editable"=>false, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("supplier_id",array("label"=>"sid", "width"=>20, "editable"=>false, "hidden"=>true));
$grid->setColProperty("supplier", array("label"=>"Supplier", "width"=>120, "editable"=>false));
$grid->setColProperty("phone", array("label"=>"Phone", "width"=>80, "editable"=>false));
$grid->setColProperty("mobile", array("label"=>"Mobile", "width"=>80, "editable"=>false));
$grid->setColProperty("email", array("label"=>"Email", "width"=>100, "editable"=>false));
$grid->setColProperty("toorder", array("label"=>"Order", "width"=>60, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#popager",
    array("buttonicon"=>"ui-icon-cart","caption"=>"","position"=>"first","title"=>"Create Purchase Orders", "onClickButton"=>"js: function(){printpos();}")
);
$grid->callGridMethod("#polist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#polist','#popager',true, null, null, true,true);

?>



