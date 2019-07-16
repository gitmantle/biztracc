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

$mbfile = 'ztmp'.$user_id.'_mthbal';

$findb = $_SESSION['s_findb'];

$heading = $_SESSION['s_coyname'].' - '.$_SESSION['s_finheading'];
$todate = $_SESSION['s_todate'];

//  month headings
for ($i = 0; $i <= 11; $i++) {
    $months[] = date("Y-m", strtotime( date( $todate.'-01' )." -$i months"));
}
$mth1 = date('M', strtotime($months[11]."-01"));
$mth2 = date('M', strtotime($months[10]."-01"));
$mth3 = date('M', strtotime($months[9]."-01"));
$mth4 = date('M', strtotime($months[8]."-01"));
$mth5 = date('M', strtotime($months[7]."-01"));
$mth6 = date('M', strtotime($months[6]."-01"));
$mth7 = date('M', strtotime($months[5]."-01"));
$mth8 = date('M', strtotime($months[4]."-01"));
$mth9 = date('M', strtotime($months[3]."-01"));
$mth10 = date('M', strtotime($months[2]."-01"));
$mth11 = date('M', strtotime($months[1]."-01"));
$mth12 = date('M', strtotime($months[0]."-01"));

$db->closeDB();

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
$grid->SelectCommand = "select uid,AccountNumber,Branch,Branchname,Sub,AccountName,m1,m2,m3,m4,m5,m6,m7,m8,m9,m10,m11,m12 from ".$findb.".".$mbfile;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getmthbals.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>100,
    "sortname"=>"AccountNumber,Branch,Sub",
    "rowList"=>array(100,200),
	"height"=>460,
	"width"=>1320
    ));


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"UID", "width"=>25, "hidden"=>true));
$grid->setColProperty("AccountNumber", array("label"=>"Account", "width"=>25));
$grid->setColProperty("Branch", array("label"=>"Branch", "width"=>25));
$grid->setColProperty("Branchname", array("label"=>"Branchname", "width"=>70));
$grid->setColProperty("Sub", array("label"=>"Sub", "width"=>25));
$grid->setColProperty("AccountName", array("label"=>"Account Name", "width"=>125));
$grid->setColProperty("m1", array("label"=>"Month 1", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("m2", array("label"=>"Month 2", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("m3", array("label"=>"Month 3", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("m4", array("label"=>"Month 4", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("m5", array("label"=>"Month 5", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("m6", array("label"=>"Month 6", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("m7", array("label"=>"Month 7", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("m8", array("label"=>"Month 8", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("m9", array("label"=>"Month 9", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("m10", array("label"=>"Month 10", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("m11", array("label"=>"Month 11", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("m12", array("label"=>"Month 12", "width"=>40, "align"=>"right","formatter"=>"number"));


// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#mbpager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Output to Excel.", "onClickButton"=>"js: function(){mb2xl();}")
);
$grid->callGridMethod("#mblist", "navButtonAdd", $buttonoptions); 

$buttonoptions2 = array("#mbpager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#mblist", "navButtonAdd", $buttonoptions2); 


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#mblist','#mbpager',true, null, null,true,true,true);
$conn = null;

?>




