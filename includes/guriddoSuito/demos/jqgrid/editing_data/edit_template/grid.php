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
$grid->SelectCommand = 'SELECT CustomerID, CompanyName, Phone, PostalCode, City FROM customers';
// Set the table to where you update the data
$grid->table = 'customers';
// Set output format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "rowList"=>array(10,20,30),
    "sortname"=>"CustomerID"
));
$grid->setColProperty('CustomerID', array("editoptions"=>array("readonly"=>true)));
// Enable navigator
$grid->navigator = true;
// Enable only editing
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>true,"del"=>false,"view"=>false, "search"=>false));
// Close the dialog after editing
$template = "<div style='margin-left:15px;'><div> Customer ID <sup>*</sup>:</div><div> {CustomerID} </div>";
$template .= "<div> Company Name: </div><div>{CompanyName} </div>";
$template .= "<div> Phone: </div><div>{Phone} </div>";
$template .= "<div> Postal Code: </div><div>{PostalCode} </div>";
$template .= "<div> City:</div><div> {City} </div>";
$template .= "<hr style='width:100%;'/>";
$template .= "<div> {sData} {cData}  </div></div>";
$grid->setNavOptions('edit',array(
	"closeAfterEdit"=>true,
	"height"=>"auto",
	"dataheight"=>"auto",
	"template"=>$template,
	"editCaption"=>"Update Customer",
	"bSubmit"=>"Update"
));

// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);

?>
