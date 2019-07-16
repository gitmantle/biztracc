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

$bsfile = 'ztmp'.$user_id.'_bs';

$findb = $_SESSION['s_findb'];

$db->query("delete FROM ".$findb.".".$bsfile." WHERE col1 = 0 and col2 = 0 and total = 0 and type = 'D'");
$db->execute();

$heading = $_SESSION['s_bsheading'];

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
$grid->SelectCommand = "select uid,Type,Header,AccountNumber,Branch,Sub,AccountName,Col1,Col2,Total from ".$findb.".".$bsfile;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getbs.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>15,
    "sortname"=>"uid",
    "rowList"=>array(15,100,200),
	"height"=>450,
	"width"=>900
    ));


$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("Type", array("label"=>"Type", "width"=>20, "hidden"=>true));
$grid->setColProperty("Header", array("label"=>"Section", "width"=>45));
$grid->setColProperty("AccountNumber", array("label"=>"Account", "width"=>25));
$grid->setColProperty("Branch", array("label"=>"Branch", "width"=>25));
$grid->setColProperty("Sub", array("label"=>"Sub", "width"=>25));
$grid->setColProperty("AccountName", array("label"=>"Account Name", "width"=>125));
$grid->setColProperty("Col1", array("label"=>"sub bal.", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("Col2", array("label"=>"Balance", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("Total", array("label"=>"Total", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>20));


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
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#bspager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Output to Excel.", "onClickButton"=>"js: function(){bs2xl();}")
);
$grid->callGridMethod("#bslist", "navButtonAdd", $buttonoptions); 

$buttonoptions1 = array("#bspager",
    array("buttonicon"=>"ui-icon-print","caption"=>"","position"=>"last","title"=>"Output to PDF.", "onClickButton"=>"js: function(){bs2pdf();}")
);
$grid->callGridMethod("#bslist", "navButtonAdd", $buttonoptions1); 

$buttonoptions2 = array("#bspager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#bslist", "navButtonAdd", $buttonoptions2); 



// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#bslist','#bspager',true, null, null,true,true,true);
$conn = null;

?>




