<?php
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

$cltdb = $_SESSION['s_cltdb'];
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$dbp = new DBClass();

$dbp->query("select * from sessions where session = :vusersession");
$dbp->bind(':vusersession', $usersession);
$row = $dbp->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];
$usergroup = $row['usergroup'];

$dbp->closeDB();

$filterfile = "ztmp".$user_id."_filterlist";

include 'jq-config.php';
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

// the actual query for the grid data
$grid->SelectCommand = "select member_id,lastname,firstname,phone,address,suburb,town,postcode,age,staff from ".$cltdb.".".$filterfile;

// Set the table to where you add the data
$grid->table = $filterfile; 

$grid->setPrimaryKeyId('member_id');
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();

// Set the url from where we obtain the data
$grid->setUrl('getFilteredList.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Members and Addresses",
    "rowNum"=>15,
    "sortname"=>"lastname",
    "rowList"=>array(15,30,50),
	"multiselect"=>true,
	"height"=>370,
	"width"=>990
    ));

// Change some property of the field(s)
$grid->setColProperty("member_id", array("label"=>"ID", "width"=>30));
$grid->setColProperty("lastname", array("label"=>"Last Name", "width"=>200));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>150));
$grid->setColProperty("phone", array("label"=>"Phone", "width"=>100));
$grid->setColProperty("address", array("label"=>"Address", "width"=>200));
$grid->setColProperty("suburb", array("label"=>"Suburb", "width"=>100));
$grid->setColProperty("town", array("label"=>"Town", "width"=>100));
$grid->setColProperty("postcode", array("label"=>"Post Code", "width"=>80));
$grid->setColProperty("age", array("label"=>"Age", "width"=>30));
$grid->setColProperty("staff", array("label"=>"Represented by", "width"=>120));

$custom = <<<CUSTOM
jQuery("#btncamp").click(function(){
    var selr = jQuery('#memlistf').jqGrid('getGridParam','selarrrow');
    //if(selr) alert(selr);
    if(selr) {
		updtsel(selr);
	}
});
CUSTOM;
$grid->setJSCode($custom); 


$dblclick = <<<ONDOUBLECLICK
function(rowid) {
	var rowdata = $("#memlistf").getRowData(rowid);
	var memberid = rowdata.member_id;
	viewmem(memberid);
}
ONDOUBLECLICK;
$grid->setGridEvent('ondblClickRow',$dblclick);

$grid->gSQLMaxRows = 4000;

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
$grid->setNavOptions('del',array("jqModal"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoption1 = array("#mempagerf",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Export to Excel", "onClickButton"=>"js: function(){xl7();}")
);
$grid->callGridMethod("#memlistf", "navButtonAdd", $buttonoption1); 

$buttonoption2 = array("#mempagerf",
    array("buttonicon"=>"ui-icon-trash","caption"=>"","position"=>"last","title"=>"Remove selected items from list", "onClickButton"=>"js: function(){trash();}")
);
$grid->callGridMethod("#memlistf", "navButtonAdd", $buttonoption2); 

// Run the script
$grid->renderGrid('#memlistf','#mempagerf',true, null, null, true,true);

?>

