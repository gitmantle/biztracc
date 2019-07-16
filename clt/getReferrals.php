<?php
session_start();
$usersession = $_COOKIE['usersession'];
$dbase = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($dbase) or die(mysql_error());

$query = "select * from sessions where session_id = ".$usersession;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $sub_id;

//construct where clause 
$where = "WHERE referrals.staff_id = staff.staff_id and referrals.sub_id = ".$subscriber; 


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
$grid->SelectCommand = "select referrals.referral_id,referrals.lastname,referrals.firstname,referrals.preferred,concat_ws(' ',referrals.country,referrals.area,referrals.comm) as phone,referrals.phoned,concat_ws(' ',staff.firstname,staff.lastname) as staffname from referrals,staff ".$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getReferrals.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Leads",
    "rowNum"=>12,
    "sortname"=>"lastname",
    "rowList"=>array(12,30,50),
	"height"=>270,
	"width"=>900
    ));

$grid->addCol(array("name"=>"act"),"first");

// Change some property of the field(s)
$grid->setColProperty("referral_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>90));
$grid->setColProperty("lastname", array("label"=>"Last Name", "width"=>210));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>105));
$grid->setColProperty("preferred", array("label"=>"Preferred", "width"=>100));
$grid->setColProperty("phone", array("label"=>"Phone", "width"=>150));
$grid->setColProperty("staffname", array("label"=>"Staff Member", "width"=>100));
$grid->setColProperty("phoned", array("label"=>"Phoned", "width"=>60));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#referrallist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#referrallist").getRowData(ids[i]);
			var cl = ids[i];
			var rowdata = $("#referrallist").getRowData(cl);
			var phoned = "'"+rowdata.phoned+"'";
			be = '<img src="../images/edit.png" title="Edit Lead" onclick="javascript:editreferral('+cl+')" ></ids>';
			ae = '<img src="../images/add.gif" title="Add Lead to Members" onclick="javascript:add2mem('+cl+')" ></ids>';
			ph = '<img src="../images/Phone.gif" title="Note phonecall to lead" onclick="javascript:addphone('+cl+','+phoned+')" ></ids>';
			se = '<img src="../images/delete.png" title="Delete Lead" onclick="javascript:delreferral('+cl+')" >'; 
			jQuery("#referrallist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+ae+'&nbsp;&nbsp;&nbsp;'+ph+'&nbsp;&nbsp;&nbsp;'+se}); 
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
$buttonoptions = array("#referralpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a referral", "onClickButton"=>"js: function(){addreferral();}")
);
$grid->callGridMethod("#referrallist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#referrallist','#referralpager',true, null, null, true,true);


?>



