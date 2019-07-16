<?php
session_start();
//ini_set('display_errors', true);
require("../db.php");

$moduledb = $_SESSION['s_admindb'];
mysql_select_db($moduledb) or die(mysql_error());

include 'jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Get the needed parameters passed from the main grid

//construct where clause 
if(isset ($_REQUEST["did"])) {
    $id = jqGridUtils::Strip($_REQUEST["did"]);
} else {
    $id = '';
}
$_SESSION['s_distlist'] = $id;

$dep = $_SESSION['s_udepot'];
if ($dep == 0) {
	$where = " 1 = 1 "; 
} else {
	$where = " depot_id = ".$dep; 
}


// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid, member_id, depot, member, balance, ordered,  checked, ok from distdetail where ".$where." and distlist_id = '".$id."'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getDistDetails.php');



// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Members in List",
    "rowNum"=>18,
    "sortname"=>"depot, member",
    "rowList"=>array(18,50,100),
	"height"=>420,
	"width"=>620
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("member_id", array("label"=>"mid", "width"=>25, "hidden"=>true));
$grid->setColProperty("depot", array("label"=>"Depot", "width"=>100));
$grid->setColProperty("member", array("label"=>"Member", "width"=>125));
$grid->setColProperty("balance", array("label"=>"Balance", "width"=>80, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("ordered", array("label"=>"Ordered", "width"=>80, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("checked", array("label"=>"Checked", "width"=>50));
$grid->setColProperty("ok", array("label"=>"OK", "width"=>50));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$grid->setSubGrid("getDistMeds.php",
        array('Medicine', 'Dose', 'per', 'Qty', 'Unit','of','Price'),
        array(200,40,40,40,50,40,80),
        array('left','right','center','right','center','right','right'));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#distdetlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var cl = ids[i];
			var rowd = $("#distdetlist").getRowData(ids[i]);
			var mid = rowd.member_id+",'m'";
			be = '<img src="../images/edit.png" title="Edit Member" onclick="javascript:editmem('+mid+')" ></ids>';
			jQuery("#distdetlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function

$buttonoptions = array("#distdetpager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Export to Excel.", "onClickButton"=>"js: function(){dxl();}")
);
$grid->callGridMethod("#distdetlist", "navButtonAdd", $buttonoptions); 

$buttonoptions1 = array("#distdetpager",
    array("buttonicon"=>"ui-icon-mail-closed","caption"=>"","position"=>"last","title"=>"Insufficient funds list for contact", "onClickButton"=>"js: function(){nofunds();}")
);
$grid->callGridMethod("#distdetlist", "navButtonAdd", $buttonoptions1); 

$buttonoptions2 = array("#distdetpager",
    array("buttonicon"=>"ui-icon-arrowrefresh-1-n","caption"=>"","position"=>"last","title"=>"Update Details.", "onClickButton"=>"js: function(){updatedist();}")
);
$grid->callGridMethod("#distdetlist", "navButtonAdd", $buttonoptions2); 

$buttonoptions3 = array("#distdetpager",
    array("buttonicon"=>"ui-icon-transfer-e-w","caption"=>"","position"=>"last","title"=>"Process Distribution List.", "onClickButton"=>"js: function(){processdist();}")
);
$grid->callGridMethod("#distdetlist", "navButtonAdd", $buttonoptions3); 

// Run the script
$grid->renderGrid('#distdetlist','#distdetpager',true, null, null,true,true,true);



?>




