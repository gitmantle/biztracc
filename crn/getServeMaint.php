<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$servicetable = 'ztmp'.$user_id.'_service';

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
$grid->SelectCommand = "select uid,vehicleno,regno,make,lastserviced,odometer,servicedue,cofdate from vehicles";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getServeMaint.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Service and Maintenance",
    "rowNum"=>20,
    "sortname"=>"vehicleno",
    "rowList"=>array(20,50,80),
	"width"=>960,
	"height"=>450
    ));


// Change some property of the field(s)
$grid->addCol(array("name"=>"act"),"last");
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("vehicleno", array("label"=>"Fleet No.", "width"=>70));
$grid->setColProperty("regno", array("label"=>"Registration No", "width"=>70));
$grid->setColProperty("make", array("label"=>"Make", "width"=>80));
$grid->setColProperty("lastserviced", array("label"=>"Last serviced", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("odometer", array("label"=>"Odometer", "width"=>50));
$grid->setColProperty("servicedue", array("label"=>"Service due", "width"=>80));
$grid->setColProperty("cofdate", array("label"=>"COF due", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#svmtlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#svmtlist").getRowData(ids[i]);
			var cl = ids[i];
			var vn = "'"+rowd.vehicleno+"'";
			se = '<img src="../images/spanner.png" title="Servicing and Repair Records" onclick="javascript:viewsm('+vn+')" ></ids>';
			jQuery("#svmtlist").setRowData(ids[i],{act:se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#svmtlist','#svmtpager',true, null, null, true,true);
?>



