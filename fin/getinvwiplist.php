<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', true);
$usersession = $_SESSION['usersession'];
$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$wiptable = 'ztmp'.$user_id.'_wip';

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

if(isset($_REQUEST["dracc"])) {
	$dr_mask = $_REQUEST['dracc'];
} else {
  	$dr_mask = ""; 
}

$heading = 'Select hours to invoice';

// enable debugging
//$grid->debug = true;

$grid->SelectCommand = "select uid,datestarted,hours from ".$findb.".".$wiptable;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getinvwiplist.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>15,
    "sortname"=>"datestarted",
    "multiselect"=>true,
    "rowList"=>array(15,50,100,200),
	"height"=>360,
	"width"=>280
	));

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"Uid", "width"=>20, "hidden"=>true));
$grid->setColProperty("datestarted", array("label"=>"Date", "width"=>200, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("hours", array("label"=>"Hours", "width"=>80));

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#invwiplist','#invwippager',true, null, null, true,true);
$conn = null;
?>