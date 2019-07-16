<?php
require_once '../includes/jquery/jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Get the needed parameters passed from the main grid
if(isset ($_REQUEST["sbid"])) {
	//session_start();
    $id = jqGridUtils::Strip($_REQUEST["sbid"]);
	$_SESSION['s_subid'] = $id;
} else {
    $id = 0;
}
// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = "SELECT coyid,coyname,coyemail,coyphone from companies WHERE coysubid = ".$id;
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel(null, array(&$id));
// Set the url from where we obtain the data
$grid->setUrl('getCompanies.php');
// Set some grid options
$grid->setGridOptions(array(
	"caption"=>'Companies',
    "rowNum"=>7,
    "rowList"=>array(7,30,50),
    "sortname"=>"coyname",
	"height"=>155,
	"width"=>700
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("coyid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("coyname", array("label"=>"Company", "width"=>100));
$grid->setColProperty("coyemail", array("label"=>"Email", "width"=>140));
$grid->setColProperty("coyphone", array("label"=>"Phone", "width"=>60));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>40));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#companylist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#companylist").getRowData(ids[i]);
			var cl = ids[i];
			var cid = rowd.coyid;
			be = '<img src="../images/edit.png" title="Edit Company" onclick="javascript:editcompany('+cl+')" ></ids>';
			se = '<img src="../images/people.png" title="Edit Users" onclick="javascript:editusers('+cl+')" ></ids>';
			te = '<img src="../images/ipad16.png" title="Update Tablets" onclick="javascript:updttablets('+cid+')" ></ids>';
			jQuery("#companylist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se+'&nbsp;&nbsp;&nbsp;'+te}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#companypager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Company", "onClickButton"=>"js: function(){addcompany();}")
);
$grid->callGridMethod("#companylist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#companylist','#companypager',true, null, null, true,true);
?>
