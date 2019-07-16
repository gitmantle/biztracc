<?php
session_start();
$admindb = $_SESSION['s_admindb'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

include 'jq-config.php';

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
// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "select ruckms.uid,ruckms.docket_no,ruckms.vehicle,ruckms.ferry_id,concat(routes.route,' ',routes.compartment) as rtc,ruckms.ddate,ruckms.ruclicence,ruckms.private,ruckms.claimed from ruckms,routes where ruckms.routeid = routes.uid";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getrucvehicles.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Vehicle Mileage and RUC Details",
    "rowNum"=>20,
    "sortname"=>"docket_no",
    "rowList"=>array(20,50,80),
	"width"=>960,
	"height"=>460
    ));


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>80, "hidden"=>true));
$grid->setColProperty("docket_no", array("label"=>"Docket", "width"=>80));
$grid->setColProperty("vehicle", array("label"=>"Vehicle No", "width"=>80));
$grid->setColProperty("ferry_id", array("label"=>"Ferry ID", "width"=>80));
$grid->setColProperty("rtc", array("label"=>"Route", "width"=>180));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("ruclicence", array("label"=>"RUC Licence", "width"=>100));
$grid->setColProperty("private", array("label"=>"Private Kms", "width"=>100));
$grid->setColProperty("claimed", array("label"=>"Claimed", "width"=>100));


// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function

$buttonoptions1 = array("#rucvpager",
    array("buttonicon"=>"ui-icon-print","caption"=>"","position"=>"last","title"=>"Print Refund Form", "onClickButton"=>"js: function(){ruc();}")
);
$grid->callGridMethod("#rucvlist", "navButtonAdd", $buttonoptions1); 

// Run the script
$grid->renderGrid('#rucvlist','#rucvpager',true, null, null, true,true);
?>



