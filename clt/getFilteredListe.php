<?php
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

$cltdb = $_SESSION['s_cltdb'];
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];
$usergroup = $row['usergroup'];

$db->closeDB();

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
$grid->SelectCommand = "select member_id,lastname,firstname,phone,email,age,dob,staff from ".$cltdb.".".$filterfile." where email != ''";


// Set the table to where you add the data
$grid->table = $filterfile; 

$grid->setPrimaryKeyId('member_id');
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();

// Set the url from where we obtain the data
$grid->setUrl('getFilteredListe.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Members and Email Addresses",
    "rowNum"=>12,
    "sortname"=>"lastname",
    "rowList"=>array(12,30,50),
	"multiselect"=>true,
	"height"=>280,
	"width"=>960
    ));

// Change some property of the field(s)
$grid->setColProperty("member_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("lastname", array("label"=>"Last Name", "width"=>200));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>140));
$grid->setColProperty("phone", array("label"=>"Phone", "width"=>90));
$grid->setColProperty("email", array("label"=>"Email", "width"=>200));
$grid->setColProperty("age", array("label"=>"Age", "width"=>100));
$grid->setColProperty("dob", array("label"=>"Birth Date", "width"=>100, "formatter"=>"date"));
$grid->setColProperty("staff", array("label"=>"Represented by", "width"=>120));

$dblclick = <<<ONDOUBLECLICK
function(rowid) {
	var rowdata = $("#memliste").getRowData(rowid);
	var memberid = rowdata.member_id;
	viewmem(memberid);
}
ONDOUBLECLICK;
$grid->setGridEvent('ondblClickRow',$dblclick);

$grid->gSQLMaxRows = 4000;

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator',array('add'=>false,'edit'=>false,'del'=>false));
//$grid->setNavOptions('del',array("jqModal"=>false));

$buttonoption2 = array("#mempagere",
    array("buttonicon"=>"ui-icon-trash","caption"=>"","position"=>"last","title"=>"Remove selected items from list", "onClickButton"=>"js: function(){trash();}")
);
$grid->callGridMethod("#memliste", "navButtonAdd", $buttonoption2); 

// Run the script
$grid->renderGrid('#memliste','#mempagere',true, null, null, true,true);



?>

