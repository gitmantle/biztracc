<?php
error_reporting (E_ALL ^ E_NOTICE);

session_start();

$coyidno = $_SESSION['s_coyid'];
$lname = $_SESSION['drname'];


include '../clt/jq-config.php';
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

$grid->SelectCommand = "select member_id,lastname,firstname,preferredname from members where (lastname like '%".$lname."%' or preferredname like '%".$lname."%' or soundex(lastname) = soundex('".$lname."'))";


// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getcldr.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"List of existing members like ".$lname,
    "rowNum"=>15,
    "sortname"=>"lastname",
    "rowList"=>array(15,30,50),
	"height"=>300,
	"width"=>850
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("member_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("lastname", array("label"=>"Company/Last Name", "width"=>200));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>160));
$grid->setColProperty("preferredname", array("label"=>"Trading/Preferred Name", "width"=>160));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>40));


$ldevent = <<<LOADCOMPLETE
function(rowid){
			var ids = jQuery("#cldrlist").getDataIDs(); 
			for(var i=0;i<ids.length;i++){ 
				var cl = ids[i]; 
				var ret = $("#cldrlist").getRowData(cl);
				be = '<img src="../images/add.gif" title="Add this Client as a Debtor" onclick="javascript:addasdr('+cl+')" ></ids>'; 
				jQuery("#cldrlist").setRowData(ids[i],{act:be}) 
			} 
}
LOADCOMPLETE;
$grid->setGridEvent("loadComplete",$ldevent);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#cldrlist','#cldrpager2',true, null, null, true,true);


?>

