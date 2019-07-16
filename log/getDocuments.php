<?php
session_start();


$admindb = $_SESSION['s_admindb'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$ad = $admin;
$sid = $subid;


$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

require_once '../includes/jquery/jq-config.php';

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
// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "select distinct users.uid,users.ufname,users.ulname from users,access where users.uid = access.staff_id and users.sub_id = ".$sid." and access.module = 'prc'";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getDocuments.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Documents signed by Staff",
    "rowNum"=>20,
    "sortname"=>"ulname",
    "rowList"=>array(20,50,80),
	"width"=>760,
	"height"=>450
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ufname", array("label"=>"First Name", "width"=>100));
$grid->setColProperty("ulname", array("label"=>"Last Name", "width"=>100));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>60));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#documentlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#documentlist").getRowData(ids[i]);
			var cl = ids[i];
			var ad = rowd.admin;
			var sid = rowd.sid;
			var stf = "'"+rowd.ufname+" "+rowd.ulname+"'";
			if (ad == 'Y') {
				ae = '<img src="../images/delete.png" title="Delete document" onclick="javascript:deldocument('+cl+')" ></ids>';
			} else {
				ae = '&nbsp;&nbsp;&nbsp;';
			}
			be = '<img src="../images/edit.png" title="View Signed Documents" onclick="javascript:viewdocuments('+cl+','+stf+')" ></ids>';
			jQuery("#documentlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+ae}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));


// Run the script
$grid->renderGrid('#documentlist','#documentpager',true, null, null, true,true);
?>



