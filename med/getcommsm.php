<?php
session_start();
//error_reporting(0);
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

$cltdb = $_SESSION['s_cltdb'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");

mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error().' '.$query);
$row = mysql_fetch_array($result);
extract($row);

$id = $_SESSION["s_memberid"];


include 'jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);

$grid->SelectCommand = "select ".$cltdb.".comms.comms_id as commid,".$cltdb.".comms_type.comm_type as commtype,concat_ws(' ',".$cltdb.".comms.country_code,".$cltdb.".comms.area_code,".$cltdb.".comms.comm) as full,".$cltdb.".comms.preferred as pref,".$cltdb.".comms.member_id as mid from ".$cltdb.".comms_type,".$cltdb.".comms where ".$cltdb.".comms_type.comms_type_id = ".$cltdb.".comms.comms_type_id and ".$cltdb.".comms.member_id = ".$id;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getcommsm.php');

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"commtype",
    "rowList"=>array(12,30,50),
	"height"=>115,
	"width"=>700
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("commid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("commtype", array("label"=>"Type", "width"=>85));
$grid->setColProperty("full", array("label"=>"Detail", "width"=>250));
$grid->setColProperty("pref", array("label"=>"Pref", "width"=>30));
$grid->setColProperty("mid", array("label"=>"Member", "width"=>30, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$ldevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#mcommslist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var from = "'m'";
		var rowd = $("#mcommslist").getRowData(ids[i]);
		var memid = rowd.mid;
		var ctype = rowd.commtype;
		if (ctype == 'Email') {
			em = '<img src="../images/email.png" title="Email" onclick="javascript:emailmem('+cl+')" >'; 
		} else {
			em = "";	
		}
		be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editcomm('+cl+','+from+')" >'; 
		se = '<img src="../images/delete.png" title="Delete" onclick="javascript:delcomm('+cl+','+from+')" />'; 
		jQuery("#mcommslist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se+'&nbsp;&nbsp;&nbsp;'+em}); 
	}
}
LOADCOMPLETE;

$dcevent = <<<DBLCLICK
function(rowid) {
	var rowdata = $("#mcommslist").getRowData(rowid);
	var memberid = rowdata.mid;
	emailmem(rowid,memberid);
}
DBLCLICK;

$grid->setGridEvent("loadComplete",$ldevent);

$grid->setGridEvent('ondblClickRow',$dcevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#mcommpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Phone Number etc.", "onClickButton"=>"js: function(){addcomm('m');}")
);
$grid->callGridMethod("#mcommslist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#mcommslist','#mcommpager',true, null, null,true,true,true);

?>




