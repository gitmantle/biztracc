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

$tbfile = 'ztmp'.$user_id.'_tb';

$findb = $_SESSION['s_findb'];

$heading = $_SESSION['s_coyname'].' - '.$_SESSION['s_tbheading'];

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
$grid->SelectCommand = "select uid,AccountNumber,Branch,Branchname,Sub,AccountName,Debit,Credit,Lastyear from ".$findb.".".$tbfile;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('gettb.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>20,
    "sortname"=>"AccountNumber,Branch,Sub",
    "rowList"=>array(20,100,200),
	"height"=>465,
	"width"=>890
    ));

$grid->addCol(array("name"=>"act"),"last");

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"UID", "width"=>25, "hidden"=>true));
$grid->setColProperty("AccountNumber", array("label"=>"Account", "width"=>25));
$grid->setColProperty("Branch", array("label"=>"Branch", "width"=>25));
$grid->setColProperty("Branchname", array("label"=>"Branchname", "width"=>70));
$grid->setColProperty("Sub", array("label"=>"Sub", "width"=>25));
$grid->setColProperty("AccountName", array("label"=>"Account Name", "width"=>125));
$grid->setColProperty("Debit", array("label"=>"Debit", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("Credit", array("label"=>"Credit", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("Lastyear", array("label"=>"Last Year", "width"=>40, "align"=>"right"));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>20));



// At end call footerData to put total  label
$grid->callGridMethod('#tblist', 'footerData', array("set",array("AccountName"=>"Balances:")));
// Set which parameter to be sumarized
$summaryrows = array("Debit"=>array("Debit"=>"SUM"),"Credit"=>array("Credit"=>"SUM"),"Lastyear"=>array("Lastyear"=>"SUM"));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#tblist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#tblist").getRowData(ids[i]);
			var cl = ids[i];
			var ac = rowd.AccountNumber;
			var br = "'"+rowd.Branch+"'";
			var sb = rowd.Sub;
			var brn = rowd.Branchname;
			if (brn == '') {
				be = '&nbsp;';
			} else {
				be = '<img src="../images/into.png" title="View Transactions" onclick="javascript:viewac('+ac+','+br+','+sb+')" ></ids>';
			}
			jQuery("#tblist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);


// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#tbpager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Output to Excel.", "onClickButton"=>"js: function(){tb2xl();}")
);
$grid->callGridMethod("#tblist", "navButtonAdd", $buttonoptions); 

$buttonoptions1 = array("#tbpager",
    array("buttonicon"=>"ui-icon-print","caption"=>"","position"=>"last","title"=>"Output to PDF.", "onClickButton"=>"js: function(){tb2pdf();}")
);
$grid->callGridMethod("#tblist", "navButtonAdd", $buttonoptions1); 

$buttonoptions2 = array("#tbpager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#tblist", "navButtonAdd", $buttonoptions2); 



// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#tblist','#tbpager',true, $summaryrows, null,true,true,true);
$conn = null;

?>




