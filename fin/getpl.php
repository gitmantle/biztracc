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

$plfile = 'ztmp'.$user_id.'_pl';

$findb = $_SESSION['s_findb'];

$db->query("delete FROM ".$findb.".".$plfile." WHERE Sbal = 0 and Bal = 0 and Total = 0");
$db->execute();

$db->closeDB();

$heading = $_SESSION['s_plheading'];

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
$grid->SelectCommand = "select uid,Type,Header,AccountNumber,Branch,Sub,AccountName,Sbal,Bal,Total from ".$findb.".".$plfile;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getpl.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>20,
    "sortname"=>"uid",
    "rowList"=>array(20,100,200),
	"height"=>450,
	"width"=>890
    ));


$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("Type", array("label"=>"Type", "width"=>20, "hidden"=>true));
$grid->setColProperty("Header", array("label"=>"Section", "width"=>35));
$grid->setColProperty("AccountNumber", array("label"=>"Account", "width"=>25));
$grid->setColProperty("Branch", array("label"=>"Branch", "width"=>25));
$grid->setColProperty("Sub", array("label"=>"Sub", "width"=>25));
$grid->setColProperty("AccountName", array("label"=>"Account Name", "width"=>125));
$grid->setColProperty("Sbal", array("label"=>"sub bal.", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("Bal", array("label"=>"Balance", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("Total", array("label"=>"Total", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>30));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#pllist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#pllist").getRowData(ids[i]);
			var cl = ids[i];
			var type = rowd.Type;
			var ac = rowd.AccountNumber;
			var br = "'"+rowd.Branch+"'";
			var sb = rowd.Sub;
			if (type == 'D') {
				be = '<img src="../images/into.png" title="View Transactions" onclick="javascript:viewac('+ac+','+br+','+sb+')" ></ids>';
			} else {
				be = ' ';
			}
			jQuery("#pllist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#plpager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Output to Excel.", "onClickButton"=>"js: function(){pl2xl();}")
);
$grid->callGridMethod("#pllist", "navButtonAdd", $buttonoptions); 

$buttonoptions1 = array("#plpager",
    array("buttonicon"=>"ui-icon-print","caption"=>"","position"=>"last","title"=>"Output to PDF.", "onClickButton"=>"js: function(){pl2pdf();}")
);
$grid->callGridMethod("#pllist", "navButtonAdd", $buttonoptions1); 

$buttonoptions2 = array("#plpager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#pllist", "navButtonAdd", $buttonoptions2); 



// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#pllist','#plpager',true, null, null,true,true,true);
$conn = null;

?>




