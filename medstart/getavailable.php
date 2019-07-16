<?php
$admindb = 'cmedsuco_cmeds4u';
require("db1.php");
mysql_select_db($admindb) or die(mysql_error());
$sessionid = session_id().$_SESSION['counter'];

$avtable = 'ztmp_available';

include 'fin/jqns-config.php';


// include the jqGrid Class
require_once "includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "includes/jquery/php/jqGridPdo.php";
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
$grid->SelectCommand = "select distinct item,cost,unit,noinunit,gitem,gcost,gunit,gnoinunit from ".$avtable." where guid = '".$sessionid."'"; 


// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getavailable.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Available Medicines - including GST where applicable",
    "rowNum"=>15,
    "rowList"=>array(15,30,50),
    "sortname"=>"item",
	"height"=>400,
	"width"=>870,
	));


// Change some property of the field(s)
//$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("item", array("label"=>"Medicine", "width"=>150));
$grid->setColProperty("cost", array("label"=>"Price", "width"=>80, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>30));
$grid->setColProperty("noinunit", array("label"=>"of", "width"=>30));
$grid->setColProperty("gitem", array("label"=>"Generic", "width"=>150));
$grid->setColProperty("gcost", array("label"=>"Price", "width"=>80, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("gunit", array("label"=>"Unit", "width"=>30));
$grid->setColProperty("gnoinunit", array("label"=>"of", "width"=>30));



// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"search"=>false,"refresh"=>false, "view"=>false));

// Run the script
$grid->renderGrid('#avlist','#avpager',true, null, null,true,true,true);

?>



