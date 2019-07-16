<?php
session_start();
//ini_set('display_errors', true);
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];

include 'jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

if(isset ($_REQUEST["son"])) {
    $id = jqGridUtils::Strip($_REQUEST["son"]);
	$r_no = substr($id,3);
	$rno = 'S_O'.$r_no;
	$dno = 'D_N'.$r_no;
} else {
    $id = '';
	$r_no = '';
	$rno = '';
	$dno = 'X';
}


$_SESSION['s_salesorder'] = $rno;

$db->closeDB();

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select ref_no,ddate,totvalue from ".$findb.".invhead where ref_no like '".$dno."%'"; 

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getdns.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Delivery Notes",
    "rowNum"=>12,
    "sortname"=>"ref_no",
    "rowList"=>array(12,30,50),
	"height"=>270,
	"width"=>400
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>80));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("totvalue", array("label"=>"Value", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$grid->setSubGrid("getdntrans.php",
        array('item', 'quantity', 'unit', 'value'),
        array(180,80,80,80),
        array('left','right','left','right'));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#dnlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowdata = $("#dnlist").getRowData(cl);
		var r = rowdata.ref_no;
		var rf = "'"+r+"'";
		pe = '<img src="../images/printer.gif" title="View and Print Delivery Note" onclick="javascript:printdn('+rf+')" >'; 
		
		jQuery("#dnlist").setRowData(ids[i],{act:pe}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

/*
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#dnpager",
    array("buttonicon"=>"ui-icon-script","caption"=>"","position"=>"first","title"=>"Create an Invoice.", "onClickButton"=>"js: function(){createinv();}")
);
$grid->callGridMethod("#dnlist", "navButtonAdd", $buttonoptions); 
*/

// Run the script
$grid->renderGrid('#dnlist','#dnpager',true, null, null,true,true,true);

?>




