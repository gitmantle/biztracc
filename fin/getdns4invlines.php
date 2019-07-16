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

$findb = $_SESSION['s_findb'];

include 'jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

if(isset ($_REQUEST["son"])) {
    $id = jqGridUtils::Strip($_REQUEST["son"]);
} else {
    $id = '';
}

$db->closeDB();

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select item,quantity,unit,currency,price*rate as price,value*rate as totvalue from ".$findb.".invtrans where ref_no = '".$id."'"; 

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getdns4invlines.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Delivery Note Items",
    "rowNum"=>5,
    "sortname"=>"item",
    "rowList"=>array(5,30,50),
	"height"=>130,
	"width"=>800
    ));


// Change some property of the field(s)
$grid->setColProperty("item", array("label"=>"Item", "width"=>100));
$grid->setColProperty("quantity", array("label"=>"Quantity", "width"=>50, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>50));
$grid->setColProperty("currency", array("label"=>" ", "width"=>50));
$grid->setColProperty("price", array("label"=>"Price", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("totvalue", array("label"=>"Value", "width"=>40, "align"=>"right","formatter"=>"number"));

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));


// Run the script
$grid->renderGrid('#dnrowlist','#dnrowpager',true, null, null,true,true,true);

?>




