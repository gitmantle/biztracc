<?php
session_start();
ini_set('display_errors', true);
require("../db.php");
$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$sid = $_SESSION['s_cltdb'];
$cid = $_SESSION['s_admindb'];
$id = $_SESSION["s_memberid"];

include 'jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

// enable debugging
//$grid->debug = true;


// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select ".$sid.".client_company_xref.uid, ".$sid.".client_company_xref.company_id,".$cid.".companies.coyname,if(".$sid.".client_company_xref.drno > 0,'Debtor','Creditor') as relationship,".$sid.".client_company_xref.sendstatement,".$sid.".client_company_xref.email,".$sid.".client_company_xref.billing,".$sid.".client_company_xref.priceband, (".$sid.".client_company_xref.current+".$sid.".client_company_xref.d30+".$sid.".client_company_xref.d60+".$sid.".client_company_xref.d90+".$sid.".client_company_xref.d120) as balance,".$sid.".client_company_xref.blocked from ".$sid.".client_company_xref,".$cid.".companies where (".$sid.".client_company_xref.sendstatement <> '') and (".$sid.".client_company_xref.client_id = ".$id.") and (".$cid.".companies.coysubid = ".$subscriber.")";  

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getfinancials.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "rowNum"=>5,
    "sortname"=>"coyname",
    "rowList"=>array(5,30,50),
	"height"=>115,
	"width"=>900
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("company_id", array("label"=>"CID", "width"=>20, "hidden"=>true));
$grid->setColProperty("coyname", array("label"=>"Company", "width"=>170));
$grid->setColProperty("relationship", array("label"=>"Relationship", "width"=>80));
$grid->setColProperty("sendstatement", array("label"=>"Statement By", "width"=>110));
$grid->setColProperty("email", array("label"=>"Email", "width"=>60 ));
$grid->setColProperty("billing", array("label"=>"Billing", "width"=>60));
$grid->setColProperty("priceband", array("label"=>"Priceband", "width"=>70));
$grid->setColProperty("balance", array("label"=>"Balance", "width"=>90, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("blocked", array("label"=>"Blocked", "width"=>60));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>80, "sortable"=>false));

$ld3event = <<<LD3COMPLETE
function(rowid){
	var ids = jQuery("#mfinancialslist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowd = $("#mfinancialslist").getRowData(ids[i]);
		var cname = rowd.coyname;
		var cn = "'"+cname.replace(/ /g,'_')+"'";
		be = '<img src="../images/edit.png" title="Edit Statement Parameters" onclick="javascript:editfinancials('+cl+','+cn+')" >'; 
		jQuery("#mfinancialslist").setRowData(ids[i],{act:be});
	} 
}
LD3COMPLETE;

$grid->setGridEvent("loadComplete",$ld3event);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#mfinancialslist','#mfinancialspager',true, null, null, true,true);

?>




