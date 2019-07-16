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
// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "select members.member_id,members.lastname,members.firstname from ".$cltdb.".members ".$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getComplainants.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Members",
    "rowNum"=>12,
    "sortname"=>"lastname",
    "rowList"=>array(12,30,50),
	"height"=>270,
	"width"=>560
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("member_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("lastname", array("label"=>"Last Name", "width"=>200));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>110));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#complist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var cl = ids[i];
			var rowdata = $("#complist").getRowData(cl);
			var fn = rowdata.firstname;
			var ln = rowdata.lastname;
			var str =  "'"+ln+", "+fn+"'";
			be = '<img src="../images/add.gif" title="Select as Complainant" onclick="javascript:selcomp('+cl+','+str+')" ></ids>';
			jQuery("#complist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#complist','#comppager',true, null, null, true,true);


?>



