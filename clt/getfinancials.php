<?php
session_start();
ini_set('display_errors', true);
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];

$aid = 'infinint_infin8';
$sid = 'infinint_sub'.$subid;
$id = $_SESSION["s_memberid"];


$db->closeDB();

$cltdb = $_SESSION['s_cltdb'];

include 'jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// enable debugging
//$grid->debug = true;


// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select client_company_xref.uid, client_company_xref.company_id,companies.coyname,if(client_company_xref.drno > 0,'Debtor','Creditor') as relationship,client_company_xref.sendstatement,client_company_xref.email,client_company_xref.billing,client_company_xref.priceband, (client_company_xref.current+client_company_xref.d30+client_company_xref.d60+client_company_xref.d90+client_company_xref.d120) as balance,client_company_xref.blocked from ".$sid.".client_company_xref,".$aid.".companies where (client_company_xref.company_id = companies.coyid) and (client_company_xref.sendstatement <> '') and (client_company_xref.client_id = ".$id.")"; 

//$grid->SelectCommand = "select uid,company_id,c.coyname,if(drno > 0,'Debtor','Creditor') as relationship,sendstatement,m.comm as email,concat(a.street_no,', ',a.ad1,', ',a.ad2,', ',a.suburb,', ',a.town) as postal,(current+d30+d60+d120) as balance,blocked from ".$sid.".client_company_xref inner join ".$aid.".companies as c on company_id = c.coyid inner join ".$sid.".comms as m on client_id = m.member_id inner join ".$sid.".addresses a on client_id = a.member_id where (sendstatement <> '') and (client_id = 4) and m.billing = 'Y' and a.billing = 'Y'";


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
$grid->setColProperty("coyname", array("label"=>"Company", "width"=>150));
$grid->setColProperty("relationship", array("label"=>"Relationship", "width"=>70));
$grid->setColProperty("sendstatement", array("label"=>"Statement By", "width"=>90));
$grid->setColProperty("email", array("label"=>"Email", "width"=>80 ));
$grid->setColProperty("postal", array("label"=>"Billing", "width"=>80));
$grid->setColProperty("priceband", array("label"=>"Priceband", "width"=>70));
$grid->setColProperty("balance", array("label"=>"Balance", "width"=>90, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("blocked", array("label"=>"Blocked", "width"=>50));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>40));

$ld3event = <<<LD3COMPLETE
function(rowid){
	var ids = jQuery("#mfinancialslist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowd = $("#mfinancialslist").getRowData(ids[i]);
		be = '<img src="../images/edit.png" title="Edit Statement Parameters" onclick="javascript:editfinancials('+cl+')" >'; 
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




