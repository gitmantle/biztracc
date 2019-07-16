<?php
error_reporting (E_ALL ^ E_WARNING);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$coyidno = $_SESSION['s_coyid'];

$cltdb = $_SESSION['s_cltdb'];

$where = "where client_company_xref.company_id = ".$coyidno." and client_company_xref.crno != 0"; 

include '../clt/jq-config.php';
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

$grid->SelectCommand = "select members.member_id,client_company_xref.uid,members.lastname,members.firstname,client_company_xref.crno,client_company_xref.crsub,client_company_xref.subname,client_company_xref.blocked,client_company_xref.sortcode,client_company_xref.client_id from ".$cltdb.".members left join ".$cltdb.".client_company_xref on members.member_id = client_company_xref.client_id ".$where;


// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getcr.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Creditors Ledger Accounts",
    "rowNum"=>15,
    "sortname"=>"sortcode",
    "rowList"=>array(15,30,50),
	"height"=>300,
	"width"=>550
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("member_id", array("label"=>"MID", "width"=>20, "hidden"=>true));
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("lastname", array("label"=>"Company/Last Name", "width"=>200));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>160));
$grid->setColProperty("crno", array("label"=>"Account", "width"=>60, "hidden"=>true));
$grid->setColProperty("crsub", array("label"=>"Sub", "width"=>60, "hidden"=>true));
$grid->setColProperty("subname", array("label"=>"Sub Acc.", "width"=>60));
$grid->setColProperty("blocked", array("label"=>"Blocked.", "width"=>50));
$grid->setColProperty("sortcode", array("label"=>"Sort code", "width"=>50, "hidden"=>true));
$grid->setColProperty("client_id", array("label"=>"Client", "width"=>50, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$grid->setSubGrid("getClientAd.php",
        array('Type', 'Addr_1', 'Addr_2', 'Suburb', 'Town'),
        array(50,120,110,110,90),
        array('left','left','left','left','left'));


$ldevent = <<<LOADCOMPLETE
function(rowid){
			var ids = jQuery("#crlist").getDataIDs(); 
			for(var i=0;i<ids.length;i++){ 
				var cl = ids[i]; 
				var ret = $("#crlist").getRowData(cl);
				var sno = ret.crsub;
				var mid = ret.client_id;
				var ud = ret.uid;
				if (sno == 0) {
					be = '<img src="../images/edit.png" title="Edit Creditor" onclick="javascript:editcr('+mid+','+sno+')" ></ids>'; 
					se = '<img src="../images/delete.png" title="Delete Creditor" onclick="javascript:delcr('+ud+')" />'; 
				} else {
					be = '<img src="../images/edit.png" title="Edit Sub Account" onclick="javascript:editcr('+mid+','+sno+')" ></ids>'; 
					se = '<img src="../images/delete.png" title="Delete Sub Account" onclick="javascript:delcr('+ud+')" />'; 
				}
				if (sno == 0) {
					sb = '<img src="../images/add.gif" title="Add Sub Account" onclick="javascript:adsubcr('+ud+')" />'; 
				} else {
					sb = '';	
				}
				jQuery("#crlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se+'&nbsp;&nbsp;&nbsp;'+sb}) 
			} 
}
LOADCOMPLETE;
$grid->setGridEvent("loadComplete",$ldevent);



$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#crlist','#crpager2',true, null, null, true,true);

?>

