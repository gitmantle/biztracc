<?php
session_start();

$stf = $_SESSION['s_staffdoc'];
$staffid = $_SESSION['s_staffid'];

$admindb = $_SESSION['s_admindb'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$ad = $admin;
$sid = $subid;


$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

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
$grid->SelectCommand = "select uid,'".$ad."'as admin,".$sid." as sid,title,document from documents where staffid = ".$staffid;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('get1doc.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Signed Documents for ".$stf,
    "rowNum"=>20,
    "sortname"=>"title",
    "rowList"=>array(20,100,200),
	"width"=>760,
	"height"=>350
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("admin", array("label"=>"Admin", "width"=>20, "hidden"=>true));
$grid->setColProperty("sid", array("label"=>"Sid", "width"=>25, "hidden"=>true));
$grid->setColProperty("title", array("label"=>"Title", "width"=>200));
$grid->setColProperty("document", array("label"=>"document", "width"=>200, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>60));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#doclist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#doclist").getRowData(ids[i]);
			var cl = ids[i];
			var ad = rowd.admin;
			var sid = rowd.sid;
			var doc = "'"+rowd.document+"'";
			if (ad == 'Y') {
				ae = '<img src="../images/delete.png" title="Delete document" onclick="javascript:deldocument('+cl+')" ></ids>';
			} else {
				ae = '&nbsp;&nbsp;&nbsp;';
			}
			be = '<img src="../images/edit.png" title="View Signed Documents" onclick="javascript:viewdocument('+cl+','+sid+','+doc+')" ></ids>';
			jQuery("#doclist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+ae}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions = array("#docpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a signed document", "onClickButton"=>"js: function(){adddocument(".$staffid.");}")
);
$grid->callGridMethod("#doclist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#doclist','#docpager',true, $summaryrows, null,true,true,true);
$conn = null;

?>




