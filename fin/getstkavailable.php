<?php
session_start();
$usersession = $_SESSION['usersession'];

$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db_trd = new DBClass();

$db_trd->query("select * from sessions where session = :vusersession");
$db_trd->bind(':vusersession', $usersession);
$row = $db_trd->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$stfile = 'ztmp'.$user_id.'_stkavailable';

$db_trd->closeDB();

include '../fin/jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "SELECT itemcode,item,onhand,uncosted,salesorders,purchaseorders,stkrequired from ".$findb.".".$stfile;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('../fin/getstkavailable.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Stock Items",
    "rowNum"=>20,
    "sortname"=>"item",
    "rowList"=>array(20,100,200),
	"height"=>450,
	"width"=>900
    ));


// Change some property of the field(s)
$grid->setColProperty("itemcode", array("label"=>"Stock Code", "width"=>50));
$grid->setColProperty("item", array("label"=>"Description", "width"=>150));
$grid->setColProperty("onhand", array("label"=>"On hand", "width"=>85));
$grid->setColProperty("uncosted", array("label"=>"Uncosted", "width"=>85));
$grid->setColProperty("salesorders", array("label"=>"Outstanding SOs", "width"=>100));
$grid->setColProperty("purchaseorders", array("label"=>"Outstanding POs", "width"=>100));
$grid->setColProperty("stkrequired", array("label"=>"Required", "width"=>85));


// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#stavailkpager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Output to Excel.", "onClickButton"=>"js: function(){stk2xl();}")
);
$grid->callGridMethod("#stkavaillist", "navButtonAdd", $buttonoptions); 

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#stkavailpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Stock Item.", "onClickButton"=>"js: function(){addstkitem();}")
);
$grid->callGridMethod("#stklist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#stkavaillist','#stkavailpager',true, null, null,true,true,true);
$conn = null;

?>




