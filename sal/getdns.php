<?php
session_start();
ini_set('display_errors', true);
require("../db.php");
$coyid = $_SESSION['s_coyid'];
$coyname = $_SESSION['s_coyname'];
$cn = $coyid.'~'.$coyname;

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

include '../clt/jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

if(isset ($_REQUEST["son"])) {
    $id = jqGridUtils::Strip($_REQUEST["son"]);
} else {
    $id = 0;
}

$q = "select invno from quotes where uid = ".$id;
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$rno = 'S_O'.$invno;
$dno = 'D_N'.$invno;

$_SESSION['s_salesorder'] = $rno;

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "select uid,ref_no,ddate,totvalue,invref from quotes where ref_no like '".$dno."%'"; 

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
	"width"=>540
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>80));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("totvalue", array("label"=>"Value", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("invref", array("label"=>"Invoiced", "width"=>80));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#dnlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowdata = $("#dnlist").getRowData(cl);
		var r = rowdata.ref_no;
		var rf = "'"+r+"'";
		be = '<img src="../images/into.png" title="View Delivery Note" onclick="javascript:viewdn('+rf+')" >'; 
		pe = '<img src="../images/printer.gif" title="Print Delivery Note" onclick="javascript:printdn('+rf+')" >'; 
		
		jQuery("#dnlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+pe}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#dnpager",
    array("buttonicon"=>"ui-icon-script","caption"=>"","position"=>"first","title"=>"Create an Invoice.", "onClickButton"=>"js: function(){createinv();}")
);
$grid->callGridMethod("#dnlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#dnlist','#dnpager',true, null, null,true,true,true);

?>




