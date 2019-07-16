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
$subscriber = $subid;
$sname = $row['uname'];;

$table = 'ztmp'.$user_id.'_bin';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$table);
$db->execute();

$db->query("create table ".$findb.".".$table." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, binid int(11) default 0, locid int(11) default 0, itemcode varchar(30) default '', location varchar(40) default '', onhand double(17,3) default 0, row varchar(20) default '', shelf varchar(20) default '', bin varchar(20) default '') engine myisam"); 
$db->execute();		

include '../fin/jq-config.php';

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

// Get the needed parameters passed from the main grid
if(isset ($_REQUEST["itid"])) {
    $id = jqGridUtils::Strip($_REQUEST["itid"]);
} else {
    $id = '';
}

$db->query("SELECT t.locid, sum(t.increase-t.decrease) as onhand, l.location from ".$findb.".stktrans t inner join ".$findb.".stklocs l on t.locid = l.uid where t.itemcode = '".$id."' group by t.locid");
$rows = $db->resultset();
if (count($rows) > 0) {
	foreach ($rows as $row) {
		extract($row);
		$db->query("insert into ".$findb.".".$table." (locid,itemcode,location,onhand) values (:locid,:itemcode,:location,:onhand)");
		$db->bind(':locid', $locid);
		$db->bind(':itemcode', $id);
		$db->bind(':location', $location);
		$db->bind(':onhand', $onhand);
		$db->execute();
	}
}
		   
$db->query("select uid,locid from ".$findb.".".$table);
$rows = $db->resultset();
if (count($rows) > 0) {
	foreach ($rows as $row) {
		extract($row);
		$tid = $uid;
		$lid = $locid;
		$db->query("select uid as binid, row, shelf, bin from ".$findb.".stkbins where itemcode = '".$id."' and locid = ".$lid);
		$brow = $db->single();
		if (!empty($brow)) {
			extract($brow);
			$db->query("update ".$findb.".".$table." set binid = :binid, row = :row, shelf = :shelf, bin = :bin where uid = :tid");
			$db->bind(':binid', $binid);
			$db->bind(':row', $row);
			$db->bind(':shelf', $shelf);
			$db->bind(':bin', $bin);
			$db->bind(':tid', $tid);
			$db->execute();
		}
	}
}

$db->closeDB();

// the actual query for the grid data
$grid->SelectCommand = "SELECT uid,binid,itemcode,location,onhand,row,shelf,bin from ".$findb.".".$table;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('../fin/getStockbyLoc.php');

// Set some grid options
$grid->setGridOptions(array(
	"caption"=>"Quantities per Location",						
    "rowNum"=>5,
    "sortname"=>"location",
    "rowList"=>array(5,50,100),
	"height"=>112,
	"width"=>880
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("binid", array("label"=>"BINID", "width"=>25, "hidden"=>true));
$grid->setColProperty("itemcode", array("label"=>"Itemcode", "width"=>40, "hidden"=>true));
$grid->setColProperty("location", array("label"=>"Location", "width"=>100));
$grid->setColProperty("onhand", array("label"=>"Quantity on hand", "width"=>80, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("row", array("label"=>"Row", "width"=>80));
$grid->setColProperty("shelf", array("label"=>"Shelf", "width"=>80));
$grid->setColProperty("bin", array("label"=>"Bin", "width"=>80));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

// At end call footerData to put total  label
// Set which parameter to be sumarized
$summaryrows = array("onhand"=>array("onhand"=>"SUM"));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#stklocqtylist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#stklocqtylist").getRowData(ids[i]);
			var cl = ids[i];
			var bid = rowd.binid;
			var item = rowd.itemcode;
			var st = "'"+cl+'~'+bid+'~'+item+"'";
			be = '<img src="../images/edit.png" title="Edit storage row/shelf/bin" onclick="javascript:editbin('+st+')" ></ids>';
			jQuery("#stklocqtylist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#stklocqtylist','#stklocqtypager',true, $summaryrows, null,true,true,true);


?>




