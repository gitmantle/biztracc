<?php
session_start();
error_reporting (E_ALL ^ E_NOTICE);
$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$chargefile = 'ztmp'.$user_id.'_charges';

$db->query("drop table if exists ".$findb.".".$chargefile);
$db->execute();

$db->query("create table ".$findb.".".$chargefile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, supplier varchar(50) default '', acno int(11) default 0, sbno int(11) default 0, descript varchar(50), charge decimal(16,2) default 0 ) engine myisam"); 
$db->execute();

$db->closeDB();


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
$grid->SelectCommand = "select uid,supplier,acno,sbno,descript,charge from ".$findb.".".$chargefile;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getCharges.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Apportion Charges (if applicable)",
    "rowNum"=>3,
    "rowList"=>array(3,20,100),
	"height"=>72,
	"width"=>800
    ));


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("supplier", array("label"=>"Supplier", "width"=>180));
$grid->setColProperty("acno", array("label"=>"Account", "width"=>80, "hidden"=>true));
$grid->setColProperty("sbno", array("label"=>"Sub Acc.", "width"=>80, "hidden"=>true));
$grid->setColProperty("descript", array("label"=>"Charge Description", "width"=>180));
$grid->setColProperty("charge", array("label"=>"Charge", "width"=>80, "align"=>"right","formatter"=>"number"));


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions1 = array("#chargepager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Charge.", "onClickButton"=>"js: function(){addcharge();}")
);
$grid->callGridMethod("#chargelist", "navButtonAdd", $buttonoptions1); 

// Run the script
$grid->renderGrid('#chargelist','#chargepager',true, null, null,true,true,true);
$conn = null;

?>




