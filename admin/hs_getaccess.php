<?php
session_start();
//ini_set('display_errors', true);
$_SESSION['s_staffid'] = 0;

require_once '../includes/jquery/jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

if(isset ($_REQUEST["id"])) {
    $id = jqGridUtils::Strip($_REQUEST["id"]);
} else {
    $id = 0;
}

$_SESSION['s_staffid'] = $id;

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select a.access_id,a.coyid,c.coyname,a.module,a.usergroup,a.branch from access a inner join companies c on a.coyid = c.coyid where a.staff_id = ".$id;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('../admin/hs_getaccess.php');

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>7,
    "sortname"=>"coyname",
    "rowList"=>array(7,50,100),
	"height"=>155,
	"width"=>750
    ));


$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("access_id", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("coyid", array("label"=>"coy_ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("coyname", array("label"=>"Company", "width"=>300));
$grid->setColProperty("module", array("label"=>"Module", "width"=>50));
$grid->setColProperty("usergroup", array("label"=>"User Group", "width"=>50));
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>50));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#accesslist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#accesslist").getRowData(ids[i]);
			var cl = ids[i];
			se = '<img src="../images/delete.png" title="Delete Access" onclick="javascript:delaccess('+cl+')" ></ids>';
			jQuery("#accesslist").setRowData(ids[i],{act:se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoption3 = array("#accesspager",
    array("buttonicon"=>"ui-icon-gear","caption"=>"","position"=>"first","title"=>"Add access to a Process", "onClickButton"=>"js: function(){addaccessprc(".$id.");}")
);
$grid->callGridMethod("#userslist", "navButtonAdd", $buttonoption3); 
$buttonoption2 = array("#accesspager",
    array("buttonicon"=>"ui-icon-note","caption"=>"","position"=>"first","title"=>"Add access to Financial Management", "onClickButton"=>"js: function(){addaccessfin(".$id.");}")
);
$grid->callGridMethod("#userslist", "navButtonAdd", $buttonoption2); 
$buttonoption1 = array("#accesspager",
    array("buttonicon"=>"ui-icon-person","caption"=>"","position"=>"first","title"=>"Add access to Client Management", "onClickButton"=>"js: function(){addaccessclt(".$id.");}")
);
$grid->callGridMethod("#userslist", "navButtonAdd", $buttonoption1); 

// Run the script
$grid->renderGrid('#accesslist','#accesspager',true, null, null,true,true,true);
?>




