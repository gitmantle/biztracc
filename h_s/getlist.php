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

$incfile = 'ztmp'.$user_id.'_hs';

include 'jq-config.php';

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
$grid->SelectCommand = "select uid,incid,coyname,date_incident,latitude,longitude,road,incident_type,harm_people,damage_property from ".$incfile;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getlist.php');

// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Incidents",
    "rowNum"=>20,
    "sortname"=>"date_incident",
	"sortorder"=>"desc",
    "rowList"=>array(20,50,80),
	"width"=>950,
	"height"=>460
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>30, "hidden"=> true));
$grid->setColProperty("coyname", array("label"=>"Contractee", "width"=>100));
$grid->setColProperty("incid", array("label"=>"Incident #", "width"=>35));
$grid->setColProperty("coyid", array("label"=>"CID", "width"=>30, "hidden"=> true));
$grid->setColProperty("date_incident", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("latitude", array("label"=>"Lat", "width"=>50, "hidden"=> true));
$grid->setColProperty("longitude", array("label"=>"Long", "width"=>50, "hidden"=> true));
$grid->setColProperty("road", array("label"=>"Road", "width"=>80));
$grid->setColProperty("incident_type", array("label"=>"Type of Incident", "width"=>100));
$grid->setColProperty("harm_people", array("label"=>"Harm", "width"=>75));
$grid->setColProperty("damage_property", array("label"=>"Damage", "width"=>75));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>75));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#listlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#listlist").getRowData(ids[i]);
			var cl = ids[i];
			var lat = rowd.latitude;
			var long = rowd.longitude;
			var coord = "'"+lat+','+long+"'";
			var ci = rowd.incid;
			var cis = ci.toString();
			var cn = "'"+rowd.coyname+" "+cis+"'";
			be = '<img src="../images/edit.png" title="View Incident" onclick="javascript:editincident('+cl+')" ></ids>';
			se = '<img src="../images/map.gif" title="Incident Location" onclick="javascript:mapincident('+cn+','+coord+')" >';
			pe = '<img src="../images/printer.gif" title="Print Incident Report" onclick="javascript:printincident('+cl+')" >';
			jQuery("#listlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+se+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+pe});  
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false,"search"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions1 = array("#listpager",
    array("buttonicon"=>"ui-icon-zoomout","caption"=>"","position"=>"first","title"=>"Unfilter", "onClickButton"=>"js: function(){closefilters();}")
);
$grid->callGridMethod("#listlist", "navButtonAdd", $buttonoptions1); 

$buttonoptions = array("#listpager",
    array("buttonicon"=>"ui-icon-zoomin","caption"=>"","position"=>"first","title"=>"Filter", "onClickButton"=>"js: function(){showfilters();}")
);
$grid->callGridMethod("#listlist", "navButtonAdd", $buttonoptions); 


// Run the script
$grid->renderGrid('#listlist','#listpager',true, null, null, true,true);
?>



