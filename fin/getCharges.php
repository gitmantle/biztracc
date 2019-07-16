<?php
session_start();
$usersession = $_SESSION['usersession'];

$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$chargefile = 'ztmp'.$user_id.'_charges';

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
$grid->SelectCommand = "select uid,supplier,acno,sbno,descript,currency,charge from ".$findb.".".$chargefile;

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
	"width"=>900
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("supplier", array("label"=>"Supplier", "width"=>180));
$grid->setColProperty("acno", array("label"=>"Account", "width"=>80, "hidden"=>true));
$grid->setColProperty("sbno", array("label"=>"Sub Acc.", "width"=>80, "hidden"=>true));
$grid->setColProperty("descript", array("label"=>"Charge Description", "width"=>180));
$grid->setColProperty("currency", array("label"=>" ", "width"=>40, "align"=>"right"));
$grid->setColProperty("charge", array("label"=>"Charge", "width"=>80, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#chargelist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#chargelist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Charge" onclick="javascript:editcharge('+cl+')" ></ids>';
			se = '<img src="../images/delete.png" title="Delete Charge" onclick="javascript:delcharge('+cl+')" />'; 
			jQuery("#chargelist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;
$grid->setGridEvent("loadComplete",$loadevent);


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




