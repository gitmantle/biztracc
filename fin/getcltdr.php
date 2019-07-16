<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$cltdb = $_SESSION['s_cltdb'];

include_once '../clt/jq-config.php';
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

$grid->SelectCommand = "select members.member_id as uid,members.lastname,members.firstname from ".$cltdb.".members ";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getcltdr.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Clients",
    "rowNum"=>13,
    "sortname"=>"lastname",
    "rowList"=>array(13,30,50),
	"height"=>300,
	"width"=>370
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("lastname", array("label"=>"Company/Last Name", "width"=>200));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>160));
$grid->setColProperty("sortcode", array("label"=>"Sort code", "width"=>50, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Act", "width"=>40));

$grid->setSubGrid("getClientAd.php",
        array('Type', 'Addr_1', 'Addr_2', 'Suburb', 'Town'),
        array(50,120,110,110,90),
        array('left','left','left','left','left'));


$ldevent = <<<LOADCOMPLETE
function(rowid){
			var ids = jQuery("#cltlistdr").getDataIDs(); 
			for(var i=0;i<ids.length;i++){ 
				var cl = ids[i]; 
				be = '<img src="../images/add.gif" title="Add as Debtor" onclick="javascript:add2dr('+cl+')" ></ids>'; 
				jQuery("#cltlistdr").setRowData(ids[i],{act:be}) 
			} 
}
LOADCOMPLETE;
$grid->setGridEvent("loadComplete",$ldevent);



$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#cltdrpager2",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Client", "onClickButton"=>"js: function(){addclt('d');}")
);
$grid->callGridMethod("#cltlistdr", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#cltlistdr','#cltdrpager2',true, null, null, true,true);


?>

