<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_trans';

$db->closeDB();


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
$grid->SelectCommand = "select uid,ddate,brdr,a2d,brcr,a2c,reference,amount,tax,descript1 from ".$findb.".".$table;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('gettrans.php');



// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>5,
    "sortname"=>"uid",
    "rowList"=>array(5,30,50),
	"height"=>120,
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
$grid->setColProperty("a2c", array("label"=>"Acc to CR", "width"=>150, ));
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
	var ids = jQuery("#translist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#translist").getRowData(ids[i]);
			var cl = ids[i];
			var ac = rowd.accountno;
			var br = "'"+rowd.branch+"'";
			be = '<img src="../images/edit.png" title="Edit Transaction" onclick="javascript:editline('+cl+')" ></ids>';
			se = '<img src="../images/delete.png" title="Delete Transaction" onclick="javascript:delline('+cl+')" ></ids>';
			jQuery("#translist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions2 = array("#transpager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#translist", "navButtonAdd", $buttonoptions2); 


// Run the script
$grid->renderGrid('#translist','#transpager',true, $summaryrows, null,true,true,true);
$conn = null;


?>




