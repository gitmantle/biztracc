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
$subscriber = $subid;
$sname = $row['uname'];

$pcfile = 'ztmp'.$user_id.'_pc';

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
$grid->SelectCommand = "select uid,item,asellprice,ssellprice from ".$findb.".".$pcfile;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('../fin/getpc.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>'Price Comparison - (including Tax)',
    "rowNum"=>20,
    "sortname"=>"item",
    "rowList"=>array(20,100,200),
	"height"=>460,
	"width"=>890
    ));



// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"UID", "width"=>25, "hidden"=>true));
$grid->setColProperty("item", array("label"=>"Item", "width"=>300));
$grid->setColProperty("asellprice", array("label"=>"Based on Avg. Cost", "width"=>100, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("ssellprice", array("label"=>"Based on fixed price", "width"=>100, "align"=>"right","formatter"=>"number"));


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#pclist','#pcpager',true, null, null,true,true,true);
$conn = null;

?>




