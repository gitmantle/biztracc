<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_journal';

$findb = $_SESSION['s_findb'];

$db->closeDB();

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
$grid->SelectCommand = "select uid,account,note,debit,credit from ".$findb.".".$table;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getjournal.php');

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "sortname"=>"uid",
    "rowList"=>array(10,50,100),
	"height"=>230,
	"width"=>950
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("account", array("label"=>"Account", "width"=>120));
$grid->setColProperty("note", array("label"=>"Note", "width"=>200));
$grid->setColProperty("debit", array("label"=>"Debit", "width"=>100, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("credit", array("label"=>"Credit", "width"=>100, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$crcell = <<< CELLATTR
function (rowid, value, rawObject, colModel, arraydata) {
    return "style='background:#ff9933;color:#000000;' class='crclass' ";
}
CELLATTR;

$grid ->setColProperty('credit', array("cellattr"=>"js:".$crcell));

$drcell = <<< CELLATTR
function (rowid, value, rawObject, colModel, arraydata) {
    return "style='background:#cccccc;color:#000000;' class='drclass' ";
}
CELLATTR;

$grid ->setColProperty('debit', array("cellattr"=>"js:".$drcell));





// At end call footerData to put total  label
$grid->callGridMethod('#journallist', 'footerData', array("note",array("reference"=>"Total:")));
// Set which parameter to be sumarized
$summaryrows = array("debit"=>array("debit"=>"SUM"),"credit"=>array("credit"=>"SUM"));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#journallist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#journallist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Transaction" onclick="javascript:editjournal('+cl+')" ></ids>';
			se = '<img src="../images/delete.png" title="Delete Transaction" onclick="javascript:deljournal('+cl+')" ></ids>';
			jQuery("#journallist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions2 = array("#journalpager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#journallist", "navButtonAdd", $buttonoptions2); 


// Run the script
$grid->renderGrid('#journallist','#journalpager',true, $summaryrows, null,true,true,true);
$conn = null;


?>




