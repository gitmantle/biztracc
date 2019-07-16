<?php
session_start();
//ini_set('display_errors', true);
require("../db.php");
$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$drfile = 'ztmp'.$user_id.'_statements';

$moduledb = $_SESSION['s_findb'];
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
// Get the needed parameters passed from the main grid

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,debtor,account,sub,current,d30,d60,d90,d120,address from ".$drfile." where sendby = 'Email'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getstemail.php');

// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Statements to be emailed",
    "rowNum"=>7,
    "sortname"=>"debtor",
    "rowList"=>array(7,50,100),
	"height"=>170,
	"width"=>940
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("debtor", array("label"=>"Debtor", "width"=>150));
$grid->setColProperty("account", array("label"=>"Account", "width"=>70));
$grid->setColProperty("sub", array("label"=>"Sub", "width"=>30));
$grid->setColProperty("current", array("label"=>"Current", "width"=>60, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("d30", array("label"=>"30 day", "width"=>60, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("d60", array("label"=>"60 day", "width"=>60, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("d90", array("label"=>"90 day", "width"=>60, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("d120", array("label"=>"120 day", "width"=>60, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("address", array("label"=>"Email", "width"=>150));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>35));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#emaillist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#emaillist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/delete.png" title="Delete from list" onclick="javascript:delstate('+cl+')" ></ids>';
			jQuery("#emaillist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#emaillist','#emailpager',true, null, null,true,true,true);



?>




