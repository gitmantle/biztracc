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

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());


if(isset($_REQUEST["coyname_mask"]) && $_REQUEST["coyname_mask"] != '' ) {
	$cn = explode('~',$_REQUEST['coyname_mask']);
	$subc = $cn[0];
	$cr = $cn[1];
	if ($_REQUEST["notcoyname_mask"] == 'N') {
		$coyname_mask = " and sub_contractor = '".$subc."' and crew = ".$cr;
	} else {
		$coyname_mask = " and sub_contractor != '".$subc."' and crew != ".$cr;
	}
} else {
  	$coyname_mask = ""; 
}

if(isset($_REQUEST["fdate_mask"]) && $_REQUEST["fdate_mask"] != '' ) {
	$fdate_mask = " and date_entered  >= '".$_REQUEST['fdate_mask']."'";
} else {
  	$fdate_mask = ""; 
}

if(isset($_REQUEST["tdate_mask"]) && $_REQUEST["tdate_mask"] != '' ) {
	$tdate_mask = " and date_entered  <= '".$_REQUEST['tdate_mask']."'";
} else {
  	$tdate_mask = ""; 
}

if(isset($_REQUEST["lti_mask"]) && $_REQUEST["lti_mask"] != '' ) {
	if ($_REQUEST["notlti_mask"] == 'N') {
		$lti_mask = " and LTI = '".$_REQUEST['lti_mask']."'";
	} else {
		$lti_mask = " and LTI != '".$_REQUEST['lti_mask']."'";
	}
} else {
  	$lti_mask = ""; 
}

if(isset($_REQUEST["type_mask"]) && $_REQUEST["type_mask"] != '' ) {
	if ($_REQUEST["nottype_mask"] == 'N') {
		$type_mask = " and incident_type = '".$_REQUEST['type_mask']."'";
	} else {
		$type_mask = " and incident_type != '".$_REQUEST['type_mask']."'";
	}
} else {
  	$type_mask = ""; 
}

if(isset($_REQUEST["harm_mask"]) && $_REQUEST["harm_mask"] != '' ) {
	if ($_REQUEST["notharm_mask"] == 'N') {
		$harm_mask = " and harm_people = '".$_REQUEST['harm_mask']."'";
	} else {
		$harm_mask = " and harm_people != '".$_REQUEST['harm_mask']."'";
	}
} else {
  	$harm_mask = ""; 
}

if(isset($_REQUEST["damage_mask"]) && $_REQUEST["damage_mask"] != '' ) {
	if ($_REQUEST["notdamage_mask"] == 'N') {
		$damage_mask = " and damage_property = '".$_REQUEST['damage_mask']."'";
	} else {
		$damage_mask = " and damage_property != '".$_REQUEST['damage_mask']."'";
	}
} else {
  	$damage_mask = ""; 
}

if(isset($_REQUEST["reocurr_mask"]) && $_REQUEST["reocurr_mask"] != '' ) {
	if ($_REQUEST["notreocurr_mask"] == 'N') {
		$reocurr_mask = " and reocurr = '".$_REQUEST['reocurr_mask']."'";
	} else {
		$reocurr_mask = " and reocurr != '".$_REQUEST['reocurr_mask']."'";
	}
} else {
  	$reocurr_mask = ""; 
}

if(isset($_REQUEST["terrain_mask"]) && $_REQUEST["terrain_mask"] != '' ) {
	if ($_REQUEST["notterrain_mask"] == 'N') {
		$terrain_mask = " and terrain = '".$_REQUEST['terrain_mask']."'";
	} else {
		$terrain_mask = " and terrain != '".$_REQUEST['terrain_mask']."'";
	}
} else {
  	$terrain_mask = ""; 
}

if(isset($_REQUEST["weather_mask"]) && $_REQUEST["weather_mask"] != '' ) {
	if ($_REQUEST["notweather_mask"] == 'N') {
		$weather_mask = " and weather = '".$_REQUEST['weather_mask']."'";
	} else {
		$weather_mask = " and weather != '".$_REQUEST['weather_mask']."'";
	}
} else {
  	$weather_mask = ""; 
}

if(isset($_REQUEST["temperature_mask"]) && $_REQUEST["temperature_mask"] != '' ) {
	if ($_REQUEST["nottemperature_mask"] == 'N') {
		$temperature_mask = " and temperature = '".$_REQUEST['temperature_mask']."'";
	} else {
		$temperature_mask = " and temperature != '".$_REQUEST['temperature_mask']."'";
	}
} else {
  	$temperature_mask = ""; 
}

if(isset($_REQUEST["wind_mask"]) && $_REQUEST["wind_mask"] != '' ) {
	if ($_REQUEST["notwind_mask"] == 'N') {
		$wind_mask = " and wind = '".$_REQUEST['wind_mask']."'";
	} else {
		$wind_mask = " and wind != '".$_REQUEST['wind_mask']."'";
	}
} else {
  	$wind_mask = ""; 
}

if(isset($_REQUEST["basic1_mask"]) && $_REQUEST["basic1_mask"] != '' ) {
	$b1 = $_REQUEST['basic1_mask'];
	$basic1_mask = " and (basic1 = '".$b1."') or (basic2 = '".$b1."') or (basic3 = '".$b1."') or (basic4 = '".$b1."') or (basic5 = '".$b1."') or (basic6 = '".$b1."') or (basic7 = '".$b1."') or (basic8 = '".$b1."')";
} else {
  	$basic1_mask = ""; 
}

if(isset($_REQUEST["basic2_mask"]) && $_REQUEST["basic2_mask"] != '' ) {
	$b2 = $_REQUEST['basic2_mask'];
	$basic2_mask = " and (basic1 = '".$b2."') or (basic2 = '".$b2."') or (basic3 = '".$b2."') or (basic4 = '".$b2."') or (basic5 = '".$b2."') or (basic6 = '".$b2."') or (basic7 = '".$b2."') or (basic8 = '".$b2."')";
} else {
  	$basic2_mask = ""; 
}


//construct where clause 
$where = " where 1 = 1 ";
$where .= $coyname_mask.$fdate_mask.$tdate_mask.$lti_mask.$type_mask.$harm_mask.$damage_mask.$reocurr_mask.$terrain_mask.$weather_mask.$temperature_mask.$wind_mask.$basic1_mask.$basic2_mask; 

$q = "update incidents set include = 'Y' ".$where;
$r = mysql_query($q) or die(mysql_error().' '.$q);

//construct the select statement
$sel = "select uid,date_incident,latitude,longitude,road,incident_type,client from incidents";

require_once 'jq-config.php';

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
// the actual query for the grid data
$grid->SelectCommand = $sel.$where;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel();

// Set the url from where we obtain the data
$grid->setUrl('getFiltered.php');

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


$grid->addCol(array("name"=>"act"));

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"Incident #", "width"=>50));
$grid->setColProperty("date_incident", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("latitude", array("label"=>"Lat", "width"=>50, "hidden"=> true));
$grid->setColProperty("longitude", array("label"=>"Long", "width"=>50, "hidden"=> true));
$grid->setColProperty("road", array("label"=>"Road", "width"=>80));
$grid->setColProperty("incident_type", array("label"=>"Type of Incident", "width"=>100));
$grid->setColProperty("client", array("label"=>"Client", "width"=>80));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#incidentlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#incidentlist").getRowData(ids[i]);
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
			jQuery("#incidentlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+se+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+pe});  
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#incidentlist','#incidentpager',true, null, null, true,true);


?>



