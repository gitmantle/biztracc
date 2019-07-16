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

$obaltable = 'ztmp'.$user_id.'_stkobal';

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
$grid->SelectCommand = "SELECT itemid,groupname,category,itemcode,item,stock,quantity,avgcost from ".$findb.".".$obaltable;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getstk.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Stock Items",
    "rowNum"=>20,
    "sortname"=>"item",
    "rowList"=>array(20,100,200),
	"height"=>400,
	"width"=>900
    ));


$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("itemid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("groupname", array("label"=>"Group", "width"=>75));
$grid->setColProperty("category", array("label"=>"Category", "width"=>75));
$grid->setColProperty("itemcode", array("label"=>"Stock Code", "width"=>50));
$grid->setColProperty("item", array("label"=>"Description", "width"=>200));
$grid->setColProperty("stock", array("label"=>"Type", "width"=>36));
$grid->setColProperty("quantity", array("label"=>"Quantity", "width"=>50, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>3)));
$grid->setColProperty("avgcost", array("label"=>"Avg. Cost", "width"=>50, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>30));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#stkobal").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#stkobal").getRowData(ids[i]);
			var cl = ids[i];
			var stocktake = rowd.stock;
			if (stocktake == 'Stock') {
				be = '<img src="../images/edit.png" title="Add a Stock Item Opening Balance" onclick="javascript:addstkobal('+cl+')" ></ids>';
			} else {
				be = '&nbsp;&nbsp;';
			}
			jQuery("#stkobal").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#stkobal','#stkobalpager',true, null, null,true,true,true);
$conn = null;

?>




