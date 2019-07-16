<?php
session_start();

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
$grid->SelectCommand = "select uid,'".$ad."'as admin,".$sid." as sid,title,document from gendocs";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getgendocs.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"General Information Documents",
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
$grid->setColProperty("sid", array("label"=>"sid", "width"=>20, "hidden"=>true));
$grid->setColProperty("title", array("label"=>"Title", "width"=>200));
$grid->setColProperty("document", array("label"=>"doc", "width"=>200, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>60));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#gendoclist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#gendoclist").getRowData(ids[i]);
			var cl = ids[i];
			var ad = rowd.admin;
			var sid = rowd.sid;
			var doc = "'"+rowd.document+"'";
			if (ad == 'Y') {
				ae = '<img src="../images/delete.png" title="Delete document" onclick="javascript:delgendoc('+cl+')" ></ids>';
			} else {
				ae = '&nbsp;&nbsp;&nbsp;';
			}
			be = '<img src="../images/edit.png" title="View Signed Documents" onclick="javascript:viewgendoc('+doc+')" ></ids>';
			jQuery("#gendoclist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+ae}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions = array("#gendocpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a document", "onClickButton"=>"js: function(){addgendoc(".$sid.");}")
);
$grid->callGridMethod("#gendoclist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#gendoclist','#gendocpager',true, $summaryrows, null,true,true,true);
$conn = null;

?>




