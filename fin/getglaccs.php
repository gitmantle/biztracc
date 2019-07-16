<?php
session_start();
//ini_set('display_errors', true);

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
// Get the needed parameters passed from the main grid


if(isset ($_REQUEST["grp"])) {
    $id = jqGridUtils::Strip($_REQUEST["grp"]);
} else {
    $id = '';
}
$_SESSION['s_accgroup'] = $id;

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select g.uid,g.branch,b.branchname,g.account,g.accountno,g.sub,g.blocked, g.system as sysacc from ".$findb.".glmast g inner join branch b on g.branch = b.branch where g.grp = '".$id."'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getglaccs.php');



// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"branch,accountno,sub",
    "rowList"=>array(12,50,100),
	"height"=>280,
	"width"=>620
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>35));
$grid->setColProperty("branchname", array("label"=>"Branch", "width"=>80));
$grid->setColProperty("account", array("label"=>"Account", "width"=>130));
$grid->setColProperty("accountno", array("label"=>"Ac.", "width"=>20));
$grid->setColProperty("sub", array("label"=>"Sub", "width"=>20));
$grid->setColProperty("blocked", array("label"=>"Blocked", "width"=>40));
$grid->setColProperty("sysacc", array("label"=>"System", "width"=>50, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>55));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#glacclist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#glacclist").getRowData(ids[i]);
			var cl = ids[i];
			var ac = rowd.accountno;
			var br = "'"+rowd.branch+"'";
			var sac = rowd.sub;
			var sysacc = rowd.sysacc;
			be = '<img src="../images/edit.png" title="Edit Account" onclick="javascript:editgl('+cl+')" ></ids>';
			if (sac == 0) {
				sb = '<img src="../images/add.gif" title="Add Sub Account" onclick="javascript:addsubgl('+ac+','+br+')" ></ids>';
			} else {
				sb = '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			se = '<img src="../images/delete.png" title="Delete Account" onclick="javascript:delgl('+cl+')" ></ids>';
			if (sysacc == 'Y') {
				sb = '&nbsp;&nbsp;&nbsp;&nbsp;';
				se = '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			jQuery("#glacclist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+sb+'&nbsp;&nbsp;&nbsp;'+se}); 
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
$buttonoptions = array("#glaccpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an account.", "onClickButton"=>"js: function(){addgl();}")
);
$grid->callGridMethod("#glacclist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#glacclist','#glaccpager',true, null, null,true,true,true);



?>




