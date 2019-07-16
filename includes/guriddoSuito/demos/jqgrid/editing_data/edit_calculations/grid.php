<?php
require_once '../../jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = 'SELECT account_id, name, debit, credit, balance from accounts';
// Set the table to where you update the data
$grid->table = 'accounts';
// Set output format to json
$grid->dataType = 'json';
$grid->setPrimaryKeyId('account_id');
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "rowList"=>array(10,20,30),
    "sortname"=>"account_id"
));
$grid->setColProperty('account_id', array("editoptions"=>array("readonly"=>true)));
$grid->setColProperty('balance', array("editable"=>false));
// Enable navigator
$grid->navigator = true;
// Enable only editing
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>true,"del"=>false,"view"=>false, "search"=>false));
// Close the dialog after editing
$grid->setNavOptions('edit',array("closeAfterEdit"=>true,"editCaption"=>"Update Account","bSubmit"=>"Update"));
// calculate the balance field
$calcs = <<< CALCS
function(postdata) {
	postdata['balance'] = parseFloat(postdata['debit']) - parseFloat(postdata['credit']);
	return postdata;
}
CALCS;
$grid->setNavEvent("edit", "serializeEditData", $calcs);
// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);
$conn = null;
?>
