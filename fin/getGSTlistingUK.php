<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_gst';

$findb = $_SESSION['s_findb'];

include 'jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "select uid,box,subject,amount from ".$findb.".".$table;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getGSTlistingUK.php');


// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "sortname"=>"uid",
    "rowList"=>array(10,20),
	"height"=>250,
	"width"=>850
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("box", array("label"=>"Box", "width"=>30));
$grid->setColProperty("subject", array("label"=>"Subject", "width"=>250));
$grid->setColProperty("amount", array("label"=>"Amount", "width"=>50, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>40, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#gstlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#gstlist").getRowData(ids[i]);
			var cl = ids[i];
			var bx = rowd.box;
			if (bx == 1 || bx == 4 || bx == 6 || bx == 7) {
				be = '<img src="../images/into.png" title="View Transactions" onclick="javascript:viewgstUK('+bx+')" ></ids>';
			} else {
				be = '';
			}
			jQuery("#gstlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);


// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions1 = array("#gstpager",
    array("buttonicon"=>"ui-icon-print","caption"=>"","position"=>"last","title"=>"Output to PDF.", "onClickButton"=>"js: function(){gst2pdf();}")
);
$grid->callGridMethod("#gstlist", "navButtonAdd", $buttonoptions1); 

$buttonoptions2 = array("#gstpager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#gstlist", "navButtonAdd", $buttonoptions2); 



// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#gstlist','#gstpager',true, null, null,true,true,true);
$conn = null;

?>




