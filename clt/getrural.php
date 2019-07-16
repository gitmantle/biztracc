<?php
session_start();

if(isset($_REQUEST["tr_mask"])) {
	$tr_mask = $_REQUEST['tr_mask'];
} else {
  	$tr_mask = ""; 
}

if(isset($_REQUEST["pc_mask"])) {
	$pc_mask = $_REQUEST['pc_mask'];
} else {
  	$pc_mask = ""; 
}

//construct where clause 
$where = "WHERE 1 = 1"; 
if($tr_mask!='') {
	$where.= " AND upper(town) LIKE '".$tr_mask."%'"; 
}
if($pc_mask!='') {
	$where.= " AND postcode LIKE '".$pc_mask."%'"; 
}


require_once '../includes/jquery/jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query


// the actual query for the grid data
$grid->SelectCommand = "SELECT rural_id,rd,town,postcode from rural ".$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getrural.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Post Office Codes",
    "rowNum"=>12,
    "sortname"=>"town",
    "rowList"=>array(12,30,50),
	"height"=>280,
	"width"=>560
    ));

$grid->addCol(array("name"=>"act"),"first");

// Change some property of the field(s)
$grid->setColProperty("rural_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("rd", array("label"=>"RD", "width"=>50));
$grid->setColProperty("town", array("label"=>"City", "width"=>150));
$grid->setColProperty("postcode", array("label"=>"Post Code", "width"=>70));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#rurallist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editrural('+cl+')" ></ids>'; 
		se = '<img src="../images/delete.png" title="Delete" onclick="javascript:delrural('+cl+')" />'; 
		jQuery("#rurallist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+se}) 
	} 
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#ruralpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a rural post code", "onClickButton"=>"js: function(){addrural();}")
);
$grid->callGridMethod("#rurallist", "navButtonAdd", $buttonoptions); 


// Run the script
$grid->renderGrid('#rurallist','#ruralpager',true, null, null, true,true);




?>




