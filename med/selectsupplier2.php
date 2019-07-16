<?php
session_start();

$cdb = $_SESSION['s_cltdb'];

include '../clt/jq-config.php';

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

$grid->SelectCommand = "select ".$cdb.".members.member_id,".$cdb.".members.lastname as supname from ".$cdb.".members where ".$cdb.".members.membertype  = 'S'";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selectsupplier2.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>'Suppliers',
    "rowNum"=>5,
    "sortname"=>"supname",
    "rowList"=>array(5,50,100,200),
	"height"=>120,
	"width"=>448
	));

// Change some property of the field(s)
$grid->setColProperty("member_id", array("label"=>"Uid", "width"=>20, "hidden"=>true));
$grid->setColProperty("supname", array("label"=>"Supplier", "width"=>200));

// on select row populate the relevant text box and hide the div
$selectac = <<<ACC
function(rowid, selected)
{
    if(rowid != null) {
	var rowdata = $("#selectsuplist2").getRowData(rowid);
	var sname = rowdata.supname;
	var sid = rowdata.member_id;
	var supplier = sname+'~'+sid;
	setsupselect(supplier,2);	
    }
}
ACC;
$grid->setGridEvent('onSelectRow', $selectac);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#selectsuplist2','#selects2pager',true, null, null, true,true);


?>

