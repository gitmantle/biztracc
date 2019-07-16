<?php
session_start();
$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error().' '.$query);
$row = mysql_fetch_array($result);
extract($row);

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$banktable = 'ztmp'.$user_id.'_bank';

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

// the actual query for the grid data
$grid->SelectCommand = "select uid,ddate,debit,credit,reference,description,reconciled from ".$banktable;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getbanktrans.php');

// Set some grid options
$grid->setGridOptions(array(
	"caption"=>"Unreconciled items in bank's accounts",						
    "rowNum"=>100,
    "sortname"=>"ddate",
    "rowList"=>array(100,200),
	"height"=>460,
	"width"=>470
    ));


$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("debit", array("label"=>"Deposit", "width"=>75, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("credit", array("label"=>"Payment", "width"=>75, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("reference", array("label"=>"Ref.", "width"=>50));
$grid->setColProperty("description", array("label"=>"Description", "width"=>100));
$grid->setColProperty("reconciled", array("label"=>"Recon", "width"=>25, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#banktranslist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#bankreclist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/into.png" title="Reconciled" onclick="javascript:recon('+cl+')" ></ids>';
			jQuery("#banktranslist").setRowData(ids[i],{act:be}); 
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
$buttonoptions = array("#banktranspager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add bank's transactions", "onClickButton"=>"js: function(){addbanktrans();}")
);
$grid->callGridMethod("#banktranslist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#banktranslist','#banktranspager',true, null, null,true,true,true);
?>




