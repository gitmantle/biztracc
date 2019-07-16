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

if(isset($_REQUEST["nm_mask"])) {
	$nm_mask = $_REQUEST['nm_mask'];
} else {
  	$nm_mask = ""; 
}

$cltdb = $_SESSION['s_cltdb'];

$db->closeDB();


//construct where clause 
$dep = $_SESSION['s_udepot'];
if ($dep == 0) {
	$where = "WHERE membertype  = 'M' "; 
} else {
	$where = "WHERE membertype  = 'M' and depot = ".$dep; 
}
if($nm_mask!='') {
	$where.= " AND lastname LIKE '".$nm_mask."%'"; 
}

include '../clt/jq-config.php';
//require_once "jq-config.php"
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
$grid->SelectCommand = "select members.member_id,members.lastname,members.firstname,members.preferredname,members.dob,members.checked from ".$cltdb.".members ".$where;


// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getMembers.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Members and Addresses",
    "rowNum"=>12,
    "sortname"=>"lastname",
    "rowList"=>array(12,30,50),
	"height"=>270,
	"width"=>560
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("member_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));
$grid->setColProperty("checked", array("label"=>"Checked", "width"=>20, "hidden"=>true));
$grid->setColProperty("lastname", array("label"=>"Last Name", "width"=>210));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>100));
$grid->setColProperty("preferredname", array("label"=>"Preferred", "width"=>75));
$grid->setColProperty("dob", array("label"=>"Age", "width"=>30));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

// point to Age column to use the function named getAge
$grid->setColProperty('dob', array('formatter'=>'js:getAge'));

$getAge = <<<GETAGE
function getAge (cellValue, options, rowdata) 
{

	var dt = cellValue.split('-');
	var by = dt[0];
	var bm = dt[1];
	var bd = dt[2]; 
	
	var d = new Date();
	var curr_day = d.getDate();
	var curr_month = d.getMonth()+1;
	var curr_year = d.getFullYear();
	var birthYear = parseInt(by);
	var birthMonth = parseInt(bm);
	var birthDay = parseInt(bd);
	var age = curr_year - birthYear;
	if (curr_month < birthMonth || (curr_month == birthMonth && curr_day < birthDay)) {
		age = age - 1;
	}
	
	if(by == '0000') {
		age = 0;
	}
	return age;

}
GETAGE;
$grid->setJSCode($getAge); 



$grid->setSubGrid("getAdClientNS.php",
        array('Type', 'Addr_1', 'Addr_2', 'Suburb', 'Town'),
        array(50,120,110,110,90),
        array('left','left','left','left','left'));


// on select row we should post the member id to second table and trigger it to reload the data
$selectcomms = <<<COMMS
function(rowid, selected)
{
    if(rowid != null) {
        jQuery("#commslist").jqGrid('setGridParam',{postData:{memid:rowid}});
        jQuery("#commslist").trigger("reloadGrid");
    }
}
COMMS;
$grid->setGridEvent('onSelectRow', $selectcomms);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#commslist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#memlist2").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#memlist2").getRowData(ids[i]);
			var chk = rowd.checked;
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Member" onclick="javascript:editmem('+cl+')" ></ids>';
			if (chk == 'Yes') {
				ck = '<img src="../images/tick.png" title="Checked">'; 
			} else {
				ck = '<img src="../images/close.png" title="Not Checked">'; 
			}
			se = '<img src="../images/delete.png" title="Delete Member" onclick="javascript:delmem('+cl+')" >'; 
			jQuery("#memlist2").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp'+ck+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$dcevent = <<<DBLCLICK
function(rowid) {
	var rowdata = $("#memlist2").getRowData(rowid);
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
$buttonoptions = array("#mempager2",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a member", "onClickButton"=>"js: function(){addmem();}")
);
$grid->callGridMethod("#memlist2", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#memlist2','#mempager2',true, null, null, true,true);

?>



