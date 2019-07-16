<?php
session_start();

if(isset($_REQUEST["nm_mask"])) {
	$nm_mask = $_REQUEST['nm_mask'];
} else {
  	$nm_mask = ""; 
}

if(isset($_REQUEST["tr_mask"])) {
	$tr_mask = $_REQUEST['tr_mask'];
} else {
  	$tr_mask = ""; 
}

if(isset($_REQUEST["ar_mask"])) {
	$ar_mask = $_REQUEST['ar_mask'];
} else {
  	$ar_mask = ""; 
}

if(isset($_REQUEST["pc_mask"])) {
	$pc_mask = $_REQUEST['pc_mask'];
} else {
  	$pc_mask = ""; 
}

//construct where clause 
$where = "WHERE 1 = 1"; 
if($nm_mask!='') {
	$where.= " AND upper(street) LIKE '".$nm_mask."%'"; 
}
if($tr_mask!='') {
	$where.= " AND upper(suburb) LIKE '".$tr_mask."%'"; 
}
if($ar_mask!='') {
	$where.= " AND upper(area) LIKE '".$ar_mask."%'"; 
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
$grid->SelectCommand = "SELECT street_id,street,suburb,area,postcode from streets ".$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getstreets.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Street Codes",
    "rowNum"=>12,
    "sortname"=>"street",
    "rowList"=>array(12,30,50),
	"height"=>280,
	"width"=>700
    ));

$grid->addCol(array("name"=>"act"),"first");

// Change some property of the field(s)
$grid->setColProperty("street_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("street", array("label"=>"Street", "width"=>150));
$grid->setColProperty("suburb", array("label"=>"Suburb", "width"=>150));
$grid->setColProperty("area", array("label"=>"Area", "width"=>100));
$grid->setColProperty("postcode", array("label"=>"Post Code", "width"=>70));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$loadevent = <<<LOADCOMPLETE
function(rowid){
  var ids = jQuery("#streetlist").getDataIDs(); 
  for(var i=0;i<ids.length;i++){ 
	  var cl = ids[i]; 
	  be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editstreet('+cl+')" ></ids>'; 
	  se = '<img src="../images/delete.png" title="Delete" onclick="javascript:delstreet('+cl+')" />'; 
	  jQuery("#streetlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+se}) 
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
$buttonoptions = array("#streetpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a rural post code", "onClickButton"=>"js: function(){addstreet();}")
);
$grid->callGridMethod("#streetlist", "navButtonAdd", $buttonoptions); 


// Run the script
$grid->renderGrid('#streetlist','#streetpager',true, null, null, true,true);



?>




