<?php
session_start();

$id = $_SESSION['s_incidentid'];
$admindb = $_SESSION['s_admindb'];

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$incfile = 'ztmp'.$user_id.'_hs';

$moduledb = $_SESSION['h_sdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select incid,subid,coyid from ".$incfile." where uid = ".$id;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);

$moduledb = 'log'.$subid.'_'.$coyid;
mysql_select_db($moduledb) or die(mysql_error());

include 'jq-config_hs.php';

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
$grid->SelectCommand = "select uid,name,injury1,body1,severity,dayslost from incinjuries where incident_id = ".$id;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getincinjuries.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"People Injured in Incident",
    "rowNum"=>15,
    "sortname"=>"name",
    "rowList"=>array(15,50,80),
	"width"=>860,
	"height"=>400
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("name", array("label"=>"Name", "width"=>150));
$grid->setColProperty("injury1", array("label"=>"Injury", "width"=>150));
$grid->setColProperty("body1", array("label"=>"Part of Body", "width"=>150));
$grid->setColProperty("severity", array("label"=>"Severity", "width"=>150));
$grid->setColProperty("dayslost", array("label"=>"Days Lost", "width"=>70));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>60));



// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));


// Run the script
$grid->renderGrid('#iinjurylist','#iinjurypager',true, null, null, true,true);
?>



