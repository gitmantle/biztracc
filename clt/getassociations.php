<?php
session_start();
//error_reporting(0);
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$tcommsfile = "ztmp".$user_id."_assocfile";

$cltdb = $_SESSION['s_cltdb'];

$db->query("drop table if exists ".$cltdb.".".$tcommsfile);
$db->execute();
$db->query("create table ".$cltdb.".".$tcommsfile." (associd int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,mid int(11) default 0,assoc varchar(30) default '',ofid int(11) default 0,ofname varchar(50) default '')  engine myisam");
$db->execute();

$id = $_SESSION["s_memberid"];

$db->query("select assoc_xref.assoc_xref_id,assoc_xref.member_id as memid,assoc_xref.association,assoc_xref.of_id from ".$cltdb.".assoc_xref where assoc_xref.member_id = ".$id); 
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query("insert into ".$cltdb.".".$tcommsfile." (mid,assoc,ofid) values (:mid,:assoc,:ofid)");
	$db->bind(':mid', $memid);
	$db->bind(':assoc', $association);
	$db->bind(':ofid', $of_id);
	
	$db->execute();
}


$db->query("select associd,ofid,assoc from ".$cltdb.".".$tcommsfile); 
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$aid = $associd;
	$db->query('select CONCAT_WS(" ",firstname,lastname) as fname, dob from '.$cltdb.'.members where member_id = '.$ofid);
	$row = $db->single();
	extract($row);
	if ($dob == "0000-00-00") {
		$age = 0;
	} else {
    	$age = floor((time() - strtotime($dob))/31556926);
	}
	if ($assoc == 'Parent') {
		$db->query('update '.$cltdb.'.'.$tcommsfile.' set ofname = "'.$fname.' ('.$age.' yrs)" where associd = '.$aid);
	} else {
		$db->query('update '.$cltdb.'.'.$tcommsfile.' set ofname = "'.$fname.'" where associd = '.$aid);
	}
	$db->execute();
}

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
$grid->SelectCommand = "select associd,mid,assoc,ofid,ofname from ".$cltdb.".".$tcommsfile; 

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getassociations.php');

// Set some grid options
$grid->setGridOptions(array(
	"caption"=>"Associations",
	"rowNum"=>12,
    "sortname"=>"assoc",
	"sortorder"=>"asc",
    "rowList"=>array(12,30,50),
	"height"=>95,
	"width"=>500
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("associd", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("mid", array("label"=>"Member ID", "width"=>30, "hidden"=>true));
$grid->setColProperty("assoc", array("label"=>"Association", "width"=>90));
$grid->setColProperty("ofid", array("label"=>"Of ID", "width"=>30, "hidden"=>true));
$grid->setColProperty("ofname", array("label"=>"Of", "width"=>200));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>50,"sortable"=>false));


$ldevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#massociationslist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowdata = $("#massociationslist").getRowData(cl);
		var memid = rowdata.mid;
		var asid = rowdata.ofid;
		de = '<img src="../images/delete.png" title="Delete Association" onclick="javascript:delassoc('+asid+','+memid+')" >'; 
		jQuery("#massociationslist").setRowData(ids[i],{act:de});
	} 
}
LOADCOMPLETE;


$grid->setGridEvent("loadComplete",$ldevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoption1 = array("#massociationspager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an associaton", "onClickButton"=>"js: function(){addassoc(".$id.",'m');}"),
);
$grid->callGridMethod("#massociationslist", "navButtonAdd", $buttonoption1); 

// Run the script
$grid->renderGrid('#massociationslist','#massociationspager',true, null, null,true,true,true);



?>




