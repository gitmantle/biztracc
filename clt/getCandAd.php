<?php
session_start();

$cltdb = $_SESSION['s_cltdb'];

$id = $_SESSION['s_mid'];
if (isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
}

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
$grid->SelectCommand = "select addresses.address_id,address_type.address_type,addresses.location,addresses.street_no,concat(addresses.ad1,' ',addresses.ad2) as addr,addresses.suburb,addresses.town from ".$cltdb.".addresses,".$cltdb.".address_type where addresses.address_type_id = address_type.address_type_id and addresses.member_id = ".$id;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getCandAd.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
	"caption"=>"Addresses",						
    "rowNum"=>12,
    "sortname"=>"address_type",
    "rowList"=>array(12,30,50),
	"height"=>60,
	"width"=>600
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("address_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("address_type", array("label"=>"Type", "width"=>50));
$grid->setColProperty("location", array("label"=>" ", "width"=>50));
$grid->setColProperty("street_no", array("label"=>"Address", "width"=>110));
$grid->setColProperty("addr", array("label"=>" ", "width"=>110));
$grid->setColProperty("suburb", array("label"=>"Suburb", "width"=>90));
$grid->setColProperty("town", array("label"=>"Town", "width"=>90));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>60));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#cadlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowdata = $("#cadlist").getRowData(cl);
		var ad1 = rowdata.ad1;
		var ad2 = rowdata.ad2;
		var suburb = rowdata.suburb;
		var town = rowdata.town;
		var country = rowdata.country;
		var from = "'c'";
		var address = "'"+ad1+','+ad2+','+suburb+','+town+','+country+"'";
		be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editad('+cl+','+from+')" >'; 
		mp = '<img src="../images/map.gif" title="Map" onclick="javascript:mapad('+address+')" >';
		jQuery("#cadlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;'+mp}); 
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
$buttonoptions = array("#cadpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an address", "onClickButton"=>"js: function(){addad();}")
);
$grid->callGridMethod("#cadlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#cadlist','#cadpager',true, null, null, true,true);



?>




