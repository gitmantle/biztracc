<?php

$usersession = $_COOKIE['usersession'];
$dbs = "ken47109_kenny";

require("../db.php");
mysql_select_db($dbs) or die(mysql_error());
$query = "select * from sessions where session_id = ".$usersession;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $sub_id;


require_once '../includes/jquery/jq-config.php';
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
$grid->SelectCommand = "SELECT subemail_id,recipient,email from subemails where sub_id = ".$subscriber;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getbccemail.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Non-member email Recipients - (double click recipient to include)",
    "rowNum"=>12,
    "sortname"=>"recipient",
    "rowList"=>array(12,30,50),
	"hiddengrid"=>true,
	"width"=>400,
	"height"=>180
    ));

//$grid->addCol(array("name"=>"act"),"first");

// Change some property of the field(s)
//$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));
$grid->setColProperty("subemail_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("recipient", array("label"=>"Recipient", "width"=>200));
$grid->setColProperty("email", array("label"=>"Email Address", "width"=>200));

$dclickevent = <<<CCDBLCLICK
function(rowid) {
	var rowdata = $("#bccemaillist").getRowData(rowid);
	var recip = rowdata.recipient;
	addbcc(rowid,recip);
	
	$(".HeaderButton", $('#bccemaillist')[0].grid.cDiv).trigger("click");	
}
CCDBLCLICK;
$grid->setGridEvent('ondblClickRow',$dclickevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#bccemailpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an email address", "onClickButton"=>"js: function(){addrecipient();}")
);
$grid->callGridMethod("#bccemaillist", "navButtonAdd", $buttonoptions); 


// Run the script
$grid->renderGrid('#bccemaillist','#bccemailpager',true, null, null, true,true);


?>




