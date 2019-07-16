<?php
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subscriber = $sub_id;

$filterfile = "consfile".$user_id;

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

// the actual query for the grid data
$grid->SelectCommand = "select member_id,lastname,firstname,phone,address,suburb,town,postcode,age,advisor from ".$filterfile;

// Set the table to where you add the data
$grid->table = $filterfile; 

$grid->setPrimaryKeyId('member_id');
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();

// Set the url from where we obtain the data
$grid->setUrl('getConsolidatedList.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Members and Addresses",
    "rowNum"=>12,
    "sortname"=>"lastname",
	"multiselect"=>true,
    "rowList"=>array(12,30,50),
	"height"=>280,
	"width"=>990
    ));

// Change some property of the field(s)
$grid->setColProperty("member_id", array("label"=>"ID", "width"=>60));
$grid->setColProperty("lastname", array("label"=>"Last Name", "width"=>200));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>150));
$grid->setColProperty("phone", array("label"=>"Phone", "width"=>100));
$grid->setColProperty("address", array("label"=>"Address", "width"=>200));
$grid->setColProperty("suburb", array("label"=>"Suburb", "width"=>100));
$grid->setColProperty("town", array("label"=>"Town", "width"=>100));
$grid->setColProperty("postcode", array("label"=>"Post Code", "width"=>75));
$grid->setColProperty("age", array("label"=>"Age", "width"=>30));
$grid->setColProperty("advisor", array("label"=>"Advisor", "width"=>100));

$dblclick = <<<ONDOUBLECLICK
function(rowid) {
	var rowdata = $("#memlistc").getRowData(rowid);
	var memberid = rowdata.member_id;
	viewmem(memberid);
}
ONDOUBLECLICK;
$grid->setGridEvent('ondblClickRow',$dblclick);

$grid->gSQLMaxRows = 4000;

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator',array('add'=>false,'edit'=>false));
$grid->setNavOptions('del',array("jqModal"=>false));

// Run the script
$grid->renderGrid('#memlistc','#mempagerc',true, null, null, true,true);

?>

