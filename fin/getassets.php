<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];;

$asfile = 'ztmp'.$user_id.'_assets';

$findb = $_SESSION['s_findb'];

$db->closeDB();

$heading = $_SESSION['s_asheading'];

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
$grid->SelectCommand = "select uid,aname,bought,acost,adepn,abv,atot,rate from ".$findb.".".$asfile;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getassets.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>15,
    "sortname"=>"uid",
    "rowList"=>array(15,100,200),
	"height"=>300,
	"width"=>900
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

//$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("aname", array("label"=>"Asset", "width"=>100));
$grid->setColProperty("bought", array("label"=>"Bought", "width"=>40, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("acost", array("label"=>"Cost", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("adepn", array("label"=>"Depreciation", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("abv", array("label"=>"Book Value", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("atot", array("label"=>"Total", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("rate", array("label"=>"Depn. rate %", "width"=>40, "align"=>"right","formatter"=>"number"));
//$grid->setColProperty("act", array("label"=>"Actions", "width"=>20));

// At end call footerData to put total  label
$grid->callGridMethod('#aslist', 'footerData', array("set",array("aname"=>"Balance:")));
// Set which parameter to be sumarized
$summaryrows = array("atot"=>array("atot"=>"SUM"));

/*
$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#bslist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#bslist").getRowData(ids[i]);
			var cl = ids[i];
			var type = rowd.Type;
			var ac = rowd.AccountNumber;
			var br = "'"+rowd.Branch+"'";
			var sb = rowd.Sub;
			if (type != 'H') {
				be = '<img src="../images/into.png" title="View Transactions" onclick="javascript:viewac('+ac+','+br+','+sb+')" ></ids>';
			} else {
				be = ' ';
			}
			jQuery("#bslist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

*/

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#aspager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Output to Excel.", "onClickButton"=>"js: function(){fa2xl();}")
);
$grid->callGridMethod("#aslist", "navButtonAdd", $buttonoptions); 

$buttonoptions1 = array("#aspager",
    array("buttonicon"=>"ui-icon-print","caption"=>"","position"=>"last","title"=>"Output to PDF.", "onClickButton"=>"js: function(){fa2pdf();}")
);
$grid->callGridMethod("#aslist", "navButtonAdd", $buttonoptions1); 


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#aslist','#aspager',true,$summaryrows, null, true,true,true);
$conn = null;

?>




