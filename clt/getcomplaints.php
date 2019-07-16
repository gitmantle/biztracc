<?php
session_start();

$usersession = $_SESSION['usersession'];

$cltdb = $_SESSION['s_cltdb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];
$usergroup = $row['usergroup'];

$db->closeDB();

//construct where clause 
$where = "WHERE sub_id = ".$subscriber; 

require_once '../includes/jquery/jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "SELECT complaint_id,member_id,complainant,against,received,acknowledged,closed,compensation from ".$cltdb.".complaints ".$where; 

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getcomplaints.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
	"caption"=>"Complaints Register",						
    "rowNum"=>12,
    "sortname"=>"received",
	"sortorder"=>"desc",
    "rowList"=>array(12,30,50),
	"height"=>350,
	"width"=>900
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("complaint_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("member_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("complainant", array("label"=>"Complainant", "width"=>170));
$grid->setColProperty("against", array("label"=>"Against", "width"=>170));
$grid->setColProperty("received", array("label"=>"Received", "width"=>90, "formatter"=>"date",  "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("acknowledged", array("label"=>"Acknowledged", "width"=>90, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("closed", array("label"=>"Closed", "width"=>90, "formatter"=>"date",  "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("compensation", array("label"=>"Compensation", "width"=>90, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>80));


$ld3event = <<<LD3COMPLETE
function(rowid){
	var ids = jQuery("#complaintslist").getDataIDs(); 
	var totpremium = 0;
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		be = '<img src="../images/edit.png" title="View/Edit" onclick="javascript:editcomplaint('+cl+')" >'; 
		jQuery("#complaintslist").setRowData(ids[i],{act:be});
	} 
}
LD3COMPLETE;

$grid->setGridEvent("loadComplete",$ld3event);


$dcevent = <<<DBLCLICK
function(rowid) {
	var rowdata = $("#complaintslist").getRowData(rowid);
	var memberid = rowdata.member_id;
	editmem(memberid);
}
DBLCLICK;

$grid->setGridEvent('ondblClickRow',$dcevent);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#complaintspager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a complaint", "onClickButton"=>"js: function(){addcomplaint();}")
);
$grid->callGridMethod("#complaintslist", "navButtonAdd", $buttonoptions); 


// Run the script
$grid->renderGrid('#complaintslist','#complaintspager',true, null, null, true,true);

?>




