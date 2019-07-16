<?php
session_start();
ini_set('display_errors', true);
require("../db.php");

$id = $_SESSION["s_memberid"];
$cltdb = $_SESSION['s_cltdb'];

$moduledb = $_SESSION['s_admindb'];
mysql_select_db($moduledb) or die(mysql_error());

include 'jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "select ".$cltdb.".addresses.address_id,".$cltdb.".addresses.location,".$cltdb.".addresses.street_no,".$cltdb.".addresses.ad1,".$cltdb.".addresses.ad2,".$cltdb.".addresses.suburb,".$cltdb.".addresses.town,".$cltdb.".addresses.state,".$cltdb.".addresses.country,".$cltdb.".addresses.postcode,".$cltdb.".address_type.address_type from ".$cltdb.".addresses,".$cltdb.".address_type where ".$cltdb.".addresses.address_type_id = ".$cltdb.".address_type.address_type_id and ".$cltdb.".addresses.member_id = ".$id; 

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getaddress.php');



// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"address_type",
    "rowList"=>array(12,30,50),
	"height"=>115,
	"width"=>940
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("address_id", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("member_id", array("label"=>"Member", "width"=>30, "hidden"=>true));
$grid->setColProperty("address_type", array("label"=>"Type", "width"=>70));
$grid->setColProperty("location", array("label"=>"Location", "width"=>80));
$grid->setColProperty("street_no", array("label"=>"Street No", "width"=>130));
$grid->setColProperty("ad1", array("label"=>"Address", "width"=>120));
$grid->setColProperty("ad2", array("label"=>" ", "width"=>110));
$grid->setColProperty("suburb", array("label"=>"Suburb", "width"=>110));
$grid->setColProperty("town", array("label"=>"Town", "width"=>90));
$grid->setColProperty("state", array("label"=>"State", "width"=>60));
$grid->setColProperty("postcode", array("label"=>"Post Code", "width"=>70));
$grid->setColProperty("country", array("label"=>"Country", "width"=>100));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>100));


$ldevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#madlist2").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowdata = $("#madlist2").getRowData(cl);
		var stno = rowdata.street_no;
		var ad1 = rowdata.ad1;
		var ad2 = rowdata.ad2;
		var suburb = rowdata.suburb;
		var town = rowdata.town;
		var country = rowdata.country;
		var address = "'"+stno+','+ad1+','+ad2+','+suburb+','+town+','+country+"'";
		be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editad('+cl+')" >'; 
		se = '<img src="../images/delete.png" title="Delete" onclick="javascript:delad('+cl+')" >'; 
		mp = '<img src="../images/map.gif" title="Map" onclick="javascript:mapad('+address+')" >';
		jQuery("#madlist2").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se+'&nbsp;&nbsp;&nbsp;'+mp}); 
	}
}
LOADCOMPLETE;

$dcevent = <<<DBLCLICK
function(rowid) {
	var rowdata = $("#madlist2").getRowData(rowid);
	var memberid = rowdata.member_id;
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
$buttonoptions = array("#madpager2",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an address.", "onClickButton"=>"js: function(){addad(".$id.");}")
);
$grid->callGridMethod("#madlist2", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#madlist2','#madpager2',true, null, null,true,true,true);

?>




