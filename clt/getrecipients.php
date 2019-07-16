<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$cluid = $_SESSION["s_memberid"];

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

include 'jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "SELECT subemail_id,recipient,email from subemails";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getrecipients.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Non-member email Recipients",
    "rowNum"=>12,
    "sortname"=>"recipient",
    "rowList"=>array(12,30,50),
	"width"=>600,
	"height"=>280
    ));

$grid->addCol(array("name"=>"act"),"first");

// Change some property of the field(s)
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));
$grid->setColProperty("subemail_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("recipient", array("label"=>"Recipient", "width"=>200));
$grid->setColProperty("email", array("label"=>"Email Address", "width"=>200));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#subemaillist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editrecipient('+cl+')" ></ids>';
			jQuery("#subemaillist").setRowData(ids[i],{act:be})
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
$buttonoptions = array("#subemailpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an email address", "onClickButton"=>"js: function(){addrecipient();}")
);
$grid->callGridMethod("#subemaillist", "navButtonAdd", $buttonoptions); 



// Run the script
$grid->renderGrid('#subemaillist','#subemailpager',true, null, null, true,true);


?>




