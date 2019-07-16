<?php
session_start();
$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;


$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());


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

// enable debugging
//$grid->debug = true;

// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "select members.member_id,members.lastname,members.firstname from members ";


// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getprospects.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Leads, Prospects and Clients",
    "rowNum"=>12,
    "sortname"=>"lastname",
    "rowList"=>array(12,30,50),
	"height"=>270,
	"width"=>300
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("member_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("lastname", array("label"=>"Last Name", "width"=>150));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>100));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));


$grid->setSubGrid("../clt/getAdClientNS.php",
        array('Type', 'Addr_1', 'Addr_2', 'Suburb', 'Town'),
        array(50,120,110,110,90),
        array('left','left','left','left','left'));


// on select row we should post the member id to second table and trigger it to reload the data
$selectquotes = <<<QUOTE
function(rowid, selected)
{
    if(rowid != null) {
        jQuery("#quotelist").jqGrid('setGridParam',{postData:{mem:rowid}});
        jQuery("#quotelist").trigger("reloadGrid");
    }
}
QUOTE;
$grid->setGridEvent('onSelectRow', $selectquotes);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#quotelist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#prospectlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#prospectlist").getRowData(ids[i]);
			var chk = rowd.checked;
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Member" onclick="javascript:editmem('+cl+')" ></ids>';
			jQuery("#prospectlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$dcevent = <<<DBLCLICK
function(rowid) {
	var rowdata = $("#prospectlist").getRowData(rowid);
	var memberid = rowdata.member_id;
	editmem(memberid);
}
DBLCLICK;

$grid->setGridEvent('ondblClickRow',$dcevent);



$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#prospectpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a member", "onClickButton"=>"js: function(){addmem();}")
);
$grid->callGridMethod("#prospectlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#prospectlist','#prospectpager',true, null, null, true,true);

?>



