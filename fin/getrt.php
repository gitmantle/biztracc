<?php
session_start();
//ini_set('display_errors', true);

$rtfile = $_SESSION['s_rtfile'];

switch($rtfile) {
	case "rt1":
		$fl = "z_1rec";
		break;
	case "rt2":
		$fl = "z_2rec";
		break;
	case "rt3":
		$fl = "z_3rec";
		break;
	case "rt4":
		$fl = "z_4rec";
		break;
	case "rt5":
		$fl = "z_5rec";
		break;
	case "rt6":
		$fl = "z_6rec";
		break;
}
$usersession = $_SESSION['usersession'];

$findb = $_SESSION['s_findb'];

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

// the actual query for the grid data
$grid->SelectCommand = "select uid,ddate,brdr,a2d,brcr,a2c,reference,amount,tax,descript1 from ".$findb.".".$fl;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getrt.php');



// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>15,
    "sortname"=>"uid",
    "rowList"=>array(15,30,50),
	"height"=>320,
	"width"=>950
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("brdr", array("label"=>"Branch", "width"=>45));
$grid->setColProperty("a2d", array("label"=>"Acc to DR", "width"=>150));
$grid->setColProperty("brcr", array("label"=>"Branch", "width"=>45));
$grid->setColProperty("a2c", array("label"=>"Acc to CR", "width"=>150));
$grid->setColProperty("reference", array("label"=>"Reference", "width"=>80));
$grid->setColProperty("amount", array("label"=>"Amount", "width"=>80, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("tax", array("label"=>"Tax", "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("descript1", array("label"=>"Description", "width"=>170));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$crcell = <<< CELLATTR
function (rowid, value, rawObject, colModel, arraydata) {
    return "style='background:#ff9933;color:#000000;' class='crclass' ";
}
CELLATTR;

$grid ->setColProperty('a2c', array("cellattr"=>"js:".$crcell));
$grid ->setColProperty('brcr', array("cellattr"=>"js:".$crcell));

$drcell = <<< CELLATTR
function (rowid, value, rawObject, colModel, arraydata) {
    return "style='background:#cccccc;color:#000000;' class='drclass' ";
}
CELLATTR;

$grid ->setColProperty('a2d', array("cellattr"=>"js:".$drcell));
$grid ->setColProperty('brdr', array("cellattr"=>"js:".$drcell));




// At end call footerData to put total  label
$grid->callGridMethod('#translist', 'footerData', array("set",array("reference"=>"Total:")));
// Set which parameter to be sumarized
$summaryrows = array("amount"=>array("amount"=>"SUM"),"tax"=>array("tax"=>"SUM"));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#rtlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#rtlist").getRowData(ids[i]);
			var cl = ids[i];
			var ac = rowd.accountno;
			var br = "'"+rowd.branch+"'";
			be = '<img src="../images/edit.png" title="Edit Transaction" onclick="javascript:rt_editline('+cl+')" ></ids>';
			se = '<img src="../images/delete.png" title="Delete Transaction" onclick="javascript:rt_delline('+cl+')" ></ids>';
			jQuery("#rtlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions = array("#rtpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add Transaction", "onClickButton"=>"js: function(){addrtTrans();}")
);
$grid->callGridMethod("#rtlist", "navButtonAdd", $buttonoptions); 

$buttonoptions2 = array("#rtpager",
    array("buttonicon"=>"ui-icon-arrowreturnthick-1-e","caption"=>"","position"=>"last","title"=>"Post Recurring Transactions", "onClickButton"=>"js: function(){rt_post();}")
);
$grid->callGridMethod("#rtlist", "navButtonAdd", $buttonoptions2); 

$buttonoptions3 = array("#rtpager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#rtlist", "navButtonAdd", $buttonoptions3);

// Run the script
$grid->renderGrid('#rtlist','#rtpager',true, $summaryrows, null,true,true,true);
$conn = null;


?>




